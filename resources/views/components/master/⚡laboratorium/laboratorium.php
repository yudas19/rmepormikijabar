<?php

use App\Concerns\CanImportExportCsv;
use App\Models\MasterLab;
use Flux\Flux;
use Livewire\Component;
use Livewire\WithPagination;

new class extends Component
{
    use CanImportExportCsv;
    use WithPagination;

    protected function getModelClass()
    {
        return MasterLab::class;
    }

    protected function getExportColumns()
    {
        return [
            'Nama Pemeriksaan' => 'nama_pemeriksaan',
            'Nilai Normal' => 'nilai_normal',
            'Satuan' => 'satuan',
            'Tarif' => 'tarif',
            'Status Aktif' => 'is_aktif',
        ];
    }

    protected function getUniqueKeys()
    {
        return ['nama_pemeriksaan'];
    }

    public $search = '';

    public $sortField = 'nama_pemeriksaan';

    public $sortDirection = 'asc';

    // Form fields
    public $selectedId = null;

    public $nama_pemeriksaan = '';

    public $nilai_normal = '';

    public $satuan = '';

    public $tarif = '';

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
        $record = MasterLab::findOrFail($id);
        $this->nama_pemeriksaan = $record->nama_pemeriksaan;
        $this->nilai_normal = $record->nilai_normal;
        $this->satuan = $record->satuan;
        $this->tarif = $record->tarif;
        $this->is_aktif = (bool) $record->is_aktif;
    }

    public function resetForm()
    {
        $this->selectedId = null;
        $this->nama_pemeriksaan = '';
        $this->nilai_normal = '';
        $this->satuan = '';
        $this->tarif = '';
        $this->is_aktif = true;
        $this->resetErrorBag();
    }

    public function save()
    {
        $rules = [
            'nama_pemeriksaan' => 'required|string|max:100',
            'nilai_normal' => 'required|string|max:100',
            'satuan' => 'required|string|max:50',
            'tarif' => 'required|numeric|min:0',
            'is_aktif' => 'required|boolean',
        ];

        $validated = $this->validate($rules);

        if ($this->selectedId) {
            $record = MasterLab::findOrFail($this->selectedId);
            $record->update($validated);
            $message = 'Layanan laboratorium berhasil diperbarui.';
        } else {
            MasterLab::create($validated);
            $message = 'Layanan laboratorium berhasil ditambahkan.';
        }

        Flux::toast(variant: 'success', text: $message);
        $this->resetForm();
    }

    public function delete($id)
    {
        $record = MasterLab::findOrFail($id);
        $record->delete();
        Flux::toast(variant: 'success', text: 'Layanan laboratorium berhasil dihapus.');
        if ($this->selectedId === $id) {
            $this->resetForm();
        }
    }

    public function render()
    {
        $data = MasterLab::query()
            ->when($this->search, function ($query) {
                $query->where('nama_pemeriksaan', 'like', '%'.$this->search.'%')
                    ->orWhere('satuan', 'like', '%'.$this->search.'%');
            })
            ->orderBy($this->sortField, $this->sortDirection)
            ->paginate(10);

        return view('components.master.⚡laboratorium.laboratorium', [
            'labs' => $data,
        ]);
    }
};
