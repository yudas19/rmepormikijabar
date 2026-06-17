<?php

use App\Models\MasterAturanPakai;
use App\Models\MasterLabTest;
use App\Models\MasterObat;
use App\Models\MasterPetugas;
use App\Models\MedicalRecord;
use App\Models\Pasien;
use App\Models\Pendaftaran;
use App\Models\Poli;
use App\Models\SuratKeterangan;
use App\Models\SuratPersetujuan;
use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Livewire\Livewire;

beforeEach(function () {
    $this->seed(DatabaseSeeder::class);
});

test('polyclinic component loads search options, rule select options, and handles new certificates/consents correctly', function () {
    $user = User::first();
    $this->actingAs($user);

    $pasien = Pasien::create([
        'no_rekam_medis' => 'RM-20261102-0999',
        'nama_pasien' => 'David Lee',
        'nik' => '9876543210123456',
        'tempat_lahir' => 'Jakarta',
        'tanggal_lahir' => '1985-05-15',
        'jenis_kelamin' => 'L',
        'alamat' => 'Jl. Kebon Jeruk No. 8',
        'status_pasien' => 'aktif',
    ]);

    $poli = Poli::where('kode_poli', 'UMU')->first();
    $dokter = MasterPetugas::where('jenis_petugas', 'Dokter')->first();
    $nurse = MasterPetugas::where('jenis_petugas', 'Perawat')->first() ?? $dokter;

    $pendaftaran = Pendaftaran::create([
        'no_registrasi' => 'REG-999888',
        'pasien_id' => $pasien->id,
        'poli_id' => $poli->id,
        'dokter_id' => $dokter->id,
        'no_antrean' => 'A-99',
        'angka_antrean' => 99,
        'status_antrean' => 'menunggu',
        'cara_bayar' => 'Umum',
        'jenis_kunjungan' => 'Baru',
        'keluhan_awal' => 'Kram perut',
    ]);

    $medicalRecord = MedicalRecord::create([
        'encounter_id' => 'ENC-999888',
        'patient_id' => $pasien->id,
        'pendaftaran_id' => $pendaftaran->id,
        'poli_id' => $poli->id,
        'status' => 'waiting',
        'nomor_antrean' => 'A-99',
    ]);

    // Make sure we have active drugs, lab tests, and rules of consumption
    $obat = MasterObat::first();
    $labTest = MasterLabTest::first();
    $aturanPakai = MasterAturanPakai::first();

    expect($obat)->not->toBeNull();
    expect($labTest)->not->toBeNull();
    expect($aturanPakai)->not->toBeNull();

    // Verify autocomplete search functionality and options selection
    Livewire::test('⚡medical-record.poli-umum', ['record' => $medicalRecord])
        // Drug Search Autocomplete
        ->set('drugQuery', substr($obat->nama_obat, 0, 3))
        ->assertSet('drugResults', function ($results) use ($obat) {
            return collect($results)->contains('id', $obat->id);
        })
        // Lab Search Autocomplete
        ->set('labQuery', substr($labTest->test_name, 0, 3))
        ->assertSet('labResults', function ($results) use ($labTest) {
            return collect($results)->contains('id', $labTest->id);
        })
        // Generate Keterangan Bebas Narkoba
        ->set('narkoba_keperluan', 'Syarat Pekerjaan Baru')
        ->set('narkoba_hasil', 'Negatif Amphetamine')
        ->set('narkoba_dokter_id', $dokter->id)
        ->call('generateNarkoba')
        ->assertDispatched('open-print-tab')
        // Generate Consent Form
        ->set('consent_type', 'general_consent')
        ->set('consent_nama_penanggung_jawab', 'David Lee')
        ->set('consent_hubungan_penanggung_jawab', 'diri_sendiri')
        ->set('consent_nik_penanggung_jawab', '9876543210123456')
        ->set('consent_pernyataan', 'setuju')
        ->set('consent_petugas_id', $nurse->id)
        ->call('generateConsent')
        ->assertDispatched('open-print-tab');

    // Verify Bebas Narkoba certificate was saved in database
    $cert = SuratKeterangan::where('pendaftaran_id', $pendaftaran->id)
        ->where('jenis_surat', 'bebas_narkoba')
        ->first();
    expect($cert)->not->toBeNull();
    expect($cert->konten_surat['keperluan'])->toBe('Syarat Pekerjaan Baru');

    // Verify Consent record was saved in database
    $consent = SuratPersetujuan::where('pendaftaran_id', $pendaftaran->id)
        ->where('jenis_persetujuan', 'general_consent')
        ->first();
    expect($consent)->not->toBeNull();
    expect($consent->nama_penanggung_jawab)->toBe('David Lee');
});
