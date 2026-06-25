<?php

namespace App\Jobs;

use App\Models\MedicalRecord;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Throwable;

class SyncBpjsMedicalRecord implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * The number of times the job may be attempted.
     *
     * @var int
     */
    public $tries = 3;

    /**
     * Indicate if the job should fail if the timeout is exceeded.
     *
     * @var bool
     */
    public $failOnTimeout = true;

    /**
     * Create a new job instance.
     */
    public function __construct(public int $medicalRecordId) {}

    /**
     * Calculate the number of seconds to wait before retrying the job.
     *
     * @return array<int, int>
     */
    public function backoff(): array
    {
        return [5, 10, 20];
    }

    /**
     * Execute the job.
     *
     * @throws Throwable
     */
    public function handle(): void
    {
        $medicalRecord = MedicalRecord::findOrFail($this->medicalRecordId);
        $medicalRecord->update(['bpjs_status' => 'processing']);

        $medicalRecord->load(['pasien', 'pendaftaran', 'dokter', 'poli']);

        try {
            $this->simulateBpjsApiCall($medicalRecord);

            $medicalRecord->update([
                'bpjs_status' => 'sent',
                'bpjs_kunjungan_no' => 'BPJS/SEP/'.date('Ymd').'/'.str_pad((string) $medicalRecord->id, 6, '0', STR_PAD_LEFT),
                'bpjs_error_log' => null,
            ]);
        } catch (Throwable $exception) {
            $retryCount = $medicalRecord->bpjs_retry_count + 1;
            $medicalRecord->update([
                'bpjs_status' => 'failed',
                'bpjs_error_log' => $exception->getMessage(),
                'bpjs_retry_count' => $retryCount,
            ]);

            throw $exception;
        }
    }

    /**
     * Simulate BPJS Health API Call (P-Care / VClaim).
     *
     * @throws \Exception
     */
    protected function simulateBpjsApiCall(MedicalRecord $record): void
    {
        $pasien = $record->pasien;
        if (! $pasien) {
            throw new \Exception('Pasien not found on medical record.');
        }

        if (empty($pasien->no_bpjs)) {
            throw new \Exception('No. Kartu BPJS kosong. Pasien must have a valid BPJS Card Number.');
        }

        $patientName = strtolower($pasien->nama_pasien ?? '');
        if (str_contains($patientName, 'bpjs-fail') || str_contains($patientName, 'timeout')) {
            throw new \Exception('BPJS API Bridge Error: 503 Service Unavailable / Connection Timeout.');
        }

        usleep(50000);
    }
}
