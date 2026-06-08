<?php

use App\Concerns\CanImportExportCsv;
use App\Models\MasterObat;
use Flux\Flux;
use Livewire\Component;
use Livewire\WithPagination;

new class extends Component
{
    use CanImportExportCsv;
    use WithPagination;

    protected function getModelClass()
    {
        return MasterObat::class;
    }

    protected function getExportColumns()
    {
        return [
            'Nama Obat' => 'nama_obat',
            'Satuan' => 'satuan',
            'Stok' => 'stok',
            'Harga Beli' => 'harga_beli',
            'Harga Jual' => 'harga_jual',
            'Kode KFA' => 'kode_kfa',
            'Nama KFA' => 'nama_kfa',
            'Status Aktif' => 'is_aktif',
        ];
    }

    protected function getUniqueKeys()
    {
        return ['nama_obat'];
    }

    public $search = '';

    public $sortField = 'nama_obat';

    public $sortDirection = 'asc';

    // Form fields
    public $selectedId = null;

    public $nama_obat = '';

    public $satuan = '';

    public $stok = 0;

    public $harga_beli = 0;

    public $harga_jual = 0;

    public $kode_kfa = '';

    public $nama_kfa = '';

    public $is_aktif = true;

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
        $record = MasterObat::findOrFail($id);
        $this->nama_obat = $record->nama_obat;
        $this->satuan = $record->satuan;
        $this->stok = $record->stok;
        $this->harga_beli = $record->harga_beli;
        $this->harga_jual = $record->harga_jual;
        $this->kode_kfa = $record->kode_kfa;
        $this->nama_kfa = $record->nama_kfa;
        $this->is_aktif = (bool) $record->is_aktif;
    }

    public function resetForm()
    {
        $this->selectedId = null;
        $this->nama_obat = '';
        $this->satuan = '';
        $this->stok = 0;
        $this->harga_beli = 0;
        $this->harga_jual = 0;
        $this->kode_kfa = '';
        $this->nama_kfa = '';
        $this->is_aktif = true;
        $this->resetErrorBag();
    }

    public function save()
    {
        $rules = [
            'nama_obat' => 'required|string|max:100',
            'satuan' => 'required|string|max:50',
            'stok' => 'required|integer|min:0',
            'harga_beli' => 'required|numeric|min:0',
            'harga_jual' => 'required|numeric|min:0',
            'kode_kfa' => 'nullable|string|max:50',
            'nama_kfa' => 'nullable|string|max:255',
            'is_aktif' => 'required|boolean',
        ];

        $validated = $this->validate($rules);

        if ($this->selectedId) {
            $record = MasterObat::findOrFail($this->selectedId);
            $record->update($validated);
            $message = 'Data obat berhasil diperbarui.';
        } else {
            MasterObat::create($validated);
            $message = 'Data obat berhasil ditambahkan.';
        }

        Flux::toast(variant: 'success', text: $message);
        $this->resetForm();
    }

    public function delete($id)
    {
        $record = MasterObat::findOrFail($id);
        $record->delete();
        Flux::toast(variant: 'success', text: 'Obat berhasil dihapus.');
        if ($this->selectedId === $id) {
            $this->resetForm();
        }
    }

    public function render()
    {
        $data = MasterObat::query()
            ->when($this->search, function ($query) {
                $query->where('nama_obat', 'like', '%'.$this->search.'%')
                    ->orWhere('kode_kfa', 'like', '%'.$this->search.'%')
                    ->orWhere('nama_kfa', 'like', '%'.$this->search.'%');
            })
            ->orderBy($this->sortField, $this->sortDirection)
            ->paginate(10);

        return view('components.master.⚡obat.obat', [
            'obats' => $data,
        ]);
    }
};
