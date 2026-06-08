<?php

use App\Models\MedicalRecordPrescription;
use Livewire\Component;

new class extends Component
{
    public function render()
    {
        // Fetch active prescriptions from medical records
        $prescriptions = MedicalRecordPrescription::with(['medicalRecord.pasien', 'items.masterObat', 'metodeRacik'])
            ->latest()
            ->take(20)
            ->get();

        return view('components.layanan.⚡farmasi.farmasi', [
            'prescriptions' => $prescriptions,
        ])->layout('layouts::app');
    }
};
