<?php

use App\Models\MasterPetugas;
use App\Models\MedicalRecord;
use App\Models\Pasien;
use App\Models\Pendaftaran;
use App\Models\Poli;
use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Livewire\Livewire;
use Spatie\Permission\Models\Role;

beforeEach(function () {
    $this->seed(DatabaseSeeder::class);
});

test('pendaftaran queue filters by date and allows cancel', function () {
    $user = User::first();
    $this->actingAs($user);

    $pasien = Pasien::create([
        'no_rekam_medis' => 'RM-REF-0001',
        'nama_pasien' => 'Anto',
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
        'no_registrasi' => 'REG-REF-0001',
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

    $mrToday = MedicalRecord::create([
        'encounter_id' => 'ENC-REF-0001',
        'patient_id' => $pasien->id,
        'pendaftaran_id' => $pendaftaran->id,
        'poli_id' => $poli->id,
        'status' => 'waiting',
        'nomor_antrean' => 'A-01',
        'tanggal_kunjungan' => date('Y-m-d'),
    ]);

    $mrYesterday = MedicalRecord::create([
        'encounter_id' => 'ENC-REF-0002',
        'patient_id' => $pasien->id,
        'pendaftaran_id' => $pendaftaran->id,
        'poli_id' => $poli->id,
        'status' => 'waiting',
        'nomor_antrean' => 'A-02',
        'tanggal_kunjungan' => date('Y-m-d', strtotime('-1 day')),
    ]);

    // Test livewire pendaftaran date filtering
    Livewire::test('⚡pendaftaran')
        ->set('filterDate', date('Y-m-d'))
        ->assertSee('A-01')
        ->assertDontSee('A-02')
        ->set('filterDate', date('Y-m-d', strtotime('-1 day')))
        ->assertSee('A-02')
        ->assertDontSee('A-01')
        // Cancel the pendaftaran
        ->call('confirmCancel', $mrToday->id)
        ->assertSet('cancelId', $mrToday->id)
        ->assertSet('showCancelConfirmation', true)
        ->call('cancelPendaftaran');

    expect($mrToday->fresh()->status)->toBe('batal');
    expect($pendaftaran->fresh()->status_antrean)->toBe('batal');
});

test('cancelled encounters are hidden from all subsequent active queues', function () {
    $user = User::first();
    $this->actingAs($user);

    $pasien = Pasien::create([
        'no_rekam_medis' => 'RM-REF-0003',
        'nama_pasien' => 'Siti',
        'nik' => '9876543210987654',
        'tempat_lahir' => 'Bandung',
        'tanggal_lahir' => '1990-01-01',
        'jenis_kelamin' => 'P',
        'golongan_darah' => 'O',
        'alamat' => 'Jl. Merdeka No. 12',
        'status_pasien' => 'aktif',
    ]);

    $poli = Poli::where('kode_poli', 'UMU')->first();
    $dokter = MasterPetugas::where('jenis_petugas', 'Dokter')->first();

    $pendaftaran = Pendaftaran::create([
        'no_registrasi' => 'REG-REF-0002',
        'pasien_id' => $pasien->id,
        'poli_id' => $poli->id,
        'dokter_id' => $dokter->id,
        'no_antrean' => 'A-03',
        'angka_antrean' => 3,
        'status_antrean' => 'batal',
        'cara_bayar' => 'Umum',
        'jenis_kunjungan' => 'Baru',
        'keluhan_awal' => 'Flu',
    ]);

    $mrCancelled = MedicalRecord::create([
        'encounter_id' => 'ENC-REF-0003',
        'patient_id' => $pasien->id,
        'pendaftaran_id' => $pendaftaran->id,
        'poli_id' => $poli->id,
        'status' => 'batal',
        'nomor_antrean' => 'A-03',
        'tanggal_kunjungan' => date('Y-m-d'),
    ]);

    // Assert that cancelled patient is not visible in poliklinik-queue
    Livewire::test('poliklinik-queue', ['poliklinik' => 'umum'])
        ->assertDontSee('A-03')
        ->assertDontSee('Siti');
});

test('smart data locking prevents non-admins from editing completed or past 3 days records', function () {
    // Setup roles & user
    $doctorRole = Role::firstOrCreate(['name' => 'dokter']);
    $standardUser = User::factory()->create();
    $standardUser->assignRole($doctorRole);
    // Give standard user access to medical record examine
    $standardUser->givePermissionTo('akses_rekam_medis');

    $adminRole = Role::firstOrCreate(['name' => 'admin']);
    $adminUser = User::factory()->create();
    $adminUser->assignRole($adminRole);
    $adminUser->givePermissionTo('akses_rekam_medis');

    $pasien = Pasien::create([
        'no_rekam_medis' => 'RM-LOCK-0001',
        'nama_pasien' => 'Lock Patient',
        'nik' => '1111111111111111',
        'tempat_lahir' => 'Bandung',
        'tanggal_lahir' => '1990-01-01',
        'jenis_kelamin' => 'L',
        'golongan_darah' => 'O',
        'alamat' => 'Address',
        'status_pasien' => 'aktif',
    ]);

    $poli = Poli::where('kode_poli', 'UMU')->first();
    $dokter = MasterPetugas::where('jenis_petugas', 'Dokter')->first();

    $pendaftaran = Pendaftaran::create([
        'no_registrasi' => 'REG-LOCK-0001',
        'pasien_id' => $pasien->id,
        'poli_id' => $poli->id,
        'dokter_id' => $dokter->id,
        'no_antrean' => 'A-04',
        'angka_antrean' => 4,
        'status_antrean' => 'menunggu',
        'cara_bayar' => 'Umum',
        'jenis_kunjungan' => 'Baru',
        'keluhan_awal' => 'Keluhan',
    ]);

    // 1. Completed record (today)
    $mrCompleted = MedicalRecord::create([
        'encounter_id' => 'ENC-LOCK-0001',
        'patient_id' => $pasien->id,
        'pendaftaran_id' => $pendaftaran->id,
        'poli_id' => $poli->id,
        'status' => 'completed',
        'nomor_antrean' => 'A-04',
        'tanggal_kunjungan' => date('Y-m-d'),
    ]);

    // Standard user should not be able to edit completed record
    $this->actingAs($standardUser);
    Livewire::test('⚡medical-record.poli-umum', ['record' => $mrCompleted])
        ->assertSet('isEditable', false);

    // Admin user should see completed record as locked initially, but can edit via unlock
    $this->actingAs($adminUser);
    Livewire::test('⚡medical-record.poli-umum', ['record' => $mrCompleted])
        ->assertSet('isEditable', false)
        ->set('isEditable', true)
        ->assertSet('isEditable', true);

    // 2. Past 3 days record
    $mrOld = MedicalRecord::create([
        'encounter_id' => 'ENC-LOCK-0002',
        'patient_id' => $pasien->id,
        'pendaftaran_id' => $pendaftaran->id,
        'poli_id' => $poli->id,
        'status' => 'waiting',
        'nomor_antrean' => 'A-05',
        'tanggal_kunjungan' => date('Y-m-d', strtotime('-4 days')),
    ]);

    // Standard user should not be able to edit old record
    $this->actingAs($standardUser);
    Livewire::test('⚡medical-record.poli-umum', ['record' => $mrOld])
        ->assertSet('isEditable', false);

    // Admin user can unlock old record
    $this->actingAs($adminUser);
    Livewire::test('⚡medical-record.poli-umum', ['record' => $mrOld])
        ->assertSet('isEditable', false)
        ->set('isEditable', true)
        ->assertSet('isEditable', true);
});

test('poliklinik queue calling button and TV Display works', function () {
    $user = User::first();
    $this->actingAs($user);

    $pasien = Pasien::create([
        'no_rekam_medis' => 'RM-CALL-0001',
        'nama_pasien' => 'Call Patient',
        'nik' => '2222222222222222',
        'tempat_lahir' => 'Bandung',
        'tanggal_lahir' => '1990-01-01',
        'jenis_kelamin' => 'L',
        'golongan_darah' => 'O',
        'alamat' => 'Address',
        'status_pasien' => 'aktif',
    ]);

    $poli = Poli::where('kode_poli', 'UMU')->first();
    $dokter = MasterPetugas::where('jenis_petugas', 'Dokter')->first();

    $pendaftaran = Pendaftaran::create([
        'no_registrasi' => 'REG-CALL-0001',
        'pasien_id' => $pasien->id,
        'poli_id' => $poli->id,
        'dokter_id' => $dokter->id,
        'no_antrean' => 'A-06',
        'angka_antrean' => 6,
        'status_antrean' => 'menunggu',
        'cara_bayar' => 'Umum',
        'jenis_kunjungan' => 'Baru',
        'keluhan_awal' => 'Keluhan',
    ]);

    $mr = MedicalRecord::create([
        'encounter_id' => 'ENC-CALL-0001',
        'patient_id' => $pasien->id,
        'pendaftaran_id' => $pendaftaran->id,
        'poli_id' => $poli->id,
        'status' => 'waiting',
        'nomor_antrean' => 'A-06',
        'tanggal_kunjungan' => date('Y-m-d'),
        'status_panggilan' => 'belum',
    ]);

    // Test calling from poliklinik-queue
    Livewire::test('poliklinik-queue', ['poliklinik' => 'umum'])
        ->call('panggilAntrean', $mr->id);

    expect($mr->fresh()->status_panggilan)->toBe('memanggil');

    // Test TV Display component polling and transitioning calling status to selesai
    Livewire::test('⚡display-antrean')
        ->call('checkIncomingCall')
        ->assertSet('activeCall.nomor_antrean', 'A-06')
        ->call('markAsDoneCalling', $mr->id)
        ->assertSet('activeCall', null);

    expect($mr->fresh()->status_panggilan)->toBe('selesai');
});
