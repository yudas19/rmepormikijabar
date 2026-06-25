<?php

use App\Models\MedicalRecord;
use Livewire\Component;

new class extends Component
{
    public $selectedPoliId;

    public $filterStartDate = '';

    public $filterEndDate = '';

    public function mount($poliklinik = null)
    {
        if ($poliklinik && ! in_array($poliklinik, ['umum', 'gigi', 'kia'])) {
            abort(404);
        }

        $this->filterStartDate = date('Y-m-d');
        $this->filterEndDate = date('Y-m-d');

        // Fetch all active medical/medis units
        $polis = \App\Models\Poli::where('jenis_unit', 'medis')->where('is_active', true)->get();

        if ($poliklinik) {
            // Support legacy routing (umum, gigi, kia)
            $matchedPoli = $polis->first(function ($p) use ($poliklinik) {
                if ($poliklinik === 'gigi') {
                    return stripos($p->nama_poli, 'gigi') !== false;
                }
                if ($poliklinik === 'kia') {
                    return stripos($p->nama_poli, 'kia') !== false || stripos($p->nama_poli, 'anak') !== false || stripos($p->nama_poli, 'ibu') !== false;
                }
                return stripos($p->nama_poli, 'umum') !== false;
            });

            if ($matchedPoli) {
                $this->selectedPoliId = $matchedPoli->id;
            }
        }

        if (!$this->selectedPoliId && $polis->isNotEmpty()) {
            $this->selectedPoliId = $polis->first()->id;
        }
    }

    public function panggilAntrean($id)
    {
        $record = MedicalRecord::findOrFail($id);
        $record->update(['status_panggilan' => 'memanggil']);
        Flux::toast(variant: 'success', text: 'Memanggil nomor antrean '.$record->nomor_antrean.'.');
    }

    public function render()
    {
        $polis = \App\Models\Poli::where('jenis_unit', 'medis')->where('is_active', true)->get();
        $currentPoli = $polis->firstWhere('id', $this->selectedPoliId);

        // Fetch medical records for the selected polyclinic
        $queues = MedicalRecord::with(['pasien', 'pendaftaran.dokter', 'poli'])
            ->when($this->selectedPoliId, function ($q) {
                $q->where('poli_id', $this->selectedPoliId);
            })
            ->whereDate('tanggal_kunjungan', '>=', $this->filterStartDate)
            ->whereDate('tanggal_kunjungan', '<=', $this->filterEndDate)
            ->where('status', '!=', 'batal')
            ->orderBy('status', 'asc')
            ->orderBy('id', 'asc')
            ->get();

        return view('components.⚡poliklinik-queue.poliklinik-queue', [
            'queues' => $queues,
            'polis' => $polis,
            'title' => $currentPoli ? $currentPoli->nama_poli : 'Pemeriksaan Medis',
        ])->layout('layouts::app');
    }
};
