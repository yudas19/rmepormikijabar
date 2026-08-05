<?php

use App\Models\FaskesProfile;
use App\Models\MasterPetugas;
use App\Models\MedicalLetter;
use App\Models\MedicalRecord;
use App\Models\Pasien;
use App\Models\Pendaftaran;
use App\Models\Poli;
use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Livewire\Livewire;

beforeEach(function () {
    $this->seed(DatabaseSeeder::class);

    // Ensure we have a FaskesProfile with ID = 1
    FaskesProfile::updateOrCreate(
        ['id' => 1],
        [
            'nama_faskes' => 'Klinik Pormiki',
            'logo_path' => 'logos/logo.png',
            'alamat' => 'Jl. Raya Perekaman Medis No. 45, Bandung',
            'penanggung_jawab' => 'Dr. Jane Doe',
            'no_telp' => '022-123456',
            'email' => 'info@emrpintar.id',
            'kode_faskes_kemenkes' => '1234567',
        ]
    );
});

test('authenticated doctor can generate a sick leave medical letter', function () {
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

    // Generate Sick Leave Letter using Livewire
    Livewire::test('⚡medical-record.poli-umum', ['record' => $medicalRecord])
        ->set('sick_start_date', '2026-06-14')
        ->set('sick_end_date', '2026-06-16')
        ->set('sick_diagnose', 'Demam Tinggi')
        ->set('sick_dokter_id', $dokter->id)
        ->call('generateSickLeave')
        ->assertDispatched('open-print-tab');

    // Assert it was saved to the database
    $letter = MedicalLetter::where('medical_record_id', $medicalRecord->id)->first();
    expect($letter)->not->toBeNull();
    expect($letter->jenis_surat)->toBe('surat_sakit');
    expect($letter->meta_data['jumlah_hari'])->toBe(3);
    expect($letter->meta_data['alasan'])->toBe('Demam Tinggi');

    // Format unique check: nomor_surat contains /SKU/Klinik/
    expect($letter->nomor_surat)->toContain('/SKU/Klinik/');
});

test('authenticated doctor can generate a health certificate medical letter', function () {
    $user = User::first();
    $this->actingAs($user);

    $pasien = Pasien::create([
        'no_rekam_medis' => 'RM-20261102-0002',
        'nama_pasien' => 'Jane Doe',
        'nik' => '1234567890123457',
        'tempat_lahir' => 'Bandung',
        'tanggal_lahir' => '1995-02-02',
        'jenis_kelamin' => 'P',
        'golongan_darah' => 'O',
        'alamat' => 'Jl. Merdeka No. 15',
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
        'keluhan_awal' => 'Sakit kepala',
    ]);

    $medicalRecord = MedicalRecord::create([
        'encounter_id' => 'ENC-123457',
        'patient_id' => $pasien->id,
        'pendaftaran_id' => $pendaftaran->id,
        'poli_id' => $poli->id,
        'status' => 'waiting',
        'nomor_antrean' => 'A-02',
    ]);

    // Generate Health Certificate using Livewire
    Livewire::test('⚡medical-record.poli-umum', ['record' => $medicalRecord])
        ->set('health_height', 170)
        ->set('health_weight', 65)
        ->set('health_tensi', '120/80')
        ->set('health_golongan_darah', 'O')
        ->set('health_butawarna', 'Tidak')
        ->set('health_catatan', 'Sehat')
        ->set('health_dokter_id', $dokter->id)
        ->call('generateHealthCert')
        ->assertDispatched('open-print-tab');

    // Assert it was saved to the database
    $letter = MedicalLetter::where('medical_record_id', $medicalRecord->id)->first();
    expect($letter)->not->toBeNull();
    expect($letter->jenis_surat)->toBe('surat_sehat');
    expect($letter->meta_data['tinggi_badan'])->toBe(170);
    expect($letter->meta_data['berat_badan'])->toBe(65);
    expect($letter->meta_data['golongan_darah'])->toBe('O');
    expect($letter->meta_data['buta_warna'])->toBe('Status' === 'Tidak' ? 'Ya' : 'Tidak'); // Actually 'Tidak' as it is set
    expect($letter->meta_data['kesimpulan'])->toBe('Sehat');
});

test('print route renders correctly with faskes header information', function () {
    $user = User::first();
    $this->actingAs($user);

    $pasien = Pasien::create([
        'no_rekam_medis' => 'RM-20261102-0003',
        'nama_pasien' => 'Alice',
        'nik' => '1234567890123458',
        'tempat_lahir' => 'Bandung',
        'tanggal_lahir' => '1998-03-03',
        'jenis_kelamin' => 'P',
        'golongan_darah' => 'B',
        'alamat' => 'Jl. Dago No. 10',
        'status_pasien' => 'aktif',
    ]);

    $poli = Poli::where('kode_poli', 'UMU')->first();
    $dokter = MasterPetugas::where('jenis_petugas', 'Dokter')->first();

    $pendaftaran = Pendaftaran::create([
        'no_registrasi' => 'REG-123458',
        'pasien_id' => $pasien->id,
        'poli_id' => $poli->id,
        'dokter_id' => $dokter->id,
        'no_antrean' => 'A-03',
        'angka_antrean' => 3,
        'status_antrean' => 'menunggu',
        'cara_bayar' => 'Umum',
        'jenis_kunjungan' => 'Baru',
        'keluhan_awal' => 'Batuk pilek',
    ]);

    $medicalRecord = MedicalRecord::create([
        'encounter_id' => 'ENC-123458',
        'patient_id' => $pasien->id,
        'pendaftaran_id' => $pendaftaran->id,
        'poli_id' => $poli->id,
        'status' => 'waiting',
        'nomor_antrean' => 'A-03',
    ]);

    $letter = MedicalLetter::create([
        'medical_record_id' => $medicalRecord->id,
        'pasien_id' => $pasien->id,
        'dokter_id' => $dokter->id,
        'nomor_surat' => '001/SKU/Klinik/VI/2026',
        'jenis_surat' => 'surat_sehat',
        'meta_data' => [
            'tinggi_badan' => 170,
            'berat_badan' => 65,
            'golongan_darah' => 'O',
            'buta_warna' => 'Tidak',
            'kesimpulan' => 'Sehat / Tidak Sehat',
        ],
    ]);

    $response = $this->get(route('medical-letters.print', ['id' => $letter->id]));

    $response->assertOk();
    $response->assertSee('SURAT KETERANGAN SEHAT');
    $response->assertSee('Klinik Pormiki'); // from faskes_profiles
    $response->assertSee('Alice'); // patient name
    $response->assertSee('dr. '.$dokter->nama_petugas); // doctor name
});
