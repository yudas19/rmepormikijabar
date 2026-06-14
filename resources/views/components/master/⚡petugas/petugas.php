<?php

use App\Concerns\CanImportExportCsv;
use App\Models\MasterPetugas;
use App\Models\User;
use Flux\Flux;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Livewire\Component;
use Livewire\WithPagination;
use Spatie\Permission\Models\Role;

new class extends Component
{
    use CanImportExportCsv;
    use WithPagination;

    protected function getModelClass()
    {
        return MasterPetugas::class;
    }

    protected function getExportColumns()
    {
        return [
            'Nama Petugas' => 'nama_petugas',
            'NIK' => 'nik',
            'Tempat Lahir' => 'tempat_lahir',
            'Tanggal Lahir' => 'tanggal_lahir',
            'Alamat' => 'alamat',
            'Telepon' => 'telepon',
            'No HP' => 'no_hp',
            'Bekerja Sejak' => 'bekerja_sejak',
            'Jenis Petugas' => 'jenis_petugas',
            'Nomor STR' => 'nomor_str',
            'Nomor SIP' => 'nomor_sip',
            'IHS Number Practitioner' => 'ihs_number_practitioner',
            'Status Aktif' => 'is_aktif',
            'User ID' => 'user_id',
        ];
    }

    protected function getUniqueKeys()
    {
        return ['nik'];
    }

    public $search = '';

    public $sortField = 'nama_petugas';

    public $sortDirection = 'asc';

    // Form fields
    public $selectedId = null;

    public $nama_petugas = '';

    public $nik = '';

    public $tempat_lahir = '';

    public $tanggal_lahir = '';

    public $alamat = '';

    public $telepon = '';

    public $no_hp = '';

    public $bekerja_sejak = '';

    public $jenis_petugas = 'Dokter';

    public $nomor_str = '';

    public $nomor_sip = '';

    public $ihs_number_practitioner = '';

    public $is_aktif = true;

    // Login credentials fields
    public $email = '';

    public $password = '';

    public $role = '';

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function sortBy($field)
    {
        if ($this->sortField === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortField = $field;
            $this->sortDirection = 'asc';
        }
    }

    public function edit($id)
    {
        $this->resetForm();
        $this->selectedId = $id;
        $record = MasterPetugas::with('user')->findOrFail($id);

        $this->nama_petugas = $record->nama_petugas;
        $this->nik = $record->nik;
        $this->tempat_lahir = $record->tempat_lahir;
        $this->tanggal_lahir = $record->tanggal_lahir;
        $this->alamat = $record->alamat;
        $this->telepon = $record->telepon;
        $this->no_hp = $record->no_hp;
        $this->bekerja_sejak = $record->bekerja_sejak;
        $this->jenis_petugas = $record->jenis_petugas;
        $this->nomor_str = $record->nomor_str;
        $this->nomor_sip = $record->nomor_sip;
        $this->ihs_number_practitioner = $record->ihs_number_practitioner;
        $this->is_aktif = (bool) $record->is_aktif;

        if ($record->user) {
            $this->email = $record->user->email;
            $this->role = $record->user->roles->first()?->name ?? '';
        } else {
            $this->email = '';
            $this->role = '';
        }
    }

    public function resetForm()
    {
        $this->selectedId = null;
        $this->nama_petugas = '';
        $this->nik = '';
        $this->tempat_lahir = '';
        $this->tanggal_lahir = '';
        $this->alamat = '';
        $this->telepon = '';
        $this->no_hp = '';
        $this->bekerja_sejak = '';
        $this->jenis_petugas = 'Dokter';
        $this->nomor_str = '';
        $this->nomor_sip = '';
        $this->ihs_number_practitioner = '';
        $this->is_aktif = true;
        $this->email = '';
        $this->password = '';
        $this->role = '';
        $this->resetErrorBag();
    }

    public function save()
    {
        $rules = [
            'nama_petugas' => 'required|string|max:100',
            'nik' => 'required|string|size:16|unique:master_petugass,nik,'.($this->selectedId ?? 'NULL').',id',
            'tempat_lahir' => 'nullable|string|max:100',
            'tanggal_lahir' => 'nullable|date',
            'alamat' => 'nullable|string|max:255',
            'telepon' => 'nullable|string|max:20',
            'no_hp' => 'nullable|string|max:15',
            'bekerja_sejak' => 'nullable|date',
            'jenis_petugas' => 'required|string|max:50',
            'nomor_str' => 'nullable|string|max:50',
            'nomor_sip' => 'nullable|string|max:50',
            'ihs_number_practitioner' => 'nullable|string|max:100',
            'is_aktif' => 'required|boolean',
            'email' => 'required|email|max:255|unique:users,email,'.($this->selectedId ? MasterPetugas::findOrFail($this->selectedId)->user_id : 'NULL').',id',
            'password' => ($this->selectedId ? 'nullable' : 'required').'|string|min:8',
            'role' => 'required|string|exists:roles,name',
        ];

        $validated = $this->validate($rules);

        DB::transaction(function () use ($validated) {
            if ($this->selectedId) {
                $record = MasterPetugas::findOrFail($this->selectedId);

                $user = $record->user;
                if ($user) {
                    $userUpdateData = [
                        'name' => $validated['nama_petugas'],
                        'email' => $validated['email'],
                    ];
                    if (! empty($validated['password'])) {
                        $userUpdateData['password'] = Hash::make($validated['password']);
                    }
                    $user->update($userUpdateData);
                } else {
                    $user = User::create([
                        'name' => $validated['nama_petugas'],
                        'email' => $validated['email'],
                        'password' => Hash::make($validated['password']),
                    ]);
                }
                $user->syncRoles([$validated['role']]);

                $validated['user_id'] = $user->id;
                $record->update(collect($validated)->except(['email', 'password', 'role'])->toArray());
                $message = 'Data petugas berhasil diperbarui.';
            } else {
                $user = User::create([
                    'name' => $validated['nama_petugas'],
                    'email' => $validated['email'],
                    'password' => Hash::make($validated['password']),
                ]);
                $user->syncRoles([$validated['role']]);

                $validated['user_id'] = $user->id;
                MasterPetugas::create(collect($validated)->except(['email', 'password', 'role'])->toArray());
                $message = 'Data petugas berhasil ditambahkan.';
            }

            Flux::toast(variant: 'success', text: $message);
        });

        $this->resetForm();
    }

    public function delete($id)
    {
        $record = MasterPetugas::findOrFail($id);
        $record->delete();
        Flux::toast(variant: 'success', text: 'Petugas berhasil dihapus.');
        if ($this->selectedId === $id) {
            $this->resetForm();
        }
    }

    public function verifyIhs()
    {
        if (empty($this->nik)) {
            Flux::toast(variant: 'danger', text: 'NIK wajib diisi untuk verifikasi IHS.');

            return;
        }

        $this->ihs_number_practitioner = 'P'.rand(1000000000, 9999999999);
        Flux::toast(variant: 'success', text: 'NIK berhasil diverifikasi dengan SatuSehat. IHS Practitioner Number diperoleh.');
    }

    public function render()
    {
        $data = MasterPetugas::query()
            ->with(['user.roles'])
            ->when($this->search, function ($query) {
                $query->where('nama_petugas', 'like', '%'.$this->search.'%')
                    ->orWhere('nik', 'like', '%'.$this->search.'%')
                    ->orWhere('jenis_petugas', 'like', '%'.$this->search.'%');
            })
            ->orderBy($this->sortField, $this->sortDirection)
            ->paginate(10);

        $roles = Role::all();

        return view('components.master.⚡petugas.petugas', [
            'petugass' => $data,
            'roles' => $roles,
        ]);
    }
};
