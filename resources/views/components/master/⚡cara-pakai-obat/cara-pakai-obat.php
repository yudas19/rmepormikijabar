<?php

use App\Concerns\CanImportExportCsv;
use App\Models\MasterAturanPakai;
use Flux\Flux;
use Livewire\Component;
use Livewire\WithPagination;

new class extends Component
{
    use CanImportExportCsv;
    use WithPagination;

    protected function getModelClass()
    {
        return MasterAturanPakai::class;
    }

    protected function getExportColumns()
    {
        return [
            'Nama Aturan Pakai' => 'nama_aturan_pakai',
            'Status Aktif' => 'is_active',
        ];
    }

    protected function getUniqueKeys()
    {
        return ['nama_aturan_pakai'];
    }

    public $search = '';

    public $sortField = 'nama_aturan_pakai';

    public $sortDirection = 'asc';

    // Form fields
    public $selectedId = null;

    public $nama_aturan_pakai = '';

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
        $record = MasterAturanPakai::findOrFail($id);
        $this->nama_aturan_pakai = $record->nama_aturan_pakai;
        $this->is_active = (bool) $record->is_active;
    }

    public function resetForm()
    {
        $this->selectedId = null;
        $this->nama_aturan_pakai = '';
        $this->is_active = true;
        $this->resetErrorBag();
    }

    public function save()
    {
        $rules = [
            'nama_aturan_pakai' => 'required|string|max:100',
            'is_active' => 'required|boolean',
        ];

        $validated = $this->validate($rules);

        if ($this->selectedId) {
            $record = MasterAturanPakai::findOrFail($this->selectedId);
            $record->update($validated);
            $message = 'Aturan pakai obat berhasil diperbarui.';
        } else {
            MasterAturanPakai::create($validated);
            $message = 'Aturan pakai obat berhasil ditambahkan.';
        }

        Flux::toast(variant: 'success', text: $message);
        $this->resetForm();
    }

    public function delete($id)
    {
        $record = MasterAturanPakai::findOrFail($id);
        $record->delete();
        Flux::toast(variant: 'success', text: 'Aturan pakai obat berhasil dihapus.');
        if ($this->selectedId === $id) {
            $this->resetForm();
        }
    }

    public function render()
    {
        $data = MasterAturanPakai::query()
            ->when($this->search, function ($query) {
                $query->where('nama_aturan_pakai', 'like', '%'.$this->search.'%');
            })
            ->orderBy($this->sortField, $this->sortDirection)
            ->paginate(10);

        return view('components.master.⚡cara-pakai-obat.cara-pakai-obat', [
            'aturanPakais' => $data,
        ]);
    }
};
