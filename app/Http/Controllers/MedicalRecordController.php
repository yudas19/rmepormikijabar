<?php

namespace App\Http\Controllers;

use App\Models\LabOrder;
use App\Models\MedicalRecord;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class MedicalRecordController extends Controller
{
    public function examine(string $poliklinik, string $encounter_id): View
    {
        /** @var User|null $user */
        $user = Auth::user();

        abort_if(! $user || ! $user->can('akses_rekam_medis'), 403, 'Akses ditolak: Anda tidak memiliki izin untuk melihat rekam medis.');

        // 1. Validate polyclinic type parameter
        if (! in_array($poliklinik, ['umum', 'gigi', 'kia'])) {
            abort(404, 'Poliklinik tidak ditemukan.');
        }

        // 2. Fetch medical record by encounter_id (unique identifier)
        $record = MedicalRecord::with(['pasien', 'poli', 'pendaftaran.dokter', 'icd10s', 'icd9s', 'prescriptions.items.requestedObat', 'perawat', 'dokter'])
            ->where('encounter_id', $encounter_id)
            ->firstOrFail();

        // 3. Ensure the accessed URL matching the record's poliklinik target
        if ($record->poliklinik_type !== $poliklinik) {
            abort(403, 'Akses ditolak: Poliklinik tidak sesuai.');
        }

        // 4. Fetch the 3 most recent medical histories
        $recentHistory = MedicalRecord::with(['icd10s', 'pendaftaran.dokter', 'dokter', 'perawat'])
            ->where('patient_id', $record->patient_id)
            ->where('status', 'completed')
            ->where('id', '!=', $record->id)
            ->latest()
            ->take(3)
            ->get();

        // 5. Fetch lab orders for this record
        $labOrders = LabOrder::with(['results.masterLabTest', 'results.analis'])
            ->where('medical_record_id', $record->id)
            ->latest()
            ->get();

        // 6. Load the base view and pass required variables
        return view('medical_records.examine', [
            'record' => $record,
            'poliklinik' => $poliklinik,
            'patient' => $record->pasien,
            'recentHistory' => $recentHistory,
            'labOrders' => $labOrders,
        ]);
    }
}
