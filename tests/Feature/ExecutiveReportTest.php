<?php

use App\Models\FaskesProfile;
use App\Models\Invoice;
use App\Models\MasterPetugas;
use App\Models\MedicalRecord;
use App\Models\Pasien;
use App\Models\Pendaftaran;
use App\Models\Poli;
use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Support\Facades\DB;

beforeEach(function () {
    $this->seed(DatabaseSeeder::class);

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

test('guests are redirected to the login page for executive reports', function () {
    $this->get('/admin/laporan-eksekutif')
        ->assertRedirect(route('login'));
});

test('authenticated users without admin role receive 403 on reports', function () {
    $user = User::factory()->create();
    $user->assignRole('dokter_umum');
    $this->actingAs($user);

    $this->get('/admin/laporan-eksekutif')
        ->assertStatus(403);
});

test('authenticated admin can access reports and view correct aggregates', function () {
    $user = User::whereHas('roles', function ($q) {
        $q->where('name', 'admin');
    })->first();
    $this->actingAs($user);

    // Seed test patient & records
    $pasien = Pasien::create([
        'no_rekam_medis' => 'RM-20261102-1201',
        'nama_pasien' => 'Budiono',
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
        'no_registrasi' => 'REG-120001',
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
        'encounter_id' => 'ENC-120001',
        'patient_id' => $pasien->id,
        'pendaftaran_id' => $pendaftaran->id,
        'poli_id' => $poli->id,
        'status' => 'waiting',
        'nomor_antrean' => 'A-01',
    ]);

    // Create disease records
    DB::table('medical_record_icd10')->insert([
        [
            'medical_record_id' => $medicalRecord->id,
            'master_icd10_id' => 1,
            'icd10_code' => 'J00',
            'icd10_name' => 'Nasofaringitis Akut (Flu Biasa)',
            'is_primary' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ],
    ]);

    // Create Invoices (one cash, one non-cash paid, one unpaid)
    Invoice::create([
        'medical_record_id' => $medicalRecord->id,
        'invoice_number' => 'INV-2026-0001',
        'subtotal' => 100000,
        'grand_total' => 100000,
        'payment_method' => 'tunai',
        'status' => 'paid',
        'paid_at' => now(),
    ]);

    Invoice::create([
        'medical_record_id' => $medicalRecord->id,
        'invoice_number' => 'INV-2026-0002',
        'subtotal' => 150000,
        'grand_total' => 150000,
        'payment_method' => 'qris',
        'status' => 'paid',
        'paid_at' => now(),
    ]);

    Invoice::create([
        'medical_record_id' => $medicalRecord->id,
        'invoice_number' => 'INV-2026-0003',
        'subtotal' => 50000,
        'grand_total' => 50000,
        'status' => 'unpaid',
    ]);

    $response = $this->get('/admin/laporan-eksekutif');

    $response->assertOk();
    $response->assertSee('LAPORAN EKSEKUTIF DAN ANALISIS');

    // Assert totals are aggregated correctly
    // Total Revenue = 100,000 + 150,000 = 250,000
    // Total Cash = 100,000
    // Total Non-Cash = 150,000
    $response->assertSee('Rp 250.000');
    $response->assertSee('Rp 100.000');
    $response->assertSee('Rp 150.000');

    // Assert disease J00 code is shown
    $response->assertSee('J00');
});
