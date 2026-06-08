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
            'Nama PCare' => 'nama_pcare',
            'Kode RS' => 'kode_rs',
            'Kode Wilayah' => 'kode_wilayah',
            'Kode Provinsi' => 'kode_provinsi',
            'Kode Kabupaten' => 'kode_kabupaten',
            'Kode Kecamatan' => 'kode_kecamatan',
            'Nama Provinsi' => 'nama_propinsi',
            'Nama Kabupaten' => 'nama_kabupaten',
            'Nama Kecamatan' => 'nama_kecamatan',
            'Alamat' => 'alamat',
            'Telepon' => 'telepon',
            'Email' => 'email',
            'Kode Faskes' => 'kode_faskes',
            'Nama Faskes' => 'nama_faskes',
            'Jenis Faskes' => 'jenis_faskes',
            'Tipe Faskes' => 'tipe_faskes',
            'Tipe Layanan' => 'tipe_layanan',
            'Is BPJS' => 'is_bpjs',
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
    public $selectedId = null;

    public $kode_pcare = '';

    public $nama_pcare = '';

    public $kode_rs = '';

    public $kode_wilayah = '';

    public $kode_provinsi = '';

    public $kode_kabupaten = '';

    public $kode_kecamatan = '';

    public $nama_propinsi = '';

    public $nama_kabupaten = '';

    public $nama_kecamatan = '';

    public $alamat = '';

    public $telepon = '';

    public $email = '';

    public $kode_faskes = '';

    public $nama_faskes = '';

    public $jenis_faskes = '';

    public $tipe_faskes = '';

    public $tipe_layanan = '';

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
        $this->selectedId = $id;
        $record = MasterPcare::findOrFail($id);

        $this->kode_pcare = $record->kode_pcare;
        $this->nama_pcare = $record->nama_pcare;
        $this->kode_rs = $record->kode_rs;
        $this->kode_wilayah = $record->kode_wilayah;
        $this->kode_provinsi = $record->kode_provinsi;
        $this->kode_kabupaten = $record->kode_kabupaten;
        $this->kode_kecamatan = $record->kode_kecamatan;
        $this->nama_propinsi = $record->nama_propinsi;
        $this->nama_kabupaten = $record->nama_kabupaten;
        $this->nama_kecamatan = $record->nama_kecamatan;
        $this->alamat = $record->alamat;
        $this->telepon = $record->telepon;
        $this->email = $record->email;
        $this->kode_faskes = $record->kode_faskes;
        $this->nama_faskes = $record->nama_faskes;
        $this->jenis_faskes = $record->jenis_faskes;
        $this->tipe_faskes = $record->tipe_faskes;
        $this->tipe_layanan = $record->tipe_layanan;
        $this->is_bpjs = (bool) $record->is_bpjs;
        $this->is_active = (bool) $record->is_active;
    }

    public function resetForm()
    {
        $this->selectedId = null;
        $this->kode_pcare = '';
        $this->nama_pcare = '';
        $this->kode_rs = '';
        $this->kode_wilayah = '';
        $this->kode_provinsi = '';
        $this->kode_kabupaten = '';
        $this->kode_kecamatan = '';
        $this->nama_propinsi = '';
        $this->nama_kabupaten = '';
        $this->nama_kecamatan = '';
        $this->alamat = '';
        $this->telepon = '';
        $this->email = '';
        $this->kode_faskes = '';
        $this->nama_faskes = '';
        $this->jenis_faskes = '';
        $this->tipe_faskes = '';
        $this->tipe_layanan = '';
        $this->is_bpjs = true;
        $this->is_active = true;
        $this->resetErrorBag();
    }

    public function save()
    {
        $rules = [
            'kode_pcare' => 'required|string|max:50',
            'nama_pcare' => 'required|string|max:100',
            'kode_rs' => 'nullable|string|max:50',
            'kode_wilayah' => 'nullable|string|max:50',
            'kode_provinsi' => 'nullable|string|max:50',
            'kode_kabupaten' => 'nullable|string|max:50',
            'kode_kecamatan' => 'nullable|string|max:50',
            'nama_propinsi' => 'nullable|string|max:100',
            'nama_kabupaten' => 'nullable|string|max:100',
            'nama_kecamatan' => 'nullable|string|max:100',
            'alamat' => 'nullable|string|max:255',
            'telepon' => 'nullable|string|max:50',
            'email' => 'nullable|email|max:100',
            'kode_faskes' => 'nullable|string|max:50',
            'nama_faskes' => 'nullable|string|max:100',
            'jenis_faskes' => 'nullable|string|max:50',
            'tipe_faskes' => 'nullable|string|max:50',
            'tipe_layanan' => 'nullable|string|max:50',
            'is_bpjs' => 'required|boolean',
            'is_active' => 'required|boolean',
        ];

        $validated = $this->validate($rules);

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
