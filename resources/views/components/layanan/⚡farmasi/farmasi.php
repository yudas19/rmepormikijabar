<?php

use App\Models\MedicalRecordPrescription;
use Livewire\Component;
use Livewire\WithPagination;

new class extends Component
{
    use WithPagination;

    public string $statusFilter = '';

    public string $searchQuery = '';

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
