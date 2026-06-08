<?php

namespace App\Http\Controllers;

use App\Models\MedicalRecord;
use App\Models\SuratKeterangan;
use App\Models\SuratPersetujuan;
use App\Models\SuratRujukan;
use Barryvdh\DomPDF\Facade\Pdf;

class PrintController extends Controller
{
    public function printConsent($id)
    {
        $consent = SuratPersetujuan::with(['pendaftaran.pasien', 'petugas'])->findOrFail($id);

        $pdf = Pdf::loadView('print.consent', compact('consent'));

        return $pdf->stream('consent_'.$consent->no_surat.'.pdf');
    }

    public function printReferral($id)
    {
        $referral = SuratRujukan::with(['pendaftaran.pasien', 'dokter'])->findOrFail($id);

        $pdf = Pdf::loadView('print.referral', compact('referral'));

        return $pdf->stream('referral_'.$referral->no_surat.'.pdf');
    }

    public function printCertificate($id)
    {
        $certificate = SuratKeterangan::with(['pasien', 'dokter'])->findOrFail($id);

        $pdf = Pdf::loadView('print.certificate', compact('certificate'));

        return $pdf->stream('certificate_'.$certificate->no_surat.'.pdf');
    }

    public function printQueueTicket($id)
    {
        $record = MedicalRecord::with(['pasien', 'pendaftaran'])->findOrFail($id);

        return view('print.queue-ticket', compact('record'));
    }
}
