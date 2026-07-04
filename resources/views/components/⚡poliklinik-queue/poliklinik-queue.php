<?php

use App\Models\MedicalRecord;
use Flux\Flux;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

new class extends Component
{
    public ?int $selectedPoliId = null;

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

    public function panggilAntrean(int $id): void
    {
        $record = MedicalRecord::findOrFail($id);
        $record->update(['status_panggilan' => 'memanggil']);
        Flux::toast(variant: 'success', text: 'Memanggil nomor antrean '.$record->nomor_antrean.'.');
    }

    public function periksaPasien(int $id)
    {
        $record = MedicalRecord::findOrFail($id);
        return $this->redirect(route('medical-record.examine', [
            'poliklinik' => $record->poliklinik_type,
            'encounter_id' => $record->encounter_id
        ]), navigate: true);
    }

    public function render()
    {
        $polis = \App\Models\Poli::where('jenis_unit', 'medis')->where('is_active', true)->get();
        $currentPoli = $polis->firstWhere('id', $this->selectedPoliId);

        $petugas = Auth::check() ? \App\Models\MasterPetugas::where('user_id', Auth::id())->first() : null;
        $cakupan = $petugas?->cakupan_antrean ?? 'hanya_poli_terpilih';
        $currentPetugasId = $petugas?->id;

        // Fetch medical records for the selected polyclinic
        $queues = MedicalRecord::with(['pasien', 'pendaftaran.dokter', 'poli'])
            ->when($cakupan === 'hanya_poli_terpilih' && $this->selectedPoliId, function ($q) {
                $q->where('poli_id', $this->selectedPoliId);
            })
            ->when($cakupan === 'hanya_dokter_bersangkutan', function ($q) use ($currentPetugasId) {
                if ($this->selectedPoliId) {
                    $q->where('poli_id', $this->selectedPoliId);
                }
                if ($currentPetugasId) {
                    $q->where('dokter_id', $currentPetugasId);
                }
            })
            ->whereDate('tanggal_kunjungan', '>=', $this->filterStartDate)
            ->whereDate('tanggal_kunjungan', '<=', $this->filterEndDate)
            ->where('status', '!=', 'batal')
            ->orderBy('status', 'asc')
            ->orderBy('id', 'asc')
            ->get();

        /** @var mixed $view */
        $view = view('components.⚡poliklinik-queue.poliklinik-queue', [
            'queues' => $queues,
            'polis' => $polis,
            'title' => $currentPoli ? $currentPoli->nama_poli : 'Pemeriksaan Medis',
        ]);

        return $view->layout('layouts::app');
    }
};

