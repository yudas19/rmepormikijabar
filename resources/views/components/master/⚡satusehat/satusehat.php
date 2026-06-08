<?php

use App\Concerns\CanImportExportCsv;
use App\Models\MasterPoliSatusehat;
use App\Models\MasterSatusehatConfig;
use App\Models\Poli;
use Flux\Flux;
use Livewire\Component;
use Livewire\WithPagination;

new class extends Component
{
    use CanImportExportCsv;
    use WithPagination;

    public $activeTab = 'config';

    public $search = '';

    public $sortField = 'id';

    public $sortDirection = 'asc';

    // Form fields
    public $selectedId = null;

    // Config form fields
    public $environment = 'sandbox';

    public $client_id = '';

    public $client_secret = '';

    public $organization_id = '';

    public $is_active = true;

    // Poli mapping form fields
    public $master_poli_id = '';

    public $satusehat_location_id = '';

    protected function getModelClass()
    {
        return $this->activeTab === 'config' ? MasterSatusehatConfig::class : MasterPoliSatusehat::class;
    }

    protected function getExportColumns()
    {
        if ($this->activeTab === 'config') {
            return [
                'Environment' => 'environment',
                'Client ID' => 'client_id',
                'Client Secret' => 'client_secret',
                'Organization ID' => 'organization_id',
                'Is Active' => 'is_active',
            ];
        } else {
            return [
                'Master Poli ID' => 'master_poli_id',
                'SatuSehat Location ID' => 'satusehat_location_id',
            ];
        }
    }

    protected function getUniqueKeys()
    {
        if ($this->activeTab === 'config') {
            return ['client_id'];
        } else {
            return ['master_poli_id'];
        }
    }

    public function updatedActiveTab()
    {
        $this->resetForm();
        $this->resetPage();
        $this->search = '';
        $this->sortField = $this->activeTab === 'config' ? 'id' : 'master_polis.nama_poli';
        $this->sortDirection = 'asc';
    }

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

        if ($this->activeTab === 'config') {
            $record = MasterSatusehatConfig::findOrFail($id);
            $this->environment = $record->environment;
            $this->client_id = $record->client_id;
            $this->client_secret = $record->client_secret;
            $this->organization_id = $record->organization_id;
            $this->is_active = (bool) $record->is_active;
        } else {
            $record = MasterPoliSatusehat::findOrFail($id);
            $this->master_poli_id = $record->master_poli_id;
            $this->satusehat_location_id = $record->satusehat_location_id;
        }
    }

    public function resetForm()
    {
        $this->selectedId = null;

        // Reset config fields
        $this->environment = 'sandbox';
        $this->client_id = '';
        $this->client_secret = '';
        $this->organization_id = '';
        $this->is_active = true;

        // Reset poli fields
        $this->master_poli_id = '';
        $this->satusehat_location_id = '';

        $this->resetErrorBag();
    }

    public function save()
    {
        if ($this->activeTab === 'config') {
            $rules = [
                'environment' => 'required|string|max:20',
                'client_id' => 'required|string|max:100',
                'client_secret' => 'required|string|max:100',
                'organization_id' => 'required|string|max:50',
                'is_active' => 'required|boolean',
            ];

            $validated = $this->validate($rules);

            if ($this->selectedId) {
                $record = MasterSatusehatConfig::findOrFail($this->selectedId);
                $record->update($validated);
                $message = 'Konfigurasi SatuSehat berhasil diperbarui.';
            } else {
                MasterSatusehatConfig::create($validated);
                $message = 'Konfigurasi SatuSehat berhasil ditambahkan.';
            }
        } else {
            $rules = [
                'master_poli_id' => 'required|exists:master_polis,id',
                'satusehat_location_id' => 'required|string|max:50',
            ];

            $validated = $this->validate($rules);

            if ($this->selectedId) {
                $record = MasterPoliSatusehat::findOrFail($this->selectedId);
                $record->update($validated);
                $message = 'Pemetaan Poli SatuSehat berhasil diperbarui.';
            } else {
                MasterPoliSatusehat::create($validated);
                $message = 'Pemetaan Poli SatuSehat berhasil ditambahkan.';
            }
        }

        Flux::toast(variant: 'success', text: $message);
        $this->resetForm();
    }

    public function delete($id)
    {
        if ($this->activeTab === 'config') {
            $record = MasterSatusehatConfig::findOrFail($id);
            $record->delete();
            Flux::toast(variant: 'success', text: 'Konfigurasi SatuSehat berhasil dihapus.');
        } else {
            $record = MasterPoliSatusehat::findOrFail($id);
            $record->delete();
            Flux::toast(variant: 'success', text: 'Pemetaan Poli SatuSehat berhasil dihapus.');
        }

        if ($this->selectedId === $id) {
            $this->resetForm();
        }
    }

    public function render()
    {
        if ($this->activeTab === 'config') {
            $data = MasterSatusehatConfig::query()
                ->when($this->search, function ($query) {
                    $query->where('client_id', 'like', '%'.$this->search.'%')
                        ->orWhere('organization_id', 'like', '%'.$this->search.'%')
                        ->orWhere('environment', 'like', '%'.$this->search.'%');
                })
                ->orderBy($this->sortField === 'master_polis.nama_poli' ? 'id' : $this->sortField, $this->sortDirection)
                ->paginate(10);
        } else {
            $data = MasterPoliSatusehat::query()
                ->select('master_poli_satusehats.*')
                ->join('master_polis', 'master_poli_satusehats.master_poli_id', '=', 'master_polis.id')
                ->when($this->search, function ($query) {
                    $query->where('master_polis.nama_poli', 'like', '%'.$this->search.'%')
                        ->orWhere('master_polis.kode_poli', 'like', '%'.$this->search.'%')
                        ->orWhere('master_poli_satusehats.satusehat_location_id', 'like', '%'.$this->search.'%');
                })
                ->orderBy($this->sortField === 'id' ? 'master_poli_satusehats.id' : $this->sortField, $this->sortDirection)
                ->paginate(10);
        }

        return view('components.master.⚡satusehat.satusehat', [
            'satusehats' => $data,
            'polis' => Poli::where('is_active', true)->get(),
        ]);
    }
};
