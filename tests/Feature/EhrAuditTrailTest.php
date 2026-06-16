<?php

use App\Models\FaskesProfile;
use App\Models\MasterPetugas;
use App\Models\MedicalLetter;
use App\Models\MedicalRecord;
use App\Models\MedicalRecordPrescription;
use App\Models\Pasien;
use App\Models\Pendaftaran;
use App\Models\Poli;
use App\Models\SuratKeterangan;
use App\Models\User;
use Database\Seeders\DatabaseSeeder;

beforeEach(function () {
    $this->seed(DatabaseSeeder::class);

    FaskesProfile::updateOrCreate(
        ['id' => 1],
        [
            'nama_faskes' => 'Klinik EMR Pintar Jabar',
            'logo_path' => 'logos/logo.png',
            'alamat' => 'Jl. Raya Perekaman Medis No. 45, Bandung',
            'penanggung_jawab' => 'Dr. Jane Doe',
            'no_telp' => '022-123456',
            'email' => 'info@emrpintar.id',
            'kode_faskes_kemenkes' => '1234567',
        ]
    );
});

test('public user can verify a valid electronic medical letter', function () {
    $pasien = Pasien::create([
        'no_rekam_medis' => 'RM-20261102-9901',
        'nama_pasien' => 'Budiman',
        'nik' => '3273010101900001',
        'tempat_lahir' => 'Bandung',
        'tanggal_lahir' => '1990-01-01',
        'jenis_kelamin' => 'L',
        'alamat' => 'Jl. Pasteur No. 10',
        'status_pasien' => 'aktif',
    ]);

    $dokter = MasterPetugas::where('jenis_petugas', 'Dokter')->first();
    $poli = Poli::first();

    $pendaftaran = Pendaftaran::create([
        'no_registrasi' => 'REG-990001',
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
        'encounter_id' => 'ENC-990001',
        'patient_id' => $pasien->id,
        'pendaftaran_id' => $pendaftaran->id,
        'poli_id' => $poli->id,
        'status' => 'waiting',
        'nomor_antrean' => 'A-01',
    ]);

    $letter = MedicalLetter::create([
        'medical_record_id' => $medicalRecord->id,
        'pasien_id' => $pasien->id,
        'dokter_id' => $dokter->id,
        'nomor_surat' => '005/SKU/Klinik/VI/2026',
        'jenis_surat' => 'surat_sakit',
        'meta_data' => [
            'jumlah_hari' => 3,
            'alasan' => 'Flu',
        ],
    ]);

    $encryptedToken = encrypt('letter-'.$letter->id);

    $response = $this->get(route('document.verify', ['encrypted_id' => $encryptedToken]));

    $response->assertOk();
    $response->assertSee('Budiman');
    $response->assertSee('Klinik EMR Pintar Jabar');
    $response->assertSee('dr. '.$dokter->nama_petugas);
    $response->assertSee('Dokumen Terverifikasi');
    $response->assertSee('Surat Keterangan Sakit');
});

test('public user can verify a valid electronic registration certificate', function () {
    $pasien = Pasien::create([
        'no_rekam_medis' => 'RM-20261102-9902',
        'nama_pasien' => 'Siti Aminah',
        'nik' => '3273010101900002',
        'tempat_lahir' => 'Bandung',
        'tanggal_lahir' => '1995-05-05',
        'jenis_kelamin' => 'P',
        'alamat' => 'Jl. Dago No. 10',
        'status_pasien' => 'aktif',
    ]);

    $dokter = MasterPetugas::where('jenis_petugas', 'Dokter')->first();
    $poli = Poli::first();

    $pendaftaran = Pendaftaran::create([
        'no_registrasi' => 'REG-990002',
        'pasien_id' => $pasien->id,
        'poli_id' => $poli->id,
        'dokter_id' => $dokter->id,
        'no_antrean' => 'A-02',
        'angka_antrean' => 2,
        'status_antrean' => 'menunggu',
        'cara_bayar' => 'Umum',
        'jenis_kunjungan' => 'Baru',
        'keluhan_awal' => 'Pemeriksaan',
    ]);

    $certificate = SuratKeterangan::create([
        'no_surat' => 'SKD/SEHAT/20260616/0001',
        'pendaftaran_id' => $pendaftaran->id,
        'pasien_id' => $pasien->id,
        'dokter_id' => $dokter->id,
        'jenis_surat' => 'sehat',
        'konten_surat' => [
            'tinggi_badan' => 165,
            'berat_badan' => 55,
        ],
    ]);

    $encryptedToken = encrypt('cert-'.$certificate->id);

    $response = $this->get(route('document.verify', ['encrypted_id' => $encryptedToken]));

    $response->assertOk();
    $response->assertSee('Siti Aminah');
    $response->assertSee('dr. '.$dokter->nama_petugas);
    $response->assertSee('Dokumen Terverifikasi');
    $response->assertSee('Surat Keterangan Sehat');
});

test('public user can verify a valid electronic prescription', function () {
    $pasien = Pasien::create([
        'no_rekam_medis' => 'RM-20261102-9903',
        'nama_pasien' => 'Dudi',
        'nik' => '3273010101900003',
        'tempat_lahir' => 'Bandung',
        'tanggal_lahir' => '1988-08-08',
        'jenis_kelamin' => 'L',
        'alamat' => 'Jl. Buah Batu No. 5',
        'status_pasien' => 'aktif',
    ]);

    $dokter = MasterPetugas::where('jenis_petugas', 'Dokter')->first();
    $poli = Poli::first();

    $pendaftaran = Pendaftaran::create([
        'no_registrasi' => 'REG-990003',
        'pasien_id' => $pasien->id,
        'poli_id' => $poli->id,
        'dokter_id' => $dokter->id,
        'no_antrean' => 'A-03',
        'angka_antrean' => 3,
        'status_antrean' => 'menunggu',
        'cara_bayar' => 'Umum',
        'jenis_kunjungan' => 'Baru',
        'keluhan_awal' => 'Batuk',
    ]);

    $medicalRecord = MedicalRecord::create([
        'encounter_id' => 'ENC-990003',
        'patient_id' => $pasien->id,
        'pendaftaran_id' => $pendaftaran->id,
        'poli_id' => $poli->id,
        'status' => 'waiting',
        'nomor_antrean' => 'A-03',
        'dokter_id' => $dokter->id,
    ]);

    $prescription = MedicalRecordPrescription::create([
        'medical_record_id' => $medicalRecord->id,
        'type' => 'non-racikan',
        'aturan_pakai' => '3 x 1 sehari',
    ]);

    $encryptedToken = encrypt('prescription-'.$prescription->id);

    $response = $this->get(route('document.verify', ['encrypted_id' => $encryptedToken]));

    $response->assertOk();
    $response->assertSee('Dudi');
    $response->assertSee('dr. '.$dokter->nama_petugas);
    $response->assertSee('Dokumen Terverifikasi');
    $response->assertSee('Resep Obat');
});

test('public user receives verification error on tampered encrypted tokens', function () {
    $response = $this->get(route('document.verify', ['encrypted_id' => 'invalid-token-string']));

    $response->assertOk();
    $response->assertSee('Verifikasi Gagal');
    $response->assertSee('Dokumen tidak valid atau tanda tangan elektronik telah kedaluwarsa/tampered.');
});

test('polyclinic workspace loads and displays the audit trail info', function () {
    $user = User::first();
    $this->actingAs($user);

    $pasien = Pasien::create([
        'no_rekam_medis' => 'RM-20261102-9904',
        'nama_pasien' => 'Eko',
        'nik' => '3273010101900004',
        'tempat_lahir' => 'Bandung',
        'tanggal_lahir' => '1992-02-02',
        'jenis_kelamin' => 'L',
        'alamat' => 'Jl. Cihampelas No. 2',
        'status_pasien' => 'aktif',
    ]);

    $dokter = MasterPetugas::where('jenis_petugas', 'Dokter')->first();
    $perawat = MasterPetugas::where('jenis_petugas', 'Perawat')->first();
    $poli = Poli::first();

    $pendaftaran = Pendaftaran::create([
        'no_registrasi' => 'REG-990004',
        'pasien_id' => $pasien->id,
        'poli_id' => $poli->id,
        'dokter_id' => $dokter->id,
        'no_antrean' => 'A-04',
        'angka_antrean' => 4,
        'status_antrean' => 'menunggu',
        'cara_bayar' => 'Umum',
        'jenis_kunjungan' => 'Baru',
        'keluhan_awal' => 'Pusing',
    ]);

    $medicalRecord = MedicalRecord::create([
        'encounter_id' => 'ENC-990004',
        'patient_id' => $pasien->id,
        'pendaftaran_id' => $pendaftaran->id,
        'poli_id' => $poli->id,
        'status' => 'waiting',
        'nomor_antrean' => 'A-04',
        'perawat_id' => $perawat->id,
        'dokter_id' => $dokter->id,
    ]);

    $response = $this->get(route('medical-record.examine', [
        'poliklinik' => 'umum',
        'encounter_id' => $medicalRecord->encounter_id,
    ]));

    $response->assertOk();
    $response->assertSee('Diinput oleh:');
    $response->assertSee($perawat->nama_petugas);
    $response->assertSee('Dokter Penanggung Jawab:');
    $response->assertSee('dr. '.$dokter->nama_petugas);
});
