<?php

use App\Concerns\CanImportExportCsv;
use App\Models\Provinsi;
use Flux\Flux;
use Livewire\Component;
use Livewire\WithPagination;

new class extends Component
{
    use CanImportExportCsv;
    use WithPagination;

    protected function getModelClass()
    {
        return Provinsi::class;
    }

    protected function getExportColumns()
    {
        return [
            'Kode Provinsi' => 'kode_provinsi',
            'Nama Provinsi' => 'nama_provinsi',
        ];
    }

    protected function getUniqueKeys()
    {
        return ['kode_provinsi'];
    }

    public $search = '';

    public $sortField = 'nama_provinsi';

    public $sortDirection = 'asc';

    // Form fields
    public $selectedId = null;

    public $kode_provinsi = '';

    public $nama_provinsi = '';

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
        $record = Provinsi::findOrFail($id);
        $this->kode_provinsi = $record->kode_provinsi;
        $this->nama_provinsi = $record->nama_provinsi;
    }

    public function resetForm()
    {
        $this->selectedId = null;
        $this->kode_provinsi = '';
        $this->nama_provinsi = '';
        $this->resetErrorBag();
    }

    public function save()
    {
        $rules = [
            'kode_provinsi' => 'required|string|max:50|unique:provinsis,kode_provinsi,'.($this->selectedId ?? 'NULL').',id',
            'nama_provinsi' => 'required|string|max:100',
        ];

        $validated = $this->validate($rules);

        if ($this->selectedId) {
            $record = Provinsi::findOrFail($this->selectedId);
            $record->update($validated);
            $message = 'Provinsi berhasil diperbarui.';
        } else {
            Provinsi::create($validated);
            $message = 'Provinsi berhasil ditambahkan.';
        }

        Flux::toast(variant: 'success', text: $message);
        $this->resetForm();
    }

    public function delete($id)
    {
        $record = Provinsi::findOrFail($id);
        $record->delete();
        Flux::toast(variant: 'success', text: 'Provinsi berhasil dihapus.');
        if ($this->selectedId === $id) {
            $this->resetForm();
        }
    }

    public function render()
    {
        $data = Provinsi::query()
            ->when($this->search, function ($query) {
                $query->where('nama_provinsi', 'like', '%'.$this->search.'%')
                    ->orWhere('kode_provinsi', 'like', '%'.$this->search.'%');
            })
            ->orderBy($this->sortField, $this->sortDirection)
            ->paginate(10);

        return view('components.master.⚡provinsi.provinsi', [
            'provinsis' => $data,
        ]);
    }
};
