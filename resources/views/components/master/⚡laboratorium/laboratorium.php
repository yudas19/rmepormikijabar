<?php

use App\Concerns\CanImportExportCsv;
use App\Models\MasterLabTest;
use Flux\Flux;
use Livewire\Component;
use Livewire\WithPagination;

new class extends Component
{
    use CanImportExportCsv;
    use WithPagination;

    protected function getModelClass()
    {
        return MasterLabTest::class;
    }

    protected function getExportColumns()
    {
        return [
            'id' => 'id',
            'test_name' => 'test_name',
            'tarif_bpjs' => 'tarif_bpjs',
            'tarif_umum' => 'tarif_umum',
            'category' => 'category',
            'default_normal_range' => 'default_normal_range',
            'default_unit' => 'default_unit',
            'is_active' => 'is_active',
        ];
    }

    protected function getUniqueKeys()
    {
        return ['test_name'];
    }

    public $search = '';

    public $sortField = 'test_name';

    public $sortDirection = 'asc';

    // Form fields
    public $selectedLabTestId = null;

    public $test_name = '';

    public $tarif_bpjs = 0;

    public $tarif_umum = 0;

    public $category = '';

    public $default_normal_range = '';

    public $default_unit = '';

    public $is_active = true;

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function sortBy(string $field)
    {
        if ($this->sortField === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortField = $field;
            $this->sortDirection = 'asc';
        }
    }

    public function edit(int $id)
    {
        $this->resetForm();
        $this->selectedLabTestId = $id;
        $record = MasterLabTest::findOrFail($id);
        $this->test_name = $record->test_name;
        $this->tarif_bpjs = $record->tarif_bpjs;
        $this->tarif_umum = $record->tarif_umum;
        $this->category = $record->category;
        $this->default_normal_range = $record->default_normal_range;
        $this->default_unit = $record->default_unit;
        $this->is_active = (bool) $record->is_active;
    }

    public function resetForm()
    {
        $this->selectedLabTestId = null;
        $this->test_name = '';
        $this->tarif_bpjs = 0;
        $this->tarif_umum = 0;
        $this->category = '';
        $this->default_normal_range = '';
        $this->default_unit = '';
        $this->is_active = true;
        $this->resetErrorBag();
    }

    public function save()
    {
        $rules = [
            'test_name' => 'required|string|max:100',
            'tarif_bpjs' => 'required|numeric|min:0',
            'tarif_umum' => 'required|numeric|min:0',
            'category' => 'required|string|max:50',
            'default_normal_range' => 'required|string|max:100',
            'default_unit' => 'required|string|max:50',
            'is_active' => 'required|boolean',
        ];

        $validated = $this->validate($rules);

        if ($this->selectedLabTestId) {
            $record = MasterLabTest::findOrFail($this->selectedLabTestId);
            $record->update($validated);
            $message = 'Layanan laboratorium berhasil diperbarui.';
        } else {
            MasterLabTest::create($validated);
            $message = 'Layanan laboratorium berhasil ditambahkan.';
        }

        Flux::toast(variant: 'success', text: $message);
        $this->resetForm();
    }

    public function delete(int $id)
    {
        $record = MasterLabTest::findOrFail($id);
        $record->delete();
        Flux::toast(variant: 'success', text: 'Layanan laboratorium berhasil dihapus.');
        if ($this->selectedLabTestId === $id) {
            $this->resetForm();
        }
    }

    public function render()
    {
        $data = MasterLabTest::query()
            ->when($this->search, function ($query) {
                $query->where('test_name', 'like', '%'.$this->search.'%')
                    ->orWhere('default_unit', 'like', '%'.$this->search.'%');
            })
            ->orderBy($this->sortField, $this->sortDirection)
            ->paginate(10);

        return view('components.master.⚡laboratorium.laboratorium', [
            'labs' => $data,
        ]);
    }
};
