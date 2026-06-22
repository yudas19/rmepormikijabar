<?php

use App\Models\MedicalLetter;
use App\Models\SuratPersetujuan;
use App\Models\SuratRujukan;
use Livewire\Component;
use Livewire\WithPagination;

new class extends Component
{
    use WithPagination;

    public string $activeTab = 'keterangan';

    public string $searchQuery = '';

    public string $dateFilter = '';

    public function updatingSearchQuery(): void
    {
        $this->resetPage();
    }

    public function updatingDateFilter(): void
    {
        $this->resetPage();
    }

    public function updatingActiveTab(): void
    {
        $this->resetPage();
        $this->searchQuery = '';
        $this->dateFilter = '';
    }

    public function resetFilters(): void
    {
        $this->searchQuery = '';
        $this->dateFilter = '';
        $this->resetPage();
    }

    public function render()
    {
        if ($this->activeTab === 'keterangan') {
            $letters = MedicalLetter::with(['pasien', 'dokter'])
                ->when($this->searchQuery, function ($query) {
                    $query->whereHas('pasien', function ($q) {
                        $q->where('nama_pasien', 'like', '%'.$this->searchQuery.'%')
                            ->orWhere('no_rekam_medis', 'like', '%'.$this->searchQuery.'%');
                    });
                })
                ->when($this->dateFilter, function ($query) {
                    $query->whereDate('created_at', $this->dateFilter);
                })
                ->latest()
                ->paginate(15);
        } elseif ($this->activeTab === 'persetujuan') {
            $letters = SuratPersetujuan::with(['pendaftaran.pasien', 'petugas'])
                ->when($this->searchQuery, function ($query) {
                    $query->whereHas('pendaftaran.pasien', function ($q) {
                        $q->where('nama_pasien', 'like', '%'.$this->searchQuery.'%')
                            ->orWhere('no_rekam_medis', 'like', '%'.$this->searchQuery.'%');
                    });
                })
                ->when($this->dateFilter, function ($query) {
                    $query->whereDate('created_at', $this->dateFilter);
                })
                ->latest()
                ->paginate(15);
        } else {
            $letters = SuratRujukan::with(['pasien', 'dokter'])
                ->when($this->searchQuery, function ($query) {
                    $query->whereHas('pasien', function ($q) {
                        $q->where('nama_pasien', 'like', '%'.$this->searchQuery.'%')
                            ->orWhere('no_rekam_medis', 'like', '%'.$this->searchQuery.'%');
                    });
                })
                ->when($this->dateFilter, function ($query) {
                    $query->whereDate('created_at', $this->dateFilter);
                })
                ->latest()
                ->paginate(15);
        }

        return view('components.admin.⚡daftar-surat.daftar-surat', [
            'letters' => $letters,
        ])->layout('layouts::app');
    }
};
