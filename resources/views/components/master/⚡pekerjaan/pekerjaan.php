<?php

use App\Concerns\CanImportExportCsv;
use App\Models\MasterPekerjaan;
use Flux\Flux;
use Livewire\Component;
use Livewire\WithPagination;

new class extends Component
{
    use CanImportExportCsv;
    use WithPagination;

    protected function getModelClass()
    {
        return MasterPekerjaan::class;
    }

    protected function getExportColumns()
    {
        return [
            'Nama Pekerjaan' => 'nama_pekerjaan',
            'Status Aktif' => 'is_active',
        ];
    }

    protected function getUniqueKeys()
    {
        return ['nama_pekerjaan'];
    }

    public $search = '';

    public $sortField = 'nama_pekerjaan';

    public $sortDirection = 'asc';

    // Form fields
    public $selectedId = null;

    public $nama_pekerjaan = '';

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
        $record = MasterPekerjaan::findOrFail($id);
        $this->nama_pekerjaan = $record->nama_pekerjaan;
        $this->is_active = (bool) $record->is_active;
    }

    public function resetForm()
    {
        $this->selectedId = null;
        $this->nama_pekerjaan = '';
        $this->is_active = true;
        $this->resetErrorBag();
    }

    public function save()
    {
        $rules = [
            'nama_pekerjaan' => 'required|string|max:100',
            'is_active' => 'required|boolean',
        ];

        $validated = $this->validate($rules);

        $clinicId = \App\Models\FaskesProfile::first()->id ?? null;

        if ($this->selectedId) {
            $record = MasterPekerjaan::findOrFail($this->selectedId);
            $record->update($validated);
            $message = 'Data pekerjaan berhasil diperbarui.';
        } else {
            $validated['clinic_id'] = $clinicId;
            MasterPekerjaan::create($validated);
            $message = 'Data pekerjaan berhasil ditambahkan.';
        }

        Flux::toast(variant: 'success', text: $message);
        $this->resetForm();
    }

    public function delete($id)
    {
        $record = MasterPekerjaan::findOrFail($id);
        $record->delete();
        Flux::toast(variant: 'success', text: 'Pekerjaan berhasil dihapus.');
        if ($this->selectedId === $id) {
            $this->resetForm();
        }
    }

    public function render()
    {
        $clinicId = \App\Models\FaskesProfile::first()->id ?? null;
        $data = MasterPekerjaan::query()
            ->where(function ($q) use ($clinicId) {
                $q->whereNull('clinic_id')
                  ->orWhere('clinic_id', $clinicId);
            })
            ->when($this->search, function ($query) {
                $query->where('nama_pekerjaan', 'like', '%'.$this->search.'%');
            })
            ->orderBy($this->sortField, $this->sortDirection)
            ->paginate(10);

        return view('components.master.⚡pekerjaan.pekerjaan', [
            'pekerjaans' => $data,
        ]);
    }
};
