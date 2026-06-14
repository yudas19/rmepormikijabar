<?php

use Flux\Flux;
use Livewire\Component;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

new class extends Component
{
    public ?int $selectedRoleId = null;

    public array $selectedPermissions = [];

    public function mount(): void
    {
        // Default to the first role if available
        $firstRole = Role::orderBy('id')->first();
        if ($firstRole) {
            $this->selectRole($firstRole->id);
        }
    }

    public function selectRole(int $roleId): void
    {
        $this->selectedRoleId = $roleId;
        $role = Role::findOrFail($roleId);

        // Fetch all permissions
        $allPermissions = Permission::pluck('name')->toArray();

        // Initialize selected permissions array based on Spatie mapping
        $this->selectedPermissions = [];
        foreach ($allPermissions as $permission) {
            $this->selectedPermissions[$permission] = $role->hasPermissionTo($permission);
        }
    }

    public function save(): void
    {
        // Authorize action
        if (! auth()->user()->can('akses_pengaturan_akses')) {
            abort(403, 'Anda tidak memiliki hak akses untuk tindakan ini.');
        }

        $role = Role::findOrFail($this->selectedRoleId);

        // Filter permissions that are set to true
        $permissionsToSync = array_keys(array_filter($this->selectedPermissions));

        // Sync Spatie permissions
        $role->syncPermissions($permissionsToSync);

        // Clear Spatie permission cache
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        Flux::toast(variant: 'success', text: "Hak akses untuk role '{$role->name}' berhasil diperbarui.");
    }

    public function render()
    {
        $roles = Role::orderBy('id')->get();

        // Categorized permissions for grouped rendering
        $permissionGroups = [
            'Layanan Klinis & Poli' => [
                'akses_pendaftaran' => 'Pendaftaran Pasien',
                'akses_rekam_medis' => 'Workspace Rekam Medis (Examine)',
                'akses_poli_umum' => 'Antrean Poli Umum',
                'akses_poli_gigi' => 'Antrean Poli Gigi',
                'akses_poli_kia' => 'Antrean Klinik KIA (Kesehatan Ibu & Anak)',
            ],
            'Penunjang Medis' => [
                'akses_laboratorium' => 'Pemeriksaan Laboratorium',
                'akses_farmasi' => 'Depo Farmasi & Dispensing',
            ],
            'Keuangan & Sistem' => [
                'akses_kasir' => 'Kasir & Billing Pembayaran',
                'akses_stock_opname' => 'Stock Opname Farmasi',
                'akses_pengaturan_akses' => 'Pengaturan Hak Akses (Access Control)',
            ],
        ];

        return view('components.admin.⚡hak-akses.hak-akses', [
            'roles' => $roles,
            'permissionGroups' => $permissionGroups,
            'activeRole' => Role::find($this->selectedRoleId),
        ])->layout('layouts::app');
    }
};
