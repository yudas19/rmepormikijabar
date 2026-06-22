<?php

use App\Models\LabOrder;
use App\Models\MasterLabTest;
use Database\Seeders\MasterLabTestSeeder;
use Livewire\Component;

new class extends Component
{
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
        // Auto-seed if master_lab_tests is empty
        if (MasterLabTest::count() === 0) {
            app(MasterLabTestSeeder::class)->run();
        }

        $orders = LabOrder::with([
            'medicalRecord.pendaftaran.pasien',
            'medicalRecord.pendaftaran.poli',
            'requester',
            'results',
        ])
            ->whereHas('medicalRecord', function ($q) {
                $q->whereDate('tanggal_kunjungan', '>=', $this->filterStartDate)
                  ->whereDate('tanggal_kunjungan', '<=', $this->filterEndDate)
                  ->where('status', '!=', 'batal');
            })
            ->when($this->statusFilter, fn ($q) => $q->where('status', $this->statusFilter))
            ->when($this->searchQuery, function ($q) {
                $q->whereHas('medicalRecord.pendaftaran.pasien', function ($sub) {
                    $sub->where('nama_pasien', 'like', '%'.$this->searchQuery.'%')
                        ->orWhere('no_rekam_medis', 'like', '%'.$this->searchQuery.'%');
                });
            })
            ->latest()
            ->paginate(20);

        return view('components.layanan.⚡laboratorium.laboratorium', [
            'orders' => $orders,
        ])->layout('layouts::app');
    }
};
