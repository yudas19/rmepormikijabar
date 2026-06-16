<?php

namespace App\Http\Controllers;

use App\Models\FaskesProfile;
use App\Models\MedicalLetter;
use App\Models\MedicalRecordPrescription;
use App\Models\SuratKeterangan;
use Illuminate\View\View;

class VerificationController extends Controller
{
    /**
     * Verify the electronic signature/document validation.
     *
     * @param  string  $encrypted_id  Encrypted document identifier.
     * @return View
     */
    public function verify($encrypted_id)
    {
        try {
            $decrypted = decrypt($encrypted_id);

            $type = null;
            $id = null;

            if (is_array($decrypted)) {
                $type = $decrypted['type'] ?? null;
                $id = $decrypted['id'] ?? null;
            } elseif (is_string($decrypted) && str_contains($decrypted, '-')) {
                [$type, $id] = explode('-', $decrypted, 2);
            } else {
                $id = $decrypted;
            }

            $document = null;
            $doctorName = '-';
            $doctorSip = '-';
            $patientName = '-';
            $visitDate = '-';
            $documentTitle = 'Dokumen Medis';

            // Try to find the document based on parsed type
            if ($type === 'letter' || $type === 'medical_letter') {
                $document = MedicalLetter::with(['pasien', 'dokter'])->find($id);
                if ($document) {
                    $doctorName = $document->dokter->nama_petugas ?? '-';
                    $doctorSip = $document->dokter->nomor_sip ?? '-';
                    $patientName = $document->pasien->nama_pasien ?? '-';
                    $visitDate = $document->created_at->format('d-m-Y');
                    $documentTitle = $document->jenis_surat === 'surat_sakit' ? 'Surat Keterangan Sakit' : 'Surat Keterangan Sehat';
                }
            } elseif ($type === 'cert' || $type === 'certificate' || $type === 'surat_keterangan') {
                $document = SuratKeterangan::with(['pasien', 'dokter'])->find($id);
                if ($document) {
                    $doctorName = $document->dokter->nama_petugas ?? '-';
                    $doctorSip = $document->dokter->nomor_sip ?? '-';
                    $patientName = $document->pasien->nama_pasien ?? '-';
                    $visitDate = $document->created_at->format('d-m-Y');
                    $documentTitle = match ($document->jenis_surat) {
                        'sakit' => 'Surat Keterangan Sakit',
                        'sehat' => 'Surat Keterangan Sehat',
                        'bebas_narkoba' => 'Surat Keterangan Bebas Narkoba',
                        default => 'Surat Keterangan',
                    };
                }
            } elseif ($type === 'prescription' || $type === 'resep') {
                $document = MedicalRecordPrescription::with(['medicalRecord.pasien', 'medicalRecord.dokter'])->find($id);
                if ($document) {
                    $doctorName = $document->medicalRecord->dokter->nama_petugas ?? '-';
                    $doctorSip = $document->medicalRecord->dokter->nomor_sip ?? '-';
                    $patientName = $document->medicalRecord->pasien->nama_pasien ?? '-';
                    $visitDate = $document->medicalRecord->tanggal_kunjungan ? $document->medicalRecord->tanggal_kunjungan->format('d-m-Y') : $document->created_at->format('d-m-Y');
                    $documentTitle = 'Resep Obat';
                }
            }

            // Fallback checking if type was not matched
            if (! $document) {
                // Try MedicalLetter
                $document = MedicalLetter::with(['pasien', 'dokter'])->find($id);
                if ($document) {
                    $doctorName = $document->dokter->nama_petugas ?? '-';
                    $doctorSip = $document->dokter->nomor_sip ?? '-';
                    $patientName = $document->pasien->nama_pasien ?? '-';
                    $visitDate = $document->created_at->format('d-m-Y');
                    $documentTitle = $document->jenis_surat === 'surat_sakit' ? 'Surat Keterangan Sakit' : 'Surat Keterangan Sehat';
                } else {
                    // Try SuratKeterangan
                    $document = SuratKeterangan::with(['pasien', 'dokter'])->find($id);
                    if ($document) {
                        $doctorName = $document->dokter->nama_petugas ?? '-';
                        $doctorSip = $document->dokter->nomor_sip ?? '-';
                        $patientName = $document->pasien->nama_pasien ?? '-';
                        $visitDate = $document->created_at->format('d-m-Y');
                        $documentTitle = match ($document->jenis_surat) {
                            'sakit' => 'Surat Keterangan Sakit',
                            'sehat' => 'Surat Keterangan Sehat',
                            'bebas_narkoba' => 'Surat Keterangan Bebas Narkoba',
                            default => 'Surat Keterangan',
                        };
                    } else {
                        // Try Prescription
                        $document = MedicalRecordPrescription::with(['medicalRecord.pasien', 'medicalRecord.dokter'])->find($id);
                        if ($document) {
                            $doctorName = $document->medicalRecord->dokter->nama_petugas ?? '-';
                            $doctorSip = $document->medicalRecord->dokter->nomor_sip ?? '-';
                            $patientName = $document->medicalRecord->pasien->nama_pasien ?? '-';
                            $visitDate = $document->medicalRecord->tanggal_kunjungan ? $document->medicalRecord->tanggal_kunjungan->format('d-m-Y') : $document->created_at->format('d-m-Y');
                            $documentTitle = 'Resep Obat';
                        }
                    }
                }
            }

            if (! $document) {
                return view('verify-document', [
                    'isValid' => false,
                    'error' => 'Dokumen tidak ditemukan dalam database sistem.',
                ]);
            }

            $faskes = FaskesProfile::find(1);
            $namaFaskes = $faskes->nama_faskes ?? 'Klinik EMR Pintar Jabar';

            return view('verify-document', [
                'isValid' => true,
                'documentTitle' => $documentTitle,
                'namaFaskes' => $namaFaskes,
                'doctorName' => $doctorName,
                'doctorSip' => $doctorSip,
                'patientName' => $patientName,
                'visitDate' => $visitDate,
                'document' => $document,
            ]);

        } catch (\Exception $e) {
            return view('verify-document', [
                'isValid' => false,
                'error' => 'Dokumen tidak valid atau tanda tangan elektronik telah kedaluwarsa/tampered.',
            ]);
        }
    }
}
