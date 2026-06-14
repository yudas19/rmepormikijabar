<?php

use App\Models\MedicalRecord;
use Livewire\Component;

new class extends Component
{
    public $activeCall = null;

    public function checkIncomingCall()
    {
        $calling = MedicalRecord::with(['pasien', 'poli', 'pendaftaran.dokter'])
            ->where('status_panggilan', 'memanggil')
            ->first();

        if ($calling) {
            $this->activeCall = [
                'id' => $calling->id,
                'nomor_antrean' => $calling->nomor_antrean,
                'poli_tujuan' => $calling->poliklinik_type === 'umum' ? 'Poli Umum' : ($calling->poliklinik_type === 'gigi' ? 'Poli Gigi' : 'Klinik KIA'),
                'nama_dokter' => $calling->pendaftaran->dokter->nama_petugas ?? $calling->dokter->nama_petugas ?? '-',
                'nama_pasien' => $calling->pasien->nama_pasien,
            ];
        }
    }

    public function markAsDoneCalling($id)
    {
        $record = MedicalRecord::find($id);
        if ($record && $record->status_panggilan === 'memanggil') {
            $record->update(['status_panggilan' => 'selesai']);
        }
        $this->activeCall = null;
    }

    public function render()
    {
        $today = today();

        $activeUmum = MedicalRecord::where('status_panggilan', 'selesai')
            ->whereHas('poli', function ($q) {
                $q->where('nama_poli', 'not like', '%gigi%')
                    ->where('nama_poli', 'not like', '%kia%')
                    ->where('nama_poli', 'not like', '%anak%')
                    ->where('nama_poli', 'not like', '%ibu%');
            })
            ->whereDate('tanggal_kunjungan', $today)
            ->orderBy('updated_at', 'desc')
            ->first();

        $activeGigi = MedicalRecord::where('status_panggilan', 'selesai')
            ->whereHas('poli', function ($q) {
                $q->where('nama_poli', 'like', '%gigi%');
            })
            ->whereDate('tanggal_kunjungan', $today)
            ->orderBy('updated_at', 'desc')
            ->first();

        $activeKia = MedicalRecord::where('status_panggilan', 'selesai')
            ->whereHas('poli', function ($q) {
                $q->where('nama_poli', 'like', '%kia%')
                    ->orWhere('nama_poli', 'like', '%anak%')
                    ->orWhere('nama_poli', 'like', '%ibu%');
            })
            ->whereDate('tanggal_kunjungan', $today)
            ->orderBy('updated_at', 'desc')
            ->first();

        $history = MedicalRecord::with(['pasien', 'poli', 'pendaftaran.dokter'])
            ->where('status_panggilan', 'selesai')
            ->whereDate('tanggal_kunjungan', $today)
            ->orderBy('updated_at', 'desc')
            ->take(5)
            ->get();

        return view('components.⚡display-antrean.display-antrean', [
            'activeUmum' => $activeUmum,
            'activeGigi' => $activeGigi,
            'activeKia' => $activeKia,
            'history' => $history,
        ])->layout('layouts::blank');
    }
};
