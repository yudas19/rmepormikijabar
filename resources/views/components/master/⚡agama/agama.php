<?php

use App\Concerns\CanImportExportCsv;
use App\Models\MasterAgama;
use Flux\Flux;
use Livewire\Component;
use Livewire\WithPagination;

new class extends Component
{
    use CanImportExportCsv;
    use WithPagination;

    protected function getModelClass()
    {
        return MasterAgama::class;
    }

    protected function getExportColumns()
    {
        return [
            'Nama Agama' => 'nama_agama',
            'Status Aktif' => 'is_active',
        ];
    }

    protected function getUniqueKeys()
    {
        return ['nama_agama'];
    }

    public $search = '';

    public $sortField = 'nama_agama';

    public $sortDirection = 'asc';

    // Form fields
    public $selectedId = null;

    public $nama_agama = '';

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
        $record = MasterAgama::findOrFail($id);
        $this->nama_agama = $record->nama_agama;
        $this->is_active = (bool) $record->is_active;
    }

    public function resetForm()
    {
        $this->selectedId = null;
        $this->nama_agama = '';
        $this->is_active = true;
        $this->resetErrorBag();
    }

    public function save()
    {
        $rules = [
            'nama_agama' => 'required|string|max:100',
            'is_active' => 'required|boolean',
        ];

        $validated = $this->validate($rules);

        // Scope by active clinic ID
        $clinicId = \App\Models\FaskesProfile::first()->id ?? null;

        if ($this->selectedId) {
            $record = MasterAgama::findOrFail($this->selectedId);
            $record->update($validated);
            $message = 'Data agama berhasil diperbarui.';
        } else {
            $validated['clinic_id'] = $clinicId;
            MasterAgama::create($validated);
            $message = 'Data agama berhasil ditambahkan.';
        }

        Flux::toast(variant: 'success', text: $message);
        $this->resetForm();
    }

    public function delete($id)
    {
        $record = MasterAgama::findOrFail($id);
        $record->delete();
        Flux::toast(variant: 'success', text: 'Agama berhasil dihapus.');
        if ($this->selectedId === $id) {
            $this->resetForm();
        }
    }

    public function render()
    {
        $clinicId = \App\Models\FaskesProfile::first()->id ?? null;
        $data = MasterAgama::query()
            ->where(function ($q) use ($clinicId) {
                $q->whereNull('clinic_id')
                  ->orWhere('clinic_id', $clinicId);
            })
            ->when($this->search, function ($query) {
                $query->where('nama_agama', 'like', '%'.$this->search.'%');
            })
            ->orderBy($this->sortField, $this->sortDirection)
            ->paginate(10);

        return view('components.master.⚡agama.agama', [
            'agamas' => $data,
        ]);
    }
};
