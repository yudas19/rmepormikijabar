<?php

use App\Models\MasterPetugas;
use App\Models\MedicalRecord;
use App\Models\OdontogramRecord;
use App\Models\Pasien;
use App\Models\Pendaftaran;
use App\Models\Poli;
use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Livewire\Livewire;

beforeEach(function () {
    $this->seed(DatabaseSeeder::class);
});

test('odontogram section is rendered for gigi poliklinik', function () {
    $user = User::first();
    $this->actingAs($user);

    $pasien = Pasien::create([
        'no_rekam_medis' => 'RM-GIGI-0001',
        'nama_pasien' => 'Budi Santoso',
        'nik' => '3201234567890010',
        'tempat_lahir' => 'Cirebon',
        'tanggal_lahir' => '1985-04-20',
        'jenis_kelamin' => 'L',
        'golongan_darah' => 'O',
        'alamat' => 'Jl. Siliwangi No. 10',
        'status_pasien' => 'aktif',
    ]);

    $poli = Poli::where('kode_poli', 'GIG')->first();
    $dokter = MasterPetugas::where('jenis_petugas', 'Dokter')->first();

    $pendaftaran = Pendaftaran::create([
        'no_registrasi' => 'REG-GIGI-0001',
        'pasien_id' => $pasien->id,
        'poli_id' => $poli->id,
        'dokter_id' => $dokter->id,
        'no_antrean' => 'G-01',
        'angka_antrean' => 1,
        'status_antrean' => 'menunggu',
        'cara_bayar' => 'Umum',
        'jenis_kunjungan' => 'Baru',
        'keluhan_awal' => 'Gigi berlubang',
    ]);

    $medicalRecord = MedicalRecord::create([
        'encounter_id' => 'ENC-GIGI-0001',
        'patient_id' => $pasien->id,
        'pendaftaran_id' => $pendaftaran->id,
        'poli_id' => $poli->id,
        'status' => 'waiting',
        'nomor_antrean' => 'G-01',
    ]);

    Livewire::test('⚡medical-record.poli-gigi', ['record' => $medicalRecord])
        ->assertSet('poliklinik', 'gigi')
        ->assertSee('Pemeriksaan Odontogram');
});

test('clicking a tooth opens the tooth modal and sets active tooth', function () {
    $user = User::first();
    $this->actingAs($user);

    $pasien = Pasien::create([
        'no_rekam_medis' => 'RM-GIGI-0002',
        'nama_pasien' => 'Citra Dewi',
        'nik' => '3201234567890011',
        'tempat_lahir' => 'Bekasi',
        'tanggal_lahir' => '1992-08-30',
        'jenis_kelamin' => 'P',
        'golongan_darah' => 'A',
        'alamat' => 'Jl. Perjuangan No. 2',
        'status_pasien' => 'aktif',
    ]);

    $poli = Poli::where('kode_poli', 'GIG')->first();
    $dokter = MasterPetugas::where('jenis_petugas', 'Dokter')->first();

    $pendaftaran = Pendaftaran::create([
        'no_registrasi' => 'REG-GIGI-0002',
        'pasien_id' => $pasien->id,
        'poli_id' => $poli->id,
        'dokter_id' => $dokter->id,
        'no_antrean' => 'G-02',
        'angka_antrean' => 2,
        'status_antrean' => 'menunggu',
        'cara_bayar' => 'Umum',
        'jenis_kunjungan' => 'Baru',
        'keluhan_awal' => 'Sakit gigi geraham',
    ]);

    $medicalRecord = MedicalRecord::create([
        'encounter_id' => 'ENC-GIGI-0002',
        'patient_id' => $pasien->id,
        'pendaftaran_id' => $pendaftaran->id,
        'poli_id' => $poli->id,
        'status' => 'anamnesis',
        'nomor_antrean' => 'G-02',
    ]);

    Livewire::test('⚡medical-record.poli-gigi', ['record' => $medicalRecord])
        ->call('openTooth', 36)
        ->assertSet('activeTooth', 36)
        ->assertSet('showToothModal', true)
        ->assertSet('activeToothCondition', 'SOU'); // default when no existing condition
});

test('tooth condition can be saved and appears in teethMap', function () {
    $user = User::first();
    $this->actingAs($user);

    $pasien = Pasien::create([
        'no_rekam_medis' => 'RM-GIGI-0003',
        'nama_pasien' => 'Eko Prasetyo',
        'nik' => '3201234567890012',
        'tempat_lahir' => 'Purwakarta',
        'tanggal_lahir' => '1988-02-14',
        'jenis_kelamin' => 'L',
        'golongan_darah' => 'B',
        'alamat' => 'Jl. Kebangsaan No. 7',
        'status_pasien' => 'aktif',
    ]);

    $poli = Poli::where('kode_poli', 'GIG')->first();
    $dokter = MasterPetugas::where('jenis_petugas', 'Dokter')->first();

    $pendaftaran = Pendaftaran::create([
        'no_registrasi' => 'REG-GIGI-0003',
        'pasien_id' => $pasien->id,
        'poli_id' => $poli->id,
        'dokter_id' => $dokter->id,
        'no_antrean' => 'G-03',
        'angka_antrean' => 3,
        'status_antrean' => 'menunggu',
        'cara_bayar' => 'Umum',
        'jenis_kunjungan' => 'Baru',
        'keluhan_awal' => 'Gigi depan patah',
    ]);

    $medicalRecord = MedicalRecord::create([
        'encounter_id' => 'ENC-GIGI-0003',
        'patient_id' => $pasien->id,
        'pendaftaran_id' => $pendaftaran->id,
        'poli_id' => $poli->id,
        'status' => 'anamnesis',
        'nomor_antrean' => 'G-03',
    ]);

    Livewire::test('⚡medical-record.poli-gigi', ['record' => $medicalRecord])
        ->call('openTooth', 11)
        ->set('activeToothCondition', 'CAR')
        ->set('activeToothNotes', 'Karies profunda mesial')
        ->call('saveToothCondition')
        ->assertSet('showToothModal', false)
        ->assertSet('activeTooth', null);
});

test('odontogram data is persisted to database on saveDraft', function () {
    $user = User::first();
    $this->actingAs($user);

    $pasien = Pasien::create([
        'no_rekam_medis' => 'RM-GIGI-0004',
        'nama_pasien' => 'Farida Nur',
        'nik' => '3201234567890013',
        'tempat_lahir' => 'Tasikmalaya',
        'tanggal_lahir' => '1990-09-09',
        'jenis_kelamin' => 'P',
        'golongan_darah' => 'O',
        'alamat' => 'Jl. Pahlawan No. 4',
        'status_pasien' => 'aktif',
    ]);

    $poli = Poli::where('kode_poli', 'GIG')->first();
    $dokter = MasterPetugas::where('jenis_petugas', 'Dokter')->first();

    $pendaftaran = Pendaftaran::create([
        'no_registrasi' => 'REG-GIGI-0004',
        'pasien_id' => $pasien->id,
        'poli_id' => $poli->id,
        'dokter_id' => $dokter->id,
        'no_antrean' => 'G-04',
        'angka_antrean' => 4,
        'status_antrean' => 'menunggu',
        'cara_bayar' => 'BPJS',
        'jenis_kunjungan' => 'Baru',
        'keluhan_awal' => 'Gigi berlubang molar kiri bawah',
    ]);

    $medicalRecord = MedicalRecord::create([
        'encounter_id' => 'ENC-GIGI-0004',
        'patient_id' => $pasien->id,
        'pendaftaran_id' => $pendaftaran->id,
        'poli_id' => $poli->id,
        'status' => 'anamnesis',
        'nomor_antrean' => 'G-04',
    ]);

    Livewire::test('⚡medical-record.poli-gigi', ['record' => $medicalRecord])
        // Simulate user clicking teeth and setting conditions
        ->call('openTooth', 36)
        ->set('activeToothCondition', 'CAR')
        ->set('activeToothNotes', 'Karies gigi 36')
        ->call('saveToothCondition')
        ->call('openTooth', 46)
        ->set('activeToothCondition', 'MIS')
        ->set('activeToothNotes', '')
        ->call('saveToothCondition')
        ->call('saveDraft');

    // Verify persisted to database
    $records = OdontogramRecord::where('medical_record_id', $medicalRecord->id)
        ->orderBy('tooth_number')
        ->get();

    expect($records)->toHaveCount(2)
        ->and($records->firstWhere('tooth_number', 36)->condition_code)->toBe('CAR')
        ->and($records->firstWhere('tooth_number', 36)->notes)->toBe('Karies gigi 36')
        ->and($records->firstWhere('tooth_number', 46)->condition_code)->toBe('MIS');
});

test('odontogram data is preloaded on component mount', function () {
    $user = User::first();
    $this->actingAs($user);

    $pasien = Pasien::create([
        'no_rekam_medis' => 'RM-GIGI-0005',
        'nama_pasien' => 'Gunawan Hadi',
        'nik' => '3201234567890014',
        'tempat_lahir' => 'Garut',
        'tanggal_lahir' => '1975-03-03',
        'jenis_kelamin' => 'L',
        'golongan_darah' => 'A',
        'alamat' => 'Jl. Kartini No. 1',
        'status_pasien' => 'aktif',
    ]);

    $poli = Poli::where('kode_poli', 'GIG')->first();
    $dokter = MasterPetugas::where('jenis_petugas', 'Dokter')->first();

    $pendaftaran = Pendaftaran::create([
        'no_registrasi' => 'REG-GIGI-0005',
        'pasien_id' => $pasien->id,
        'poli_id' => $poli->id,
        'dokter_id' => $dokter->id,
        'no_antrean' => 'G-05',
        'angka_antrean' => 5,
        'status_antrean' => 'menunggu',
        'cara_bayar' => 'Umum',
        'jenis_kunjungan' => 'Lama',
        'keluhan_awal' => 'Kontrol setelah cabut gigi',
    ]);

    $medicalRecord = MedicalRecord::create([
        'encounter_id' => 'ENC-GIGI-0005',
        'patient_id' => $pasien->id,
        'pendaftaran_id' => $pendaftaran->id,
        'poli_id' => $poli->id,
        'status' => 'anamnesis',
        'nomor_antrean' => 'G-05',
    ]);

    // Pre-seed odontogram
    OdontogramRecord::create([
        'medical_record_id' => $medicalRecord->id,
        'tooth_number' => 17,
        'condition_code' => 'FML',
        'notes' => 'Tambal amalgam',
    ]);

    OdontogramRecord::create([
        'medical_record_id' => $medicalRecord->id,
        'tooth_number' => 46,
        'condition_code' => 'MIS',
        'notes' => null,
    ]);

    $component = Livewire::test('⚡medical-record.poli-gigi', ['record' => $medicalRecord]);

    $teethMap = $component->get('teethMap');

    expect($teethMap)->toHaveKey(17)
        ->and($teethMap[17]['condition_code'])->toBe('FML')
        ->and($teethMap[17]['notes'])->toBe('Tambal amalgam')
        ->and($teethMap)->toHaveKey(46)
        ->and($teethMap[46]['condition_code'])->toBe('MIS');
});
