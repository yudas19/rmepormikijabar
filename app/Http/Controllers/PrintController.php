<?php

namespace App\Http\Controllers;

use App\Models\FaskesProfile;
use App\Models\MedicalRecord;
use App\Models\MedicalRecordPrescription;
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

        $filename = 'consent_'.str_replace(['/', '\\'], '_', $consent->no_surat).'.pdf';

        return $pdf->stream($filename);
    }

    public function printReferral($id)
    {
        $referral = SuratRujukan::with(['pendaftaran.pasien', 'dokter'])->findOrFail($id);

        $pdf = Pdf::loadView('print.referral', compact('referral'));

        $filename = 'referral_'.str_replace(['/', '\\'], '_', $referral->no_surat).'.pdf';

        return $pdf->stream($filename);
    }

    public function printCertificate($id)
    {
        $certificate = SuratKeterangan::with(['pasien', 'dokter'])->findOrFail($id);
        $profile = FaskesProfile::find(1);

        $pdf = Pdf::loadView('print.certificate', compact('certificate', 'profile'));

        $filename = 'certificate_'.str_replace(['/', '\\'], '_', $certificate->no_surat).'.pdf';

        return $pdf->stream($filename);
    }

    public function printQueueTicket($id)
    {
        $record = MedicalRecord::with(['pasien', 'pendaftaran'])->findOrFail($id);

        return view('print.queue-ticket', compact('record'));
    }

    public function printPrescription($id)
    {
        $prescription = MedicalRecordPrescription::with([
            'medicalRecord.pasien',
            'medicalRecord.dokter',
            'items.requestedObat',
            'items.dispensedObat',
            'metodeRacik',
            'apoteker',
        ])->findOrFail($id);

        $profile = FaskesProfile::find(1);

        return view('print.resep', compact('prescription', 'profile'));
    }
}
