<?php

use App\Models\MasterIcd10;
use App\Models\MasterIcd9;
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

test('guests are redirected to the login page for polyclinic and services', function () {
    $this->get(route('poli.queue', ['poliklinik' => 'umum']))
        ->assertRedirect(route('login'));

    $this->get(route('layanan.laboratorium'))
        ->assertRedirect(route('login'));

    $this->get(route('layanan.farmasi'))
        ->assertRedirect(route('login'));
});

test('authenticated users can visit the polyclinic queue pages', function () {
    $user = User::first(); // seeded user with role/permissions if needed
    $this->actingAs($user);

    $this->get(route('poli.queue', ['poliklinik' => 'umum']))
        ->assertOk();

    $this->get(route('poli.queue', ['poliklinik' => 'gigi']))
        ->assertOk();

    $this->get(route('poli.queue', ['poliklinik' => 'kia']))
        ->assertOk();
});

test('authenticated users can visit the laboratorium and farmasi pages', function () {
    $user = User::first();
    $this->actingAs($user);

    $this->get(route('layanan.laboratorium'))
        ->assertOk();

    $this->get(route('layanan.farmasi'))
        ->assertOk();
});

test('invalid polyclinic parameter returns 404', function () {
    $user = User::first();
    $this->actingAs($user);

    $this->get(route('poli.queue', ['poliklinik' => 'invalid-poli']))
        ->assertStatus(404);
});

test('authenticated users can visit the examine page', function () {
    $user = User::first();
    $this->actingAs($user);

    $pasien = Pasien::create([
        'no_rekam_medis' => 'RM-20261102-0001',
        'nama_pasien' => 'John Doe',
        'nik' => '1234567890123456',
        'tempat_lahir' => 'Bandung',
        'tanggal_lahir' => '1990-01-01',
        'jenis_kelamin' => 'L',
        'golongan_darah' => 'O',
        'alamat' => 'Jl. Merdeka No. 12',
        'status_pasien' => 'aktif',
    ]);

    $poli = Poli::where('kode_poli', 'UMU')->first();
    $dokter = MasterPetugas::where('jenis_petugas', 'Dokter')->first();

    $pendaftaran = Pendaftaran::create([
        'no_registrasi' => 'REG-123456',
        'pasien_id' => $pasien->id,
        'poli_id' => $poli->id,
        'dokter_id' => $dokter->id,
        'no_antrean' => 'A-01',
        'angka_antrean' => 1,
        'status_antrean' => 'menunggu',
        'cara_bayar' => 'Umum',
        'jenis_kunjungan' => 'Baru',
        'keluhan_awal' => 'Demam',
    ]);

    $medicalRecord = MedicalRecord::create([
        'encounter_id' => 'ENC-123456',
        'patient_id' => $pasien->id,
        'pendaftaran_id' => $pendaftaran->id,
        'poli_id' => $poli->id,
        'status' => 'waiting',
        'nomor_antrean' => 'A-01',
    ]);

    $response = $this->get(route('medical-record.examine', [
        'poliklinik' => 'umum',
        'encounter_id' => $medicalRecord->encounter_id,
    ]));

    $response->assertOk();
    $response->assertSeeLivewire('⚡medical-record.poli-umum');
});

test('authenticated users can save consolidated soap data in workspace', function () {
    $user = User::first();
    $this->actingAs($user);

    $pasien = Pasien::create([
        'no_rekam_medis' => 'RM-20261102-0001',
        'nama_pasien' => 'John Doe',
        'nik' => '1234567890123456',
        'tempat_lahir' => 'Bandung',
        'tanggal_lahir' => '1990-01-01',
        'jenis_kelamin' => 'L',
        'golongan_darah' => 'O',
        'alamat' => 'Jl. Merdeka No. 12',
        'status_pasien' => 'aktif',
    ]);

    $poli = Poli::where('kode_poli', 'UMU')->first();
    $dokter = MasterPetugas::where('jenis_petugas', 'Dokter')->first();

    $pendaftaran = Pendaftaran::create([
        'no_registrasi' => 'REG-123456',
        'pasien_id' => $pasien->id,
        'poli_id' => $poli->id,
        'dokter_id' => $dokter->id,
        'no_antrean' => 'A-01',
        'angka_antrean' => 1,
        'status_antrean' => 'menunggu',
        'cara_bayar' => 'Umum',
        'jenis_kunjungan' => 'Baru',
        'keluhan_awal' => 'Demam Awal',
    ]);

    $medicalRecord = MedicalRecord::create([
        'encounter_id' => 'ENC-123456',
        'patient_id' => $pasien->id,
        'pendaftaran_id' => $pendaftaran->id,
        'poli_id' => $poli->id,
        'status' => 'waiting',
        'nomor_antrean' => 'A-01',
    ]);

    // Test Livewire component
    Livewire::test('⚡medical-record.poli-umum', ['record' => $medicalRecord])
        ->assertSet('keluhan_utama', 'Demam Awal') // should fallback to pendaftaran keluhan_awal
        ->set('keluhan_utama', 'Demam Tinggi 3 Hari')
        ->set('riwayat_alergi', 'Alergi Seafood')
        ->set('subjective', 'Pasien datang mengeluh demam tinggi')
        ->set('objective', 'Suhu 39C, tensi normal')
        ->set('assessment', 'Febris Susp. DHF')
        ->set('plan', 'Cek darah lengkap, paracetamol 3x1')
        ->call('saveDraft');

    // Verify database has updated columns on the consolidated table
    $medicalRecord->refresh();
    expect($medicalRecord->keluhan_utama)->toBe('Demam Tinggi 3 Hari');
    expect($medicalRecord->riwayat_alergi)->toBe('Alergi Seafood');
    expect($medicalRecord->subjective)->toBe('Pasien datang mengeluh demam tinggi');
});

test('authenticated users can search and select ICD-10 and ICD-9 codes in workspace', function () {
    $user = User::first();
    $this->actingAs($user);

    $pasien = Pasien::create([
        'no_rekam_medis' => 'RM-20261102-0002',
        'nama_pasien' => 'Jane Doe',
        'nik' => '1234567890123457',
        'tempat_lahir' => 'Jakarta',
        'tanggal_lahir' => '1995-02-02',
        'jenis_kelamin' => 'P',
        'golongan_darah' => 'A',
        'alamat' => 'Jl. Sudirman No. 1',
        'status_pasien' => 'aktif',
    ]);

    $poli = Poli::where('kode_poli', 'UMU')->first();
    $dokter = MasterPetugas::where('jenis_petugas', 'Dokter')->first();

    $pendaftaran = Pendaftaran::create([
        'no_registrasi' => 'REG-123457',
        'pasien_id' => $pasien->id,
        'poli_id' => $poli->id,
        'dokter_id' => $dokter->id,
        'no_antrean' => 'A-02',
        'angka_antrean' => 2,
        'status_antrean' => 'menunggu',
        'cara_bayar' => 'Umum',
        'jenis_kunjungan' => 'Baru',
        'keluhan_awal' => 'Flu',
    ]);

    $medicalRecord = MedicalRecord::create([
        'encounter_id' => 'ENC-123457',
        'patient_id' => $pasien->id,
        'pendaftaran_id' => $pendaftaran->id,
        'poli_id' => $poli->id,
        'status' => 'waiting',
        'nomor_antrean' => 'A-02',
    ]);

    // Test Livewire component search and select
    Livewire::test('⚡medical-record.poli-umum', ['record' => $medicalRecord])
        ->set('icd10Query', 'J00')
        ->assertCount('icd10Results', 1)
        ->call('selectIcd10', MasterIcd10::where('kode', 'J00')->first()->id)
        ->assertCount('selectedIcd10s', 1)
        ->set('icd9Query', '89.52')
        ->assertCount('icd9Results', 1)
        ->call('selectIcd9', MasterIcd9::where('kode', '89.52')->first()->id)
        ->assertCount('selectedIcd9s', 1)
        ->call('saveDraft');

    // Verify database associations
    $medicalRecord->refresh();
    expect($medicalRecord->icd10s)->toHaveCount(1);
    expect($medicalRecord->icd10s->first()->icd10_code)->toBe('J00');
    expect($medicalRecord->icd9s)->toHaveCount(1);
    expect($medicalRecord->icd9s->first()->icd9_code)->toBe('89.52');
});
