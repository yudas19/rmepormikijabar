<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class PermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        // Define default permissions
        $permissions = [
            'akses_pendaftaran',
            'akses_rekam_medis',
            'akses_poli_umum',
            'akses_poli_gigi',
            'akses_poli_kia',
            'akses_laboratorium',
            'akses_farmasi',
            'akses_kasir',
            'akses_stock_opname',
            'akses_pengaturan_akses',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission, 'guard_name' => 'web']);
        }

        // Define default permissions for each role
        $rolePermissions = [
            'admin' => $permissions, // all permissions
            'rekam_medis' => [
                'akses_pendaftaran',
                'akses_rekam_medis',
            ],
            'dokter_umum' => [
                'akses_rekam_medis',
                'akses_poli_umum',
            ],
            'dokter_gigi' => [
                'akses_rekam_medis',
                'akses_poli_gigi',
            ],
            'perawat' => [
                'akses_rekam_medis',
                'akses_poli_umum',
                'akses_poli_gigi',
                'akses_poli_kia',
            ],
            'bidan' => [
                'akses_rekam_medis',
                'akses_poli_kia',
            ],
            'analis_lab' => [
                'akses_laboratorium',
            ],
            'apoteker' => [
                'akses_farmasi',
                'akses_stock_opname',
            ],
            'kasir' => [
                'akses_kasir',
            ],
        ];

        foreach ($rolePermissions as $roleName => $perms) {
            $role = Role::firstOrCreate(['name' => $roleName, 'guard_name' => 'web']);
            $role->syncPermissions($perms);
        }
    }
}
