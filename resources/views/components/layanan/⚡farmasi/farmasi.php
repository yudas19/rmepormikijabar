<?php

use App\Models\MedicalRecordPrescription;
use Livewire\Component;
use Livewire\WithPagination;

new class extends Component
{
    use WithPagination;

    public string $statusFilter = '';

    public string $searchQuery = '';

    public string $filterStartDate = '';

    public string $filterEndDate = '';

    public function mount(): void
    {
        $this->filterStartDate = date('Y-m-d');
        $this->filterEndDate = date('Y-m-d');
    }

    public function render()
    {
        $prescriptions = MedicalRecordPrescription::with([
            'medicalRecord.pendaftaran.pasien',
            'medicalRecord.pendaftaran.poli',
            'medicalRecord.pendaftaran.dokter',
            'items.requestedObat',
            'metodeRacik',
            'apoteker',
        ])
            ->whereHas('medicalRecord', function ($q) {
                $q->whereDate('tanggal_kunjungan', '>=', $this->filterStartDate)
                  ->whereDate('tanggal_kunjungan', '<=', $this->filterEndDate)
                  ->where('status', '!=', 'batal');
            })
            ->when($this->statusFilter, fn ($q) => $q->where('dispensing_status', $this->statusFilter))
            ->when($this->searchQuery, function ($q) {
                $q->whereHas('medicalRecord.pendaftaran.pasien', function ($sub) {
                    $sub->where('nama_pasien', 'like', '%'.$this->searchQuery.'%')
                        ->orWhere('no_rekam_medis', 'like', '%'.$this->searchQuery.'%');
                });
            })
            ->latest()
            ->paginate(20);

        return view('components.layanan.⚡farmasi.farmasi', [
            'prescriptions' => $prescriptions,
        ])->layout('layouts::app');
    }
};
