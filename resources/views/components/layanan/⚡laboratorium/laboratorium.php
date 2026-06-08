<?php

use App\Models\PermintaanLab;
use Livewire\Component;

new class extends Component
{
    public function render()
    {
        // Fetch active lab requests
        $requests = PermintaanLab::query()
            ->join('pendaftarans', 'permintaan_labs.pendaftaran_id', '=', 'pendaftarans.id')
            ->join('pasiens', 'pendaftarans.pasien_id', '=', 'pasiens.id')
            ->join('master_petugass', 'pendaftarans.dokter_id', '=', 'master_petugass.id')
            ->select(
                'permintaan_labs.*',
                'pasiens.nama_pasien',
                'pasiens.no_rekam_medis',
                'master_petugass.nama_petugas as nama_dokter'
            )
            ->latest()
            ->take(20)
            ->get();

        return view('components.layanan.⚡laboratorium.laboratorium', [
            'requests' => $requests,
        ])->layout('layouts::app');
    }
};
