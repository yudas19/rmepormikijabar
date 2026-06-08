<?php

use App\Models\User;
use Database\Seeders\DatabaseSeeder;

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

    $pasien = \App\Models\Pasien::create([
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

    $poli = \App\Models\Poli::where('kode_poli', 'UMU')->first();
    $dokter = \App\Models\MasterPetugas::where('jenis_petugas', 'Dokter')->first();

    $pendaftaran = \App\Models\Pendaftaran::create([
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

    $medicalRecord = \App\Models\MedicalRecord::create([
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

