<?php

use App\Concerns\CanImportExportCsv;
use App\Models\MasterPendidikan;
use Flux\Flux;
use Livewire\Component;
use Livewire\WithPagination;

new class extends Component
{
    use CanImportExportCsv;
    use WithPagination;

    protected function getModelClass()
    {
        return MasterPendidikan::class;
    }

    protected function getExportColumns()
    {
        return [
            'Nama Pendidikan' => 'nama_pendidikan',
            'Status Aktif' => 'is_active',
        ];
    }

    protected function getUniqueKeys()
    {
        return ['nama_pendidikan'];
    }

    public $search = '';

    public $sortField = 'nama_pendidikan';

    public $sortDirection = 'asc';

    // Form fields
    public $selectedId = null;

    public $nama_pendidikan = '';

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
        $record = MasterPendidikan::findOrFail($id);
        $this->nama_pendidikan = $record->nama_pendidikan;
        $this->is_active = (bool) $record->is_active;
    }

    public function resetForm()
    {
        $this->selectedId = null;
        $this->nama_pendidikan = '';
        $this->is_active = true;
        $this->resetErrorBag();
    }

    public function save()
    {
        $rules = [
            'nama_pendidikan' => 'required|string|max:100',
            'is_active' => 'required|boolean',
        ];

        $validated = $this->validate($rules);

        $clinicId = \App\Models\FaskesProfile::first()->id ?? null;

        if ($this->selectedId) {
            $record = MasterPendidikan::findOrFail($this->selectedId);
            $record->update($validated);
            $message = 'Data pendidikan berhasil diperbarui.';
        } else {
            $validated['clinic_id'] = $clinicId;
            MasterPendidikan::create($validated);
            $message = 'Data pendidikan berhasil ditambahkan.';
        }

        Flux::toast(variant: 'success', text: $message);
        $this->resetForm();
    }

    public function delete($id)
    {
        $record = MasterPendidikan::findOrFail($id);
        $record->delete();
        Flux::toast(variant: 'success', text: 'Pendidikan berhasil dihapus.');
        if ($this->selectedId === $id) {
            $this->resetForm();
        }
    }

    public function render()
    {
        $clinicId = \App\Models\FaskesProfile::first()->id ?? null;
        $data = MasterPendidikan::query()
            ->where(function ($q) use ($clinicId) {
                $q->whereNull('clinic_id')
                  ->orWhere('clinic_id', $clinicId);
            })
            ->when($this->search, function ($query) {
                $query->where('nama_pendidikan', 'like', '%'.$this->search.'%');
            })
            ->orderBy($this->sortField, $this->sortDirection)
            ->paginate(10);

        return view('components.master.⚡pendidikan.pendidikan', [
            'pendidikans' => $data,
        ]);
    }
};
