<?php

use App\Jobs\SyncBpjsMedicalRecord;
use App\Models\MasterPetugas;
use App\Models\MedicalRecord;
use App\Models\Pasien;
use App\Models\Poli;
use Database\Seeders\DatabaseSeeder;

beforeEach(function () {
    $this->seed(DatabaseSeeder::class);
});

test('bpjs sync job completes successfully with valid patient and bpjs number', function () {
    $pasien = Pasien::create([
        'no_rekam_medis' => 'RM-BPJS-001',
        'nama_pasien' => 'Budiono Siregar',
        'nik' => '1234567890123456',
        'no_bpjs' => '0001234567890',
        'tempat_lahir' => 'Medan',
        'tanggal_lahir' => '1985-08-17',
        'jenis_kelamin' => 'L',
        'alamat' => 'Jl. Merdeka No. 10',
    ]);

    $poli = Poli::where('kode_poli', 'UMU')->first();
    $dokter = MasterPetugas::where('jenis_petugas', 'Dokter')->first();

    $record = MedicalRecord::create([
        'encounter_id' => 'ENC-BPJS-001',
        'patient_id' => $pasien->id,
        'poli_id' => $poli->id,
        'dokter_id' => $dokter->id,
        'status' => 'completed',
        'nomor_antrean' => 'A-10',
        'temperature' => 36.6,
        'tensi_sistole' => 120,
        'tensi_diastole' => 80,
        'pulse_rate' => 76,
        'bpjs_status' => 'pending',
    ]);

    // Dispatch job synchronously
    SyncBpjsMedicalRecord::dispatchSync($record->id);

    $record->refresh();

    expect($record->bpjs_status)->toBe('sent');
    expect($record->bpjs_kunjungan_no)->toStartWith('BPJS/SEP/');
    expect($record->bpjs_error_log)->toBeNull();
    expect($record->bpjs_retry_count)->toBe(0);
});

test('bpjs sync job fails and records errors when simulated failure patient name is given', function () {
    $pasien = Pasien::create([
        'no_rekam_medis' => 'RM-BPJS-002',
        'nama_pasien' => 'Test bpjs-fail Patient',
        'nik' => '1234567890123456',
        'no_bpjs' => '0001234567891',
        'tempat_lahir' => 'Medan',
        'tanggal_lahir' => '1985-08-17',
        'jenis_kelamin' => 'L',
        'alamat' => 'Jl. Merdeka No. 11',
    ]);

    $poli = Poli::where('kode_poli', 'UMU')->first();
    $dokter = MasterPetugas::where('jenis_petugas', 'Dokter')->first();

    $record = MedicalRecord::create([
        'encounter_id' => 'ENC-BPJS-002',
        'patient_id' => $pasien->id,
        'poli_id' => $poli->id,
        'dokter_id' => $dokter->id,
        'status' => 'completed',
        'nomor_antrean' => 'A-11',
        'temperature' => 36.6,
        'tensi_sistole' => 120,
        'tensi_diastole' => 80,
        'pulse_rate' => 76,
        'bpjs_status' => 'pending',
    ]);

    try {
        SyncBpjsMedicalRecord::dispatchSync($record->id);
        $this->fail('Job should have thrown an exception.');
    } catch (Throwable $exception) {
        expect($exception->getMessage())->toContain('503 Service Unavailable');
    }

    $record->refresh();

    expect($record->bpjs_status)->toBe('failed');
    expect($record->bpjs_error_log)->toContain('503 Service Unavailable');
    expect($record->bpjs_retry_count)->toBe(1);
    expect($record->bpjs_kunjungan_no)->toBeNull();
});

test('bpjs sync job fails when patient has no bpjs number', function () {
    $pasien = Pasien::create([
        'no_rekam_medis' => 'RM-BPJS-003',
        'nama_pasien' => 'Budiono No BPJS',
        'nik' => '1234567890123456',
        'no_bpjs' => '',
        'tempat_lahir' => 'Medan',
        'tanggal_lahir' => '1985-08-17',
        'jenis_kelamin' => 'L',
        'alamat' => 'Jl. Merdeka No. 12',
    ]);

    $poli = Poli::where('kode_poli', 'UMU')->first();
    $dokter = MasterPetugas::where('jenis_petugas', 'Dokter')->first();

    $record = MedicalRecord::create([
        'encounter_id' => 'ENC-BPJS-003',
        'patient_id' => $pasien->id,
        'poli_id' => $poli->id,
        'dokter_id' => $dokter->id,
        'status' => 'completed',
        'nomor_antrean' => 'A-12',
        'temperature' => 36.6,
        'tensi_sistole' => 120,
        'tensi_diastole' => 80,
        'pulse_rate' => 76,
        'bpjs_status' => 'pending',
    ]);

    try {
        SyncBpjsMedicalRecord::dispatchSync($record->id);
        $this->fail('Job should have thrown an exception.');
    } catch (Throwable $exception) {
        expect($exception->getMessage())->toContain('No. Kartu BPJS kosong');
    }

    $record->refresh();

    expect($record->bpjs_status)->toBe('failed');
    expect($record->bpjs_error_log)->toContain('No. Kartu BPJS kosong');
    expect($record->bpjs_retry_count)->toBe(1);
});
