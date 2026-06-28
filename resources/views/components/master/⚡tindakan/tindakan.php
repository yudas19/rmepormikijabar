<?php

use App\Concerns\CanImportExportCsv;
use App\Models\MasterTindakan;
use Flux\Flux;
use Livewire\Component;
use Livewire\WithPagination;

new class extends Component
{
    use CanImportExportCsv;
    use WithPagination;

    protected function getModelClass()
    {
        return MasterTindakan::class;
    }

    protected function getExportColumns()
    {
        return [
            'Nama Tindakan' => 'nama_tindakan',
            'Tarif' => 'tarif',
            'Kode ICD 9' => 'kode_icd9',
            'Nama ICD 9' => 'nama_icd9',
            'Status Aktif' => 'is_aktif',
        ];
    }

    protected function getUniqueKeys()
    {
        return ['nama_tindakan'];
    }

    public $search = '';

    public $sortField = 'nama_tindakan';

    public $sortDirection = 'asc';

    // Form fields
    public $selectedId = null;

    public $nama_tindakan = '';

    public $tarif = '';

    public $kode_icd9 = '';

    public $nama_icd9 = '';

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
        $record = MasterTindakan::findOrFail($id);
        $this->nama_tindakan = $record->nama_tindakan;
        $this->tarif = $record->tarif_umum;
        $this->kode_icd9 = $record->kode_icd9;
        $this->nama_icd9 = $record->nama_icd9;
        $this->is_aktif = (bool) $record->is_aktif;
    }

    public function resetForm()
    {
        $this->selectedId = null;
        $this->nama_tindakan = '';
        $this->tarif = '';
        $this->kode_icd9 = '';
        $this->nama_icd9 = '';
        $this->is_aktif = true;
        $this->resetErrorBag();
    }

    public function save()
    {
        $rules = [
            'nama_tindakan' => 'required|string|max:100',
            'tarif' => 'required|numeric|min:0',
            'kode_icd9' => 'nullable|string|max:50',
            'nama_icd9' => 'nullable|string|max:255',
            'is_aktif' => 'required|boolean',
        ];

        $validated = $this->validate($rules);

        $dataToSave = [
            'nama_tindakan' => $validated['nama_tindakan'],
            'tarif_umum' => $validated['tarif'],
            'tarif_bpjs' => $validated['tarif'],
            'kode_icd9' => $validated['kode_icd9'],
            'nama_icd9' => $validated['nama_icd9'],
            'is_aktif' => $validated['is_aktif'],
        ];

        if ($this->selectedId) {
            $record = MasterTindakan::findOrFail($this->selectedId);
            $record->update($dataToSave);
            $message = 'Tindakan berhasil diperbarui.';
        } else {
            MasterTindakan::create($dataToSave);
            $message = 'Tindakan berhasil ditambahkan.';
        }

        Flux::toast(variant: 'success', text: $message);
        $this->resetForm();
    }

    public function delete($id)
    {
        $record = MasterTindakan::findOrFail($id);
        $record->delete();
        Flux::toast(variant: 'success', text: 'Tindakan berhasil dihapus.');
        if ($this->selectedId === $id) {
            $this->resetForm();
        }
    }

    public function render()
    {
        $data = MasterTindakan::query()
            ->when($this->search, function ($query) {
                $query->where('nama_tindakan', 'like', '%'.$this->search.'%')
                    ->orWhere('kode_icd9', 'like', '%'.$this->search.'%');
            })
            ->orderBy($this->sortField, $this->sortDirection)
            ->paginate(10);

        return view('components.master.⚡tindakan.tindakan', [
            'tindakans' => $data,
        ]);
    }
};
