<?php

use App\Models\MasterIcd10;
use App\Models\MasterPetugas;
use App\Models\MedicalRecord;
use App\Models\MedicalRecordIcd10;
use App\Models\Pasien;
use App\Models\Poli;
use App\Models\User;
use Database\Seeders\DatabaseSeeder;

beforeEach(function () {
    $this->seed(DatabaseSeeder::class);
});

test('guests are redirected to the login page for satusehat dashboard', function () {
    $this->get('/admin/satusehat-dashboard')
        ->assertRedirect(route('login'));
});

test('authenticated users can visit the satusehat dashboard', function () {
    $user = User::first();
    $this->actingAs($user);

    $this->get('/admin/satusehat-dashboard')
        ->assertOk()
        ->assertSee('SatuSehat Bridging Monitor');
});

test('satusehat dashboard filters records by date range', function () {
    $user = User::first();
    $this->actingAs($user);

    $poli = Poli::where('kode_poli', 'UMU')->first();
    $dokter = MasterPetugas::where('jenis_petugas', 'Dokter')->first();

    $pasienA = Pasien::create([
        'no_rekam_medis' => 'RM-RANGE-A',
        'nama_pasien' => 'Range Patient A',
        'nik' => '1234567890123460',
        'tempat_lahir' => 'Bandung',
        'tanggal_lahir' => '1990-01-01',
        'jenis_kelamin' => 'L',
        'alamat' => 'Jl. Test',
    ]);

    $pasienB = Pasien::create([
        'no_rekam_medis' => 'RM-RANGE-B',
        'nama_pasien' => 'Range Patient B',
        'nik' => '1234567890123461',
        'tempat_lahir' => 'Bandung',
        'tanggal_lahir' => '1990-01-01',
        'jenis_kelamin' => 'L',
        'alamat' => 'Jl. Test',
    ]);

    // Record from 3 days ago
    MedicalRecord::create([
        'encounter_id' => 'ENC-RNG-A',
        'patient_id' => $pasienA->id,
        'poli_id' => $poli->id,
        'dokter_id' => $dokter->id,
        'status' => 'completed',
        'nomor_antrean' => 'R-01',
        'created_at' => now()->subDays(3),
    ]);

    // Record from 1 day ago
    MedicalRecord::create([
        'encounter_id' => 'ENC-RNG-B',
        'patient_id' => $pasienB->id,
        'poli_id' => $poli->id,
        'dokter_id' => $dokter->id,
        'status' => 'completed',
        'nomor_antrean' => 'R-02',
        'created_at' => now()->subDay(),
    ]);

    // Filter range that includes both records
    $this->get('/admin/satusehat-dashboard?'.http_build_query([
        'date_from' => now()->subDays(3)->toDateString(),
        'date_to' => now()->subDay()->toDateString(),
    ]))
        ->assertOk()
        ->assertSee('Range Patient A')
        ->assertSee('Range Patient B');

    // Filter range that includes only the recent record
    $this->get('/admin/satusehat-dashboard?'.http_build_query([
        'date_from' => now()->subDay()->toDateString(),
        'date_to' => now()->subDay()->toDateString(),
    ]))
        ->assertOk()
        ->assertSee('Range Patient B')
        ->assertDontSee('Range Patient A');
});

test('medical record dynamic validation logic works correctly', function () {
    $pasien = Pasien::create([
        'no_rekam_medis' => 'RM-999',
        'nama_pasien' => 'Test Patient',
        'nik' => '', // Empty NIK
        'tempat_lahir' => 'Bandung',
        'tanggal_lahir' => '1990-01-01',
        'jenis_kelamin' => 'L',
        'alamat' => 'Jl. Test',
        'status_pasien' => 'aktif',
    ]);

    $poli = Poli::where('kode_poli', 'UMU')->first();
    // Clear SatuSehat Location ID for testing
    $poli->update(['satu_sehat_location_id' => null]);
    if ($poli->satusehat) {
        $poli->satusehat->delete();
    }

    $dokter = MasterPetugas::where('jenis_petugas', 'Dokter')->first();
    $dokter->update(['ihs_number_practitioner' => null]);

    $medicalRecord = MedicalRecord::create([
        'encounter_id' => 'ENC-999',
        'patient_id' => $pasien->id,
        'poli_id' => $poli->id,
        'dokter_id' => $dokter->id,
        'status' => 'completed',
        'nomor_antrean' => 'A-99',
        'temperature' => null,
        'tensi_sistole' => null,
        'tensi_diastole' => null,
        'pulse_rate' => null,
    ]);

    // 1. Initial validation - Should be incomplete with missing items
    $eval = $medicalRecord->evaluateSatusehatValidation();
    expect($eval['status'])->toBe('incomplete');
    expect($eval['missing'])->toContain('NIK Pasien Kosong');
    expect($eval['missing'])->toContain('IHS Pasien Kosong');
    expect($eval['missing'])->toContain('IHS Dokter Kosong');
    expect($eval['missing'])->toContain('ID Lokasi SatuSehat Poli Kosong');
    expect($eval['missing'])->toContain('Suhu Tubuh Belum Diisi');
    expect($eval['missing'])->toContain('Tekanan Darah Belum Diisi');
    expect($eval['missing'])->toContain('Nadi Belum Diisi');
    expect($eval['missing'])->toContain('ICD-10 Belum Diisi');

    // 2. Resolve patient NIK & IHS
    $pasien->update([
        'nik' => '1234567890123456',
        'ihs_number' => 'IHS-12345',
    ]);

    // 3. Resolve doctor IHS
    $dokter->update([
        'ihs_number_practitioner' => 'IHS-DOC-123',
    ]);

    // 4. Resolve location ID
    $poli->update([
        'satu_sehat_location_id' => 'LOC-123',
    ]);

    // 5. Resolve vital signs
    $medicalRecord->update([
        'temperature' => 36.5,
        'tensi_sistole' => 120,
        'tensi_diastole' => 80,
        'pulse_rate' => 80,
    ]);

    // 6. Resolve ICD-10 diagnosis
    $icd10 = MasterIcd10::first();
    MedicalRecordIcd10::create([
        'medical_record_id' => $medicalRecord->id,
        'master_icd10_id' => $icd10->id,
        'icd10_code' => $icd10->kode,
        'icd10_name' => $icd10->nama_penyakit,
        'is_primary' => true,
    ]);

    // Re-evaluate validation - Should be ready
    $medicalRecord->refresh();
    $eval = $medicalRecord->evaluateSatusehatValidation();
    expect($eval['status'])->toBe('ready');
    expect($eval['missing'])->toBeEmpty();
});

test('individual dispatcher simulates success and failure cases', function () {
    $user = User::first();
    $this->actingAs($user);

    $pasienSuccess = Pasien::create([
        'no_rekam_medis' => 'RM-OK',
        'nama_pasien' => 'Success Patient',
        'nik' => '1234567890123456',
        'ihs_number' => 'IHS-OK',
        'tempat_lahir' => 'Bandung',
        'tanggal_lahir' => '1990-01-01',
        'jenis_kelamin' => 'L',
        'alamat' => 'Jl. Test',
    ]);

    $pasienFailure = Pasien::create([
        'no_rekam_medis' => 'RM-FAIL',
        'nama_pasien' => 'Simulate Failure Patient',
        'nik' => '9934567890123456', // starts with 99
        'ihs_number' => 'IHS-FAIL',
        'tempat_lahir' => 'Bandung',
        'tanggal_lahir' => '1990-01-01',
        'jenis_kelamin' => 'L',
        'alamat' => 'Jl. Test',
    ]);

    $poli = Poli::where('kode_poli', 'UMU')->first();
    $poli->update(['satu_sehat_location_id' => 'LOC-OK']);

    $dokter = MasterPetugas::where('jenis_petugas', 'Dokter')->first();
    $dokter->update(['ihs_number_practitioner' => 'IHS-DOC-OK']);

    $recordSuccess = MedicalRecord::create([
        'encounter_id' => 'ENC-OK',
        'patient_id' => $pasienSuccess->id,
        'poli_id' => $poli->id,
        'dokter_id' => $dokter->id,
        'status' => 'completed',
        'nomor_antrean' => 'A-01',
        'temperature' => 36.5,
        'tensi_sistole' => 120,
        'tensi_diastole' => 80,
        'pulse_rate' => 80,
        'satusehat_status' => 'ready',
    ]);

    $recordFailure = MedicalRecord::create([
        'encounter_id' => 'ENC-FAIL',
        'patient_id' => $pasienFailure->id,
        'poli_id' => $poli->id,
        'dokter_id' => $dokter->id,
        'status' => 'completed',
        'nomor_antrean' => 'A-02',
        'temperature' => 36.5,
        'tensi_sistole' => 120,
        'tensi_diastole' => 80,
        'pulse_rate' => 80,
        'satusehat_status' => 'ready',
    ]);

    $icd10 = MasterIcd10::first();
    foreach ([$recordSuccess, $recordFailure] as $rec) {
        MedicalRecordIcd10::create([
            'medical_record_id' => $rec->id,
            'master_icd10_id' => $icd10->id,
            'icd10_code' => $icd10->kode,
            'icd10_name' => $icd10->nama_penyakit,
            'is_primary' => true,
        ]);
    }

    // 1. Dispatch success record
    $this->post(route('admin.satusehat-dashboard.dispatch', ['record' => $recordSuccess->id]))
        ->assertRedirect();
    $recordSuccess->refresh();
    expect($recordSuccess->satusehat_status)->toBe('sent');
    expect($recordSuccess->satusehat_encounter_id)->toStartWith('Encounter/');
    expect($recordSuccess->satusehat_condition_id)->toStartWith('Condition/');
    expect($recordSuccess->satusehat_error_log)->toBeNull();

    // 2. Dispatch failure record
    $this->post(route('admin.satusehat-dashboard.dispatch', ['record' => $recordFailure->id]))
        ->assertRedirect();
    $recordFailure->refresh();
    expect($recordFailure->satusehat_status)->toBe('failed');
    expect($recordFailure->satusehat_encounter_id)->toBeNull();
    expect($recordFailure->satusehat_condition_id)->toBeNull();
    expect($recordFailure->satusehat_error_log)->toContain('Simulated FHIR API Error Response');
});

test('batch dispatcher dispatches all ready records', function () {
    $user = User::first();
    $this->actingAs($user);

    $pasien = Pasien::create([
        'no_rekam_medis' => 'RM-BATCH',
        'nama_pasien' => 'Batch Patient',
        'nik' => '1234567890123458',
        'ihs_number' => 'IHS-BATCH',
        'tempat_lahir' => 'Bandung',
        'tanggal_lahir' => '1990-01-01',
        'jenis_kelamin' => 'L',
        'alamat' => 'Jl. Test',
    ]);

    $poli = Poli::where('kode_poli', 'UMU')->first();
    $poli->update(['satu_sehat_location_id' => 'LOC-BATCH']);

    $dokter = MasterPetugas::where('jenis_petugas', 'Dokter')->first();
    $dokter->update(['ihs_number_practitioner' => 'IHS-DOC-BATCH']);

    $record = MedicalRecord::create([
        'encounter_id' => 'ENC-BATCH',
        'patient_id' => $pasien->id,
        'poli_id' => $poli->id,
        'dokter_id' => $dokter->id,
        'status' => 'completed',
        'nomor_antrean' => 'A-03',
        'temperature' => 36.5,
        'tensi_sistole' => 120,
        'tensi_diastole' => 80,
        'pulse_rate' => 80,
        'satusehat_status' => 'ready',
        'created_at' => now(), // today
    ]);

    $icd10 = MasterIcd10::first();
    MedicalRecordIcd10::create([
        'medical_record_id' => $record->id,
        'master_icd10_id' => $icd10->id,
        'icd10_code' => $icd10->kode,
        'icd10_name' => $icd10->nama_penyakit,
        'is_primary' => true,
    ]);

    // Dispatch batch
    $this->post(route('admin.satusehat-dashboard.dispatch-all-ready'), [
        'date_from' => now()->toDateString(),
        'date_to' => now()->toDateString(),
    ])
        ->assertRedirect();

    $record->refresh();
    expect($record->satusehat_status)->toBe('sent');
});
