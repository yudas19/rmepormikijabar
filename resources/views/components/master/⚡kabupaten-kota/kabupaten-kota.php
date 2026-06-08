<?php

use App\Concerns\CanImportExportCsv;
use App\Models\KabupatenKota;
use Flux\Flux;
use Livewire\Component;
use Livewire\WithPagination;

new class extends Component
{
    use CanImportExportCsv;
    use WithPagination;

    protected function getModelClass()
    {
        return KabupatenKota::class;
    }

    protected function getExportColumns()
    {
        return [
            'Kode Kabupaten Kota' => 'kode_kabupaten_kota',
            'Nama Kabupaten Kota' => 'nama_kabupaten_kota',
        ];
    }

    protected function getUniqueKeys()
    {
        return ['kode_kabupaten_kota'];
    }

    public $search = '';

    public $sortField = 'nama_kabupaten_kota';

    public $sortDirection = 'asc';

    // Form fields
    public $selectedId = null;

    public $kode_kabupaten_kota = '';

    public $nama_kabupaten_kota = '';

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
        $record = KabupatenKota::findOrFail($id);
        $this->kode_kabupaten_kota = $record->kode_kabupaten_kota;
        $this->nama_kabupaten_kota = $record->nama_kabupaten_kota;
    }

    public function resetForm()
    {
        $this->selectedId = null;
        $this->kode_kabupaten_kota = '';
        $this->nama_kabupaten_kota = '';
        $this->resetErrorBag();
    }

    public function save()
    {
        $rules = [
            'kode_kabupaten_kota' => 'nullable|string|max:50|unique:kabupaten_kotas,kode_kabupaten_kota,'.($this->selectedId ?? 'NULL').',id',
            'nama_kabupaten_kota' => 'required|string|max:100',
        ];

        $validated = $this->validate($rules);

        if ($this->selectedId) {
            $record = KabupatenKota::findOrFail($this->selectedId);
            $record->update($validated);
            $message = 'Kabupaten/Kota berhasil diperbarui.';
        } else {
            KabupatenKota::create($validated);
            $message = 'Kabupaten/Kota berhasil ditambahkan.';
        }

        Flux::toast(variant: 'success', text: $message);
        $this->resetForm();
    }

    public function delete($id)
    {
        $record = KabupatenKota::findOrFail($id);
        $record->delete();
        Flux::toast(variant: 'success', text: 'Kabupaten/Kota berhasil dihapus.');
        if ($this->selectedId === $id) {
            $this->resetForm();
        }
    }

    public function render()
    {
        $data = KabupatenKota::query()
            ->when($this->search, function ($query) {
                $query->where('nama_kabupaten_kota', 'like', '%'.$this->search.'%')
                    ->orWhere('kode_kabupaten_kota', 'like', '%'.$this->search.'%');
            })
            ->orderBy($this->sortField, $this->sortDirection)
            ->paginate(10);

        return view('components.master.⚡kabupaten-kota.kabupaten-kota', [
            'kabupatenKotas' => $data,
        ]);
    }
};
