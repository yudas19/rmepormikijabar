<?php

use App\Concerns\CanImportExportCsv;
use App\Models\Poli;
use Flux\Flux;
use Livewire\Component;
use Livewire\WithPagination;

new class extends Component
{
    use CanImportExportCsv;
    use WithPagination;

    protected function getModelClass()
    {
        return Poli::class;
    }

    protected function getExportColumns()
    {
        return [
            'Kode Poli' => 'kode_poli',
            'Nama Poli' => 'nama_poli',
            'Kode Poli BPJS' => 'kode_poli_bpjs',
            'Satu Sehat Location ID' => 'satu_sehat_location_id',
            'Status Aktif' => 'is_active',
        ];
    }

    protected function getUniqueKeys()
    {
        return ['kode_poli'];
    }

    public $search = '';

    public $sortField = 'nama_poli';

    public $sortDirection = 'asc';

    // Form fields
    public $selectedId = null;

    public $kode_poli = '';

    public $nama_poli = '';

    public $kode_poli_bpjs = '';

    public $satu_sehat_location_id = '';

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
        $record = Poli::findOrFail($id);

        $this->kode_poli = $record->kode_poli;
        $this->nama_poli = $record->nama_poli;
        $this->kode_poli_bpjs = $record->kode_poli_bpjs;
        $this->satu_sehat_location_id = $record->satu_sehat_location_id;
        $this->is_active = (bool) $record->is_active;
    }

    public function resetForm()
    {
        $this->selectedId = null;
        $this->kode_poli = '';
        $this->nama_poli = '';
        $this->kode_poli_bpjs = '';
        $this->satu_sehat_location_id = '';
        $this->is_active = true;
        $this->resetErrorBag();
    }

    public function save()
    {
        $rules = [
            'kode_poli' => 'required|string|max:10|unique:master_polis,kode_poli,'.($this->selectedId ?? 'NULL').',id',
            'nama_poli' => 'required|string|max:50',
            'kode_poli_bpjs' => 'nullable|string|max:10',
            'satu_sehat_location_id' => 'nullable|string|max:255',
            'is_active' => 'required|boolean',
        ];

        $validated = $this->validate($rules);

        if ($this->selectedId) {
            $record = Poli::findOrFail($this->selectedId);
            $record->update($validated);
            $message = 'Poliklinik berhasil diperbarui.';
        } else {
            Poli::create($validated);
            $message = 'Poliklinik berhasil ditambahkan.';
        }

        Flux::toast(variant: 'success', text: $message);
        $this->resetForm();
    }

    public function delete($id)
    {
        $record = Poli::findOrFail($id);
        $record->delete();
        Flux::toast(variant: 'success', text: 'Poliklinik berhasil dihapus.');
        if ($this->selectedId === $id) {
            $this->resetForm();
        }
    }

    public function syncLocation()
    {
        $this->satu_sehat_location_id = 'LOC-'.rand(10000000, 99999999);
        Flux::toast(variant: 'success', text: 'Location ID berhasil disinkronkan dari SatuSehat.');
    }

    public function render()
    {
        $data = Poli::query()
            ->when($this->search, function ($query) {
                $query->where('nama_poli', 'like', '%'.$this->search.'%')
                    ->orWhere('kode_poli', 'like', '%'.$this->search.'%')
                    ->orWhere('kode_poli_bpjs', 'like', '%'.$this->search.'%');
            })
            ->orderBy($this->sortField, $this->sortDirection)
            ->paginate(10);

        return view('components.master.⚡poli.poli', [
            'polis' => $data,
        ]);
    }
};
