<?php

use App\Models\LabOrder;
use App\Models\LabOrderResult;
use App\Models\MasterLabTest;
use App\Models\MasterPetugas;
use App\Models\MedicalRecord;
use App\Models\Pasien;
use App\Models\Pendaftaran;
use App\Models\Poli;
use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Livewire\Livewire;

beforeEach(function () {
    $this->seed(DatabaseSeeder::class);
});

test('master lab tests are seeded with at least 20 records', function () {
    expect(MasterLabTest::count())->toBeGreaterThanOrEqual(20);
    expect(MasterLabTest::where('category', 'Kimia Darah')->exists())->toBeTrue();
    expect(MasterLabTest::where('category', 'Hematologi')->exists())->toBeTrue();
});

test('doctor can search lab tests in the workspace', function () {
    $user = User::first();
    $this->actingAs($user);

    $pasien = Pasien::create([
        'no_rekam_medis' => 'RM-LAB-0001',
        'nama_pasien' => 'Tes Pasien Lab',
        'nik' => '3201234567000001',
        'tempat_lahir' => 'Bandung',
        'tanggal_lahir' => '1990-01-01',
        'jenis_kelamin' => 'L',
        'golongan_darah' => 'O',
        'alamat' => 'Jl. Test No.1',
        'status_pasien' => 'aktif',
    ]);

    $poli = Poli::where('kode_poli', 'UMU')->first();
    $dokter = MasterPetugas::where('jenis_petugas', 'Dokter')->first();

    $pendaftaran = Pendaftaran::create([
        'no_registrasi' => 'REG-LAB-0001',
        'pasien_id' => $pasien->id,
        'poli_id' => $poli->id,
        'dokter_id' => $dokter->id,
        'no_antrean' => 'A-01',
        'angka_antrean' => 1,
        'status_antrean' => 'menunggu',
        'cara_bayar' => 'Umum',
        'jenis_kunjungan' => 'Baru',
        'keluhan_awal' => 'Cek lab rutin',
    ]);

    $medicalRecord = MedicalRecord::create([
        'encounter_id' => 'ENC-LAB-0001',
        'patient_id' => $pasien->id,
        'pendaftaran_id' => $pendaftaran->id,
        'poli_id' => $poli->id,
        'status' => 'anamnesis',
        'nomor_antrean' => 'A-01',
    ]);

    Livewire::test('⚡medical-record.poli-umum', ['record' => $medicalRecord])
        ->set('labQuery', 'Darah')
        ->assertSet('labResults', fn ($results) => count($results) > 0);
});

test('doctor can add lab test to selectedLabTests', function () {
    $user = User::first();
    $this->actingAs($user);

    $pasien = Pasien::create([
        'no_rekam_medis' => 'RM-LAB-0002',
        'nama_pasien' => 'Pasien Lab Kedua',
        'nik' => '3201234567000002',
        'tempat_lahir' => 'Jakarta',
        'tanggal_lahir' => '1985-06-15',
        'jenis_kelamin' => 'P',
        'golongan_darah' => 'A',
        'alamat' => 'Jl. Lab No. 2',
        'status_pasien' => 'aktif',
    ]);

    $poli = Poli::where('kode_poli', 'UMU')->first();
    $dokter = MasterPetugas::where('jenis_petugas', 'Dokter')->first();

    $pendaftaran = Pendaftaran::create([
        'no_registrasi' => 'REG-LAB-0002',
        'pasien_id' => $pasien->id,
        'poli_id' => $poli->id,
        'dokter_id' => $dokter->id,
        'no_antrean' => 'A-02',
        'angka_antrean' => 2,
        'status_antrean' => 'menunggu',
        'cara_bayar' => 'Umum',
        'jenis_kunjungan' => 'Baru',
        'keluhan_awal' => 'Demam',
    ]);

    $medicalRecord = MedicalRecord::create([
        'encounter_id' => 'ENC-LAB-0002',
        'patient_id' => $pasien->id,
        'pendaftaran_id' => $pendaftaran->id,
        'poli_id' => $poli->id,
        'status' => 'anamnesis',
        'nomor_antrean' => 'A-02',
    ]);

    $labTest = MasterLabTest::where('test_name', 'Gula Darah Sewaktu (GDS)')->first();

    Livewire::test('⚡medical-record.poli-umum', ['record' => $medicalRecord])
        ->call('addLabTest', $labTest->id)
        ->assertSet('selectedLabTests', fn ($tests) => count($tests) === 1 && $tests[0]['id'] === $labTest->id);
});

test('lab total tariff is correctly computed from selected tests', function () {
    $user = User::first();
    $this->actingAs($user);

    $pasien = Pasien::create([
        'no_rekam_medis' => 'RM-LAB-0003',
        'nama_pasien' => 'Pasien Lab Tiga',
        'nik' => '3201234567000003',
        'tempat_lahir' => 'Surabaya',
        'tanggal_lahir' => '1975-11-20',
        'jenis_kelamin' => 'L',
        'golongan_darah' => 'B',
        'alamat' => 'Jl. Lab No. 3',
        'status_pasien' => 'aktif',
    ]);

    $poli = Poli::where('kode_poli', 'UMU')->first();
    $dokter = MasterPetugas::where('jenis_petugas', 'Dokter')->first();

    $pendaftaran = Pendaftaran::create([
        'no_registrasi' => 'REG-LAB-0003',
        'pasien_id' => $pasien->id,
        'poli_id' => $poli->id,
        'dokter_id' => $dokter->id,
        'no_antrean' => 'A-03',
        'angka_antrean' => 3,
        'status_antrean' => 'menunggu',
        'cara_bayar' => 'Umum',
        'jenis_kunjungan' => 'Baru',
        'keluhan_awal' => 'Check up',
    ]);

    $medicalRecord = MedicalRecord::create([
        'encounter_id' => 'ENC-LAB-0003',
        'patient_id' => $pasien->id,
        'pendaftaran_id' => $pendaftaran->id,
        'poli_id' => $poli->id,
        'status' => 'anamnesis',
        'nomor_antrean' => 'A-03',
    ]);

    $test1 = MasterLabTest::where('test_name', 'Hemoglobin')->first();
    $test2 = MasterLabTest::where('test_name', 'Gula Darah Sewaktu (GDS)')->first();
    $expectedTotal = $test1->tariff + $test2->tariff;

    $component = Livewire::test('⚡medical-record.poli-umum', ['record' => $medicalRecord])
        ->call('addLabTest', $test1->id)
        ->call('addLabTest', $test2->id);

    expect($component->get('labTotalTariff'))->toBe($expectedTotal);
});

test('saving draft creates lab_order and lab_order_results with snapshots', function () {
    $user = User::first();
    $this->actingAs($user);

    $pasien = Pasien::create([
        'no_rekam_medis' => 'RM-LAB-0004',
        'nama_pasien' => 'Pasien Lab Empat',
        'nik' => '3201234567000004',
        'tempat_lahir' => 'Medan',
        'tanggal_lahir' => '2000-03-25',
        'jenis_kelamin' => 'P',
        'golongan_darah' => 'AB',
        'alamat' => 'Jl. Lab No. 4',
        'status_pasien' => 'aktif',
    ]);

    $poli = Poli::where('kode_poli', 'UMU')->first();
    $dokter = MasterPetugas::where('jenis_petugas', 'Dokter')->first();

    $pendaftaran = Pendaftaran::create([
        'no_registrasi' => 'REG-LAB-0004',
        'pasien_id' => $pasien->id,
        'poli_id' => $poli->id,
        'dokter_id' => $dokter->id,
        'no_antrean' => 'A-04',
        'angka_antrean' => 4,
        'status_antrean' => 'menunggu',
        'cara_bayar' => 'BPJS',
        'jenis_kunjungan' => 'Baru',
        'keluhan_awal' => 'Cek gula darah',
    ]);

    $medicalRecord = MedicalRecord::create([
        'encounter_id' => 'ENC-LAB-0004',
        'patient_id' => $pasien->id,
        'pendaftaran_id' => $pendaftaran->id,
        'poli_id' => $poli->id,
        'status' => 'anamnesis',
        'nomor_antrean' => 'A-04',
    ]);

    $gds = MasterLabTest::where('test_name', 'Gula Darah Sewaktu (GDS)')->first();
    $hb = MasterLabTest::where('test_name', 'Hemoglobin')->first();

    Livewire::test('⚡medical-record.poli-umum', ['record' => $medicalRecord])
        ->call('addLabTest', $gds->id)
        ->call('addLabTest', $hb->id)
        ->set('labClinicalNotes', 'Cek di awal pagi, pasien sudah puasa 8 jam')
        ->call('saveDraft');

    $labOrder = LabOrder::where('medical_record_id', $medicalRecord->id)->first();

    expect($labOrder)->not->toBeNull()
        ->and($labOrder->status)->toBe('pending')
        ->and($labOrder->total_tariff)->toBe($gds->tariff + $hb->tariff)
        ->and($labOrder->clinical_notes)->toBe('Cek di awal pagi, pasien sudah puasa 8 jam')
        ->and($labOrder->results()->count())->toBe(2);

    $gdsResult = $labOrder->results()->where('master_lab_test_id', $gds->id)->first();
    expect($gdsResult)->not->toBeNull()
        ->and($gdsResult->test_name_snapshot)->toBe($gds->test_name)
        ->and($gdsResult->tariff_snapshot)->toBe($gds->tariff)
        ->and($gdsResult->normal_range_snapshot)->toBe($gds->default_normal_range)
        ->and($gdsResult->unit_snapshot)->toBe($gds->default_unit)
        ->and($gdsResult->result_value)->toBeNull();
});

test('lab order is preloaded correctly on component mount', function () {
    $user = User::first();
    $this->actingAs($user);

    $pasien = Pasien::create([
        'no_rekam_medis' => 'RM-LAB-0005',
        'nama_pasien' => 'Pasien Lab Lima',
        'nik' => '3201234567000005',
        'tempat_lahir' => 'Makassar',
        'tanggal_lahir' => '1988-07-07',
        'jenis_kelamin' => 'L',
        'golongan_darah' => 'O',
        'alamat' => 'Jl. Lab No. 5',
        'status_pasien' => 'aktif',
    ]);

    $poli = Poli::where('kode_poli', 'UMU')->first();
    $dokter = MasterPetugas::where('jenis_petugas', 'Dokter')->first();

    $pendaftaran = Pendaftaran::create([
        'no_registrasi' => 'REG-LAB-0005',
        'pasien_id' => $pasien->id,
        'poli_id' => $poli->id,
        'dokter_id' => $dokter->id,
        'no_antrean' => 'A-05',
        'angka_antrean' => 5,
        'status_antrean' => 'menunggu',
        'cara_bayar' => 'Umum',
        'jenis_kunjungan' => 'Lama',
        'keluhan_awal' => 'Kontrol kolesterol',
    ]);

    $medicalRecord = MedicalRecord::create([
        'encounter_id' => 'ENC-LAB-0005',
        'patient_id' => $pasien->id,
        'pendaftaran_id' => $pendaftaran->id,
        'poli_id' => $poli->id,
        'status' => 'anamnesis',
        'nomor_antrean' => 'A-05',
    ]);

    // Pre-create a lab order
    $kolesterol = MasterLabTest::where('test_name', 'Kolesterol Total')->first();
    $labOrder = LabOrder::create([
        'medical_record_id' => $medicalRecord->id,
        'requested_by_id' => $dokter->id,
        'status' => 'pending',
        'total_tariff' => $kolesterol->tariff,
        'clinical_notes' => 'Pasien puasa',
    ]);

    LabOrderResult::create([
        'lab_order_id' => $labOrder->id,
        'master_lab_test_id' => $kolesterol->id,
        'test_name_snapshot' => $kolesterol->test_name,
        'normal_range_snapshot' => $kolesterol->default_normal_range,
        'unit_snapshot' => $kolesterol->default_unit,
        'tariff_snapshot' => $kolesterol->tariff,
    ]);

    $component = Livewire::test('⚡medical-record.poli-umum', ['record' => $medicalRecord]);

    expect($component->get('existingLabOrderId'))->toBe($labOrder->id)
        ->and($component->get('labClinicalNotes'))->toBe('Pasien puasa')
        ->and(count($component->get('selectedLabTests')))->toBe(1)
        ->and($component->get('selectedLabTests.0.id'))->toBe($kolesterol->id);
});
