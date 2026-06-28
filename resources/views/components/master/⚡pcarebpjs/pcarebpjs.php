<?php

use App\Concerns\CanImportExportCsv;
use App\Models\MasterPcare;
use Flux\Flux;
use Livewire\Component;
use Livewire\WithPagination;

new class extends Component
{
    use CanImportExportCsv;
    use WithPagination;

    protected function getModelClass()
    {
        return MasterPcare::class;
    }

    protected function getExportColumns()
    {
        return [
            'Kode PCare' => 'kode_pcare',
            'Nama Konfigurasi' => 'nama_pcare',
            'Kode Faskes BPJS' => 'kode_faskes',
            'Nama Faskes BPJS' => 'nama_faskes',
            'Environment' => 'bpjs_env',
            'Is Active' => 'is_active',
        ];
    }

    protected function getUniqueKeys()
    {
        return ['kode_pcare'];
    }

    public $search = '';

    public $sortField = 'nama_pcare';

    public $sortDirection = 'asc';

    // Form fields
    public $selectedPcareId = null;

    public $kode_pcare = '';

    public $nama_pcare = '';

    public $kode_faskes = '';

    public $nama_faskes = '';

    public $bpjs_env = 'development';

    public $bpjs_cons_id = '';

    public $bpjs_secret_key = '';

    public $bpjs_user_key = '';

    public $pcare_username = '';

    public $pcare_password = '';

    public $user_mjkn = '';

    public $is_bpjs = true;

    public $is_active = true;

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
        $this->selectedPcareId = $id;
        $record = MasterPcare::findOrFail($id);

        $this->kode_pcare = $record->kode_pcare;
        $this->nama_pcare = $record->nama_pcare;
        $this->kode_faskes = $record->kode_faskes;
        $this->nama_faskes = $record->nama_faskes;
        $this->bpjs_env = $record->bpjs_env ?? 'development';
        $this->pcare_username = $record->pcare_username;
        $this->user_mjkn = $record->user_mjkn;
        $this->is_bpjs = (bool) $record->is_bpjs;
        $this->is_active = (bool) $record->is_active;

        // Decrypt values safely
        try {
            $this->bpjs_cons_id = $record->bpjs_cons_id ? decrypt($record->bpjs_cons_id) : '';
        } catch (\Exception $e) {
            $this->bpjs_cons_id = $record->bpjs_cons_id ?? '';
        }

        try {
            $this->bpjs_secret_key = $record->bpjs_secret_key ? decrypt($record->bpjs_secret_key) : '';
        } catch (\Exception $e) {
            $this->bpjs_secret_key = $record->bpjs_secret_key ?? '';
        }

        try {
            $this->bpjs_user_key = $record->bpjs_user_key ? decrypt($record->bpjs_user_key) : '';
        } catch (\Exception $e) {
            $this->bpjs_user_key = $record->bpjs_user_key ?? '';
        }

        try {
            $this->pcare_password = $record->pcare_password ? decrypt($record->pcare_password) : '';
        } catch (\Exception $e) {
            $this->pcare_password = $record->pcare_password ?? '';
        }
    }

    public function resetForm()
    {
        $this->selectedId = null;
        $this->kode_pcare = '';
        $this->nama_pcare = '';
        $this->kode_faskes = '';
        $this->nama_faskes = '';
        $this->bpjs_env = 'development';
        $this->bpjs_cons_id = '';
        $this->bpjs_secret_key = '';
        $this->bpjs_user_key = '';
        $this->pcare_username = '';
        $this->pcare_password = '';
        $this->user_mjkn = '';
        $this->is_bpjs = true;
        $this->is_active = true;
        $this->resetErrorBag();
    }

    public function save()
    {
        $rules = [
            'kode_pcare' => 'required|string|max:50',
            'nama_pcare' => 'required|string|max:100',
            'kode_faskes' => 'nullable|string|max:50',
            'nama_faskes' => 'nullable|string|max:100',
            'bpjs_env' => 'required|string|in:development,production',
            'bpjs_cons_id' => 'nullable|string|max:100',
            'bpjs_secret_key' => 'nullable|string|max:255',
            'bpjs_user_key' => 'nullable|string|max:255',
            'pcare_username' => 'nullable|string|max:100',
            'pcare_password' => 'nullable|string|max:255',
            'user_mjkn' => 'nullable|string|max:100',
            'is_bpjs' => 'required|boolean',
            'is_active' => 'required|boolean',
        ];

        $validated = $this->validate($rules);

        // Map legacy non-nullable database columns
        $validated['kode_rs'] = '-';
        $validated['kode_wilayah'] = '-';
        $validated['kode_provinsi'] = '-';
        $validated['kode_kabupaten'] = '-';
        $validated['kode_kecamatan'] = '-';
        $validated['nama_propinsi'] = '-';
        $validated['nama_kabupaten'] = '-';
        $validated['nama_kecamatan'] = '-';

        // Secure encryption for sensitive data
        if (!empty($validated['bpjs_cons_id'])) {
            $validated['bpjs_cons_id'] = encrypt($validated['bpjs_cons_id']);
        }
        if (!empty($validated['bpjs_secret_key'])) {
            $validated['bpjs_secret_key'] = encrypt($validated['bpjs_secret_key']);
        }
        if (!empty($validated['bpjs_user_key'])) {
            $validated['bpjs_user_key'] = encrypt($validated['bpjs_user_key']);
        }
        if (!empty($validated['pcare_password'])) {
            $validated['pcare_password'] = encrypt($validated['pcare_password']);
        }

        if ($this->selectedId) {
            $record = MasterPcare::findOrFail($this->selectedId);
            $record->update($validated);
            $message = 'Konfigurasi PCare BPJS berhasil diperbarui.';
        } else {
            MasterPcare::create($validated);
            $message = 'Konfigurasi PCare BPJS berhasil ditambahkan.';
        }

        Flux::toast(variant: 'success', text: $message);
        $this->resetForm();
    }

    public function delete($id)
    {
        $record = MasterPcare::findOrFail($id);
        $record->delete();
        Flux::toast(variant: 'success', text: 'Konfigurasi PCare BPJS berhasil dihapus.');
        if ($this->selectedId === $id) {
            $this->resetForm();
        }
    }

    public function render()
    {
        $data = MasterPcare::query()
            ->when($this->search, function ($query) {
                $query->where('nama_pcare', 'like', '%'.$this->search.'%')
                    ->orWhere('kode_pcare', 'like', '%'.$this->search.'%')
                    ->orWhere('kode_faskes', 'like', '%'.$this->search.'%');
            })
            ->orderBy($this->sortField, $this->sortDirection)
            ->paginate(10);

        return view('components.master.⚡pcarebpjs.pcarebpjs', [
            'pcares' => $data,
        ]);
    }
};
