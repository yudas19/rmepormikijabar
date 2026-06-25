<?php

use App\Models\MasterPcare;
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

test('bpjs pcare referral form state, autocomplete, and payload generation work on poli-umum', function () {
    $user = User::first();
    $this->actingAs($user);

    $pasien = Pasien::create([
        'no_rekam_medis' => 'RM-UM-0099',
        'nama_pasien' => 'Slamet Rahardjo',
        'nik' => '3201234567890099',
        'no_bpjs' => '0002345678901',
        'tempat_lahir' => 'Jakarta',
        'tanggal_lahir' => '1970-01-01',
        'jenis_kelamin' => 'L',
        'alamat' => 'Jl. Kebon Jeruk No. 1',
    ]);

    $poli = Poli::where('kode_poli', 'UMU')->first();
    $dokter = MasterPetugas::where('jenis_petugas', 'Dokter')->first();

    $pendaftaran = Pendaftaran::create([
        'no_registrasi' => 'REG-UM-0099',
        'pasien_id' => $pasien->id,
        'poli_id' => $poli->id,
        'dokter_id' => $dokter->id,
        'no_antrean' => 'A-99',
        'angka_antrean' => 99,
        'status_antrean' => 'menunggu',
        'cara_bayar' => 'BPJS',
        'jenis_kunjungan' => 'Lama',
    ]);

    $medicalRecord = MedicalRecord::create([
        'encounter_id' => 'ENC-UM-0099',
        'patient_id' => $pasien->id,
        'pendaftaran_id' => $pendaftaran->id,
        'poli_id' => $poli->id,
        'dokter_id' => $dokter->id,
        'status' => 'anamnesis',
        'nomor_antrean' => 'A-99',
    ]);

    // Create a mock hospital record
    MasterPcare::create([
        'kode_pcare' => 'PKR-999',
        'nama_pcare' => 'RS Test Pcare Rujukan',
        'kode_rs' => 'RS-999',
        'kode_wilayah' => 'WIL-999',
        'kode_provinsi' => '32',
        'kode_kabupaten' => '3201',
        'kode_kecamatan' => '3201010',
        'nama_propinsi' => 'Jawa Barat',
        'nama_kabupaten' => 'Bogor',
        'nama_kecamatan' => 'Cibinong',
        'kode_faskes' => 'F-999',
        'nama_faskes' => 'RS Harapan Mulia',
    ]);

    $component = Livewire::test('⚡medical-record.poli-umum', ['record' => $medicalRecord])
        ->assertSet('showPcareReferralForm', false)
        ->set('showPcareReferralForm', true)
        ->assertSet('rujuk_tanggal_est', date('Y-m-d'))
        ->set('faskesQuery', 'Harapan')
        ->assertCount('faskesResults', 1)
        ->call('selectFaskes', 'F-999', 'RS Harapan Mulia')
        ->assertSet('rujuk_ppk_kode', 'F-999')
        ->assertSet('faskesQuery', 'RS Harapan Mulia (F-999)')
        ->set('rujuk_spesialis', '001')
        ->set('rujuk_sarana', '001')
        ->set('tensi_sistole', 130)
        ->set('tensi_diastole', 85)
        ->set('pulse_rate', 80)
        ->set('respiratory_rate', 20)
        ->set('temperature', 36.8)
        ->set('weight', 70)
        ->set('height', 170)
        ->set('subjective', 'Kepala pusing sejak kemarin')
        ->set('selectedIcd10s', [
            ['id' => 1, 'kode' => 'I10', 'nama_penyakit' => 'Essential hypertension', 'is_primary' => true],
        ])
        ->set('rujuk_is_tacc', true)
        ->set('rujuk_tacc_jenis', '1')
        ->set('rujuk_tacc_alasan', 'Tekanan darah tinggi persisten')
        ->assertHasNoErrors();

    $payload = $component->instance()->generateReferralPayload();

    expect($payload['patient']['nama'])->toBe('Slamet Rahardjo');
    expect($payload['ttv']['sistole'])->toBe(130);
    expect($payload['soape']['subjective'])->toBe('Kepala pusing sejak kemarin');
    expect($payload['diagnosis']['primary_code'])->toBe('I10');
    expect($payload['referral_parameters']['spesialis_code'])->toBe('001');
    expect($payload['referral_parameters']['ppk_kode'])->toBe('F-999');
    expect($payload['referral_parameters']['is_tacc'])->toBeTrue();
    expect($payload['referral_parameters']['tacc_jenis'])->toBe('1');
    expect($payload['referral_parameters']['tacc_alasan'])->toBe('Tekanan darah tinggi persisten');
});

test('bpjs pcare referral form state, autocomplete, and payload generation work on poli-gigi', function () {
    $user = User::first();
    $this->actingAs($user);

    $pasien = Pasien::create([
        'no_rekam_medis' => 'RM-GI-0099',
        'nama_pasien' => 'Dewi Persik',
        'nik' => '3201234567890088',
        'no_bpjs' => '0002345678902',
        'tempat_lahir' => 'Jakarta',
        'tanggal_lahir' => '1980-01-01',
        'jenis_kelamin' => 'P',
        'alamat' => 'Jl. Kebon Jeruk No. 2',
    ]);

    $poli = Poli::where('kode_poli', 'GIG')->first();
    $dokter = MasterPetugas::where('jenis_petugas', 'Dokter')->first();

    $pendaftaran = Pendaftaran::create([
        'no_registrasi' => 'REG-GI-0099',
        'pasien_id' => $pasien->id,
        'poli_id' => $poli->id,
        'dokter_id' => $dokter->id,
        'no_antrean' => 'G-99',
        'angka_antrean' => 99,
        'status_antrean' => 'menunggu',
        'cara_bayar' => 'BPJS',
        'jenis_kunjungan' => 'Lama',
    ]);

    $medicalRecord = MedicalRecord::create([
        'encounter_id' => 'ENC-GI-0099',
        'patient_id' => $pasien->id,
        'pendaftaran_id' => $pendaftaran->id,
        'poli_id' => $poli->id,
        'dokter_id' => $dokter->id,
        'status' => 'anamnesis',
        'nomor_antrean' => 'G-99',
    ]);

    MasterPcare::create([
        'kode_pcare' => 'PKR-888',
        'nama_pcare' => 'Klinik Gigi Spesialis',
        'kode_rs' => 'RS-888',
        'kode_wilayah' => 'WIL-888',
        'kode_provinsi' => '32',
        'kode_kabupaten' => '3201',
        'kode_kecamatan' => '3201010',
        'nama_propinsi' => 'Jawa Barat',
        'nama_kabupaten' => 'Bogor',
        'nama_kecamatan' => 'Cibinong',
        'kode_faskes' => 'F-888',
        'nama_faskes' => 'Klinik Utama Gigi Sehat',
    ]);

    $component = Livewire::test('⚡medical-record.poli-gigi', ['record' => $medicalRecord])
        ->assertSet('showPcareReferralForm', false)
        ->set('showPcareReferralForm', true)
        ->set('faskesQuery', 'Sehat')
        ->assertCount('faskesResults', 1)
        ->call('selectFaskes', 'F-888', 'Klinik Utama Gigi Sehat')
        ->assertSet('rujuk_ppk_kode', 'F-888')
        ->set('rujuk_spesialis', '012')
        ->set('rujuk_sarana', '001')
        ->set('selectedIcd10s', [
            ['id' => 2, 'kode' => 'K02', 'nama_penyakit' => 'Dental caries', 'is_primary' => true],
        ])
        ->assertHasNoErrors();

    $payload = $component->instance()->generateReferralPayload();
    expect($payload['diagnosis']['primary_code'])->toBe('K02');
    expect($payload['referral_parameters']['spesialis_code'])->toBe('012');
    expect($payload['referral_parameters']['ppk_kode'])->toBe('F-888');
});

test('bpjs pcare referral form state, autocomplete, and payload generation work on poli-kia', function () {
    $user = User::first();
    $this->actingAs($user);

    $pasien = Pasien::create([
        'no_rekam_medis' => 'RM-KIA-0099',
        'nama_pasien' => 'Kartika Sari',
        'nik' => '3201234567890077',
        'no_bpjs' => '0002345678903',
        'tempat_lahir' => 'Jakarta',
        'tanggal_lahir' => '1990-01-01',
        'jenis_kelamin' => 'P',
        'alamat' => 'Jl. Kebon Jeruk No. 3',
    ]);

    $poli = Poli::where('kode_poli', 'KIA')->first();
    $dokter = MasterPetugas::where('jenis_petugas', 'Dokter')->first();

    $pendaftaran = Pendaftaran::create([
        'no_registrasi' => 'REG-KIA-0099',
        'pasien_id' => $pasien->id,
        'poli_id' => $poli->id,
        'dokter_id' => $dokter->id,
        'no_antrean' => 'K-99',
        'angka_antrean' => 99,
        'status_antrean' => 'menunggu',
        'cara_bayar' => 'BPJS',
        'jenis_kunjungan' => 'Lama',
    ]);

    $medicalRecord = MedicalRecord::create([
        'encounter_id' => 'ENC-KIA-0099',
        'patient_id' => $pasien->id,
        'pendaftaran_id' => $pendaftaran->id,
        'poli_id' => $poli->id,
        'dokter_id' => $dokter->id,
        'status' => 'anamnesis',
        'nomor_antrean' => 'K-99',
    ]);

    MasterPcare::create([
        'kode_pcare' => 'PKR-777',
        'nama_pcare' => 'RS Bersalin Kasih Ibu',
        'kode_rs' => 'RS-777',
        'kode_wilayah' => 'WIL-777',
        'kode_provinsi' => '32',
        'kode_kabupaten' => '3201',
        'kode_kecamatan' => '3201010',
        'nama_propinsi' => 'Jawa Barat',
        'nama_kabupaten' => 'Bogor',
        'nama_kecamatan' => 'Cibinong',
        'kode_faskes' => 'F-777',
        'nama_faskes' => 'RSB Kasih Ibu',
    ]);

    $component = Livewire::test('⚡medical-record.poli-kia', ['record' => $medicalRecord])
        ->assertSet('showPcareReferralForm', false)
        ->set('showPcareReferralForm', true)
        ->set('faskesQuery', 'Kasih')
        ->assertCount('faskesResults', 1)
        ->call('selectFaskes', 'F-777', 'RSB Kasih Ibu')
        ->assertSet('rujuk_ppk_kode', 'F-777')
        ->set('rujuk_spesialis', '003')
        ->set('rujuk_sarana', '001')
        ->set('selectedIcd10s', [
            ['id' => 3, 'kode' => 'O80', 'nama_penyakit' => 'Single spontaneous delivery', 'is_primary' => true],
        ])
        ->assertHasNoErrors();

    $payload = $component->instance()->generateReferralPayload();
    expect($payload['diagnosis']['primary_code'])->toBe('O80');
    expect($payload['referral_parameters']['spesialis_code'])->toBe('003');
    expect($payload['referral_parameters']['ppk_kode'])->toBe('F-777');
});
