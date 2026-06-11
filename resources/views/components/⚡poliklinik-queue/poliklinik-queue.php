<?php

use App\Models\MedicalRecord;
use Livewire\Component;

new class extends Component
{
    public $poliklinik;

    public function mount($poliklinik)
    {
        if (! in_array($poliklinik, ['umum', 'gigi', 'kia'])) {
            abort(404);
        }
        $this->poliklinik = $poliklinik;
    }

    public function render()
    {
        $namaPoli = [
            'umum' => 'Poli Umum',
            'gigi' => 'Poli Gigi',
            'kia' => 'Klinik KIA',
        ];

        // Fetch medical records (encounters) for today for this polyclinic
        $queues = MedicalRecord::with(['pasien', 'pendaftaran.dokter', 'poli'])
            ->whereHas('poli', function ($q) {
                if ($this->poliklinik === 'gigi') {
                    $q->where('nama_poli', 'like', '%gigi%');
                } elseif ($this->poliklinik === 'kia') {
                    $q->where('nama_poli', 'like', '%kia%')
                        ->orWhere('nama_poli', 'like', '%anak%')
                        ->orWhere('nama_poli', 'like', '%ibu%');
                } else {
                    $q->where('nama_poli', 'not like', '%gigi%')
                        ->where('nama_poli', 'not like', '%kia%')
                        ->where('nama_poli', 'not like', '%anak%')
                        ->where('nama_poli', 'not like', '%ibu%');
                }
            })
            ->whereDate('created_at', today())
            ->orderBy('status', 'asc') // Group active statuses first
            ->orderBy('id', 'asc')     // First registered first
            ->get();

        return view('components.⚡poliklinik-queue.poliklinik-queue', [
            'queues' => $queues,
            'title' => $namaPoli[$this->poliklinik],
        ])->layout('layouts::app');
    }
};
