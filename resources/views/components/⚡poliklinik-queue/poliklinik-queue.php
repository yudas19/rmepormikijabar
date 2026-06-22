<?php

use App\Models\MedicalRecord;
use Livewire\Component;

new class extends Component
{
    public $poliklinik;

    public $filterStartDate = '';

    public $filterEndDate = '';

    public function mount($poliklinik)
    {
        if (! in_array($poliklinik, ['umum', 'gigi', 'kia'])) {
            abort(404);
        }

        abort_if(! auth()->user()->can('akses_poli_'.$poliklinik), 403, 'Akses ditolak: Anda tidak memiliki izin untuk melihat antrean Poliklinik ini.');

        $this->poliklinik = $poliklinik;
        $this->filterStartDate = date('Y-m-d');
        $this->filterEndDate = date('Y-m-d');
    }

    public function panggilAntrean($id)
    {
        $record = MedicalRecord::findOrFail($id);
        $record->update(['status_panggilan' => 'memanggil']);
        Flux::toast(variant: 'success', text: 'Memanggil nomor antrean '.$record->nomor_antrean.'.');
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
            ->whereDate('tanggal_kunjungan', '>=', $this->filterStartDate)
            ->whereDate('tanggal_kunjungan', '<=', $this->filterEndDate)
            ->where('status', '!=', 'batal')
            ->orderBy('status', 'asc') // Group active statuses first
            ->orderBy('id', 'asc')     // First registered first
            ->get();

        return view('components.⚡poliklinik-queue.poliklinik-queue', [
            'queues' => $queues,
            'title' => $namaPoli[$this->poliklinik],
        ])->layout('layouts::app');
    }
};
