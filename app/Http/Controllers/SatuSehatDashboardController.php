<?php

namespace App\Http\Controllers;

use App\Models\MedicalRecord;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

class SatuSehatDashboardController extends Controller
{
    /**
     * Display the SatuSehat Bridging Queue Dashboard.
     */
    public function index(Request $request): View
    {
        $date = $request->input('date', today()->toDateString());

        // Fetch medical records for the selected date
        $records = MedicalRecord::with(['pasien', 'dokter', 'poli', 'poli.satusehat', 'icd10s'])
            ->whereDate('created_at', $date)
            ->where('status', '!=', 'batal')
            ->get();

        // Dynamically evaluate status for non-sent records and update DB if needed
        foreach ($records as $record) {
            if ($record->satusehat_status !== 'sent') {
                $eval = $record->evaluateSatusehatValidation();

                if ($record->satusehat_status !== $eval['status']) {
                    // If it was failed, only change back if it's now ready
                    if ($record->satusehat_status === 'failed' && $eval['status'] === 'incomplete') {
                        continue;
                    }
                    $record->update([
                        'satusehat_status' => $eval['status'],
                    ]);
                }
            }
        }

        // Re-query or compute aggregates from collection for consistent dashboard counts
        $counts = [
            'incomplete' => $records->where('satusehat_status', 'incomplete')->count(),
            'ready' => $records->where('satusehat_status', 'ready')->count(),
            'sent' => $records->where('satusehat_status', 'sent')->count(),
            'failed' => $records->where('satusehat_status', 'failed')->count(),
            'total' => $records->count(),
        ];

        return view('admin.satusehat-dashboard.index', [
            'records' => $records,
            'date' => $date,
            'counts' => $counts,
        ]);
    }

    /**
     * Dispatch an individual medical record to SatuSehat (Mock API Call).
     */
    public function dispatchRecord(MedicalRecord $record): RedirectResponse
    {
        // Must be ready or failed to be sent
        if (! in_array($record->satusehat_status, ['ready', 'failed'])) {
            return back()->with('error', 'Status rekam medis tidak valid untuk dikirim.');
        }

        $result = $this->simulateSatuSehatTransmission($record);

        if ($result['success']) {
            return back()->with('success', "Rekam Medis No. RM {$record->pasien?->no_rekam_medis} berhasil dikirim ke SatuSehat.");
        }

        return back()->with('error', "Gagal mengirim Rekam Medis No. RM {$record->pasien?->no_rekam_medis} (Simulasi API Error).");
    }

    /**
     * Dispatch all 'ready' medical records for the filtered date (Mock Batch API Call).
     */
    public function dispatchAllReady(Request $request): RedirectResponse
    {
        $date = $request->input('date', today()->toDateString());

        $records = MedicalRecord::whereDate('created_at', $date)
            ->where('satusehat_status', 'ready')
            ->where('status', '!=', 'batal')
            ->get();

        if ($records->isEmpty()) {
            return back()->with('warning', 'Tidak ada data antrean yang berstatus Ready untuk dikirim pada tanggal ini.');
        }

        $sentCount = 0;
        $failCount = 0;

        foreach ($records as $record) {
            $result = $this->simulateSatuSehatTransmission($record);
            if ($result['success']) {
                $sentCount++;
            } else {
                $failCount++;
            }
        }

        $msg = "Proses Batch Selesai: {$sentCount} data berhasil dikirim.";
        if ($failCount > 0) {
            $msg .= " {$failCount} data gagal dikirim (simulasi error).";

            return back()->with('warning', $msg);
        }

        return back()->with('success', $msg);
    }

    /**
     * Simulate FHIR API Call for SatuSehat Encounter and Condition resource.
     *
     * @return array{success: bool}
     */
    protected function simulateSatuSehatTransmission(MedicalRecord $record): array
    {
        $eval = $record->evaluateSatusehatValidation();

        // Ensure it is actually ready (valid NIK, IHS, location, TTV, ICD-10)
        if ($eval['status'] !== 'ready') {
            $record->update([
                'satusehat_status' => 'incomplete',
                'satusehat_error_log' => 'Pre-validation failed: '.implode(', ', $eval['missing']),
            ]);

            return ['success' => false];
        }

        $patientName = strtolower($record->pasien?->nama_pasien ?? '');
        $patientNik = $record->pasien?->nik ?? '';

        // Simulate API failure if patient name contains 'fail' or NIK starts with '99'
        if (str_contains($patientName, 'fail') || str_starts_with($patientNik, '99')) {
            $mockError = [
                'resourceType' => 'OperationOutcome',
                'id' => (string) Str::uuid(),
                'issue' => [
                    [
                        'severity' => 'error',
                        'code' => 'invalid',
                        'diagnostics' => 'Simulated FHIR API Error Response: 400 Bad Request. '.
                                         'Patient resource registration is suspended or invalid. '.
                                         'Please recheck Kemenkes SatuSehat IHS validation status.',
                    ],
                ],
            ];

            $record->update([
                'satusehat_status' => 'failed',
                'satusehat_error_log' => json_encode($mockError, JSON_PRETTY_PRINT),
                'satusehat_encounter_id' => null,
                'satusehat_condition_id' => null,
            ]);

            return ['success' => false];
        }

        // Simulate successful transmission
        $record->update([
            'satusehat_status' => 'sent',
            'satusehat_encounter_id' => 'Encounter/'.Str::uuid(),
            'satusehat_condition_id' => 'Condition/'.Str::uuid(),
            'satusehat_error_log' => null,
        ]);

        return ['success' => true];
    }
}
