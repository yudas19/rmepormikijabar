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

    FaskesProfile::updateOrCreate(
        ['id' => 1],
        [
            'nama_faskes' => 'Klinik Pormiki',
            'logo_path' => 'logos/logo.png',
            'alamat' => 'Jl. Raya Perekaman Medis No. 45, Bandung',
            'penanggung_jawab' => 'Dr. PMIK',
            'no_telp' => '022-123456',
            'email' => 'info@pormiki.id',
            'kode_faskes_kemenkes' => '1234567',
        ]
    );
});

test('authenticated user can access the archive log page', function () {
    $user = User::first();
    $this->actingAs($user);

    $this->get(route('admin.daftar-surat'))
        ->assertOk()
        ->assertSee('ARSIP SURAT KETERANGAN');
});

test('archive log page lists generated letters and allows filtering', function () {
    $user = User::first();
    $this->actingAs($user);

    $pasien1 = Pasien::create([
        'no_rekam_medis' => 'RM-20261102-9901',
        'nama_pasien' => 'Budiono Siregar',
        'nik' => '1234567890123450',
        'tempat_lahir' => 'Bandung',
        'tanggal_lahir' => '1990-01-01',
        'jenis_kelamin' => 'L',
        'golongan_darah' => 'O',
        'alamat' => 'Jl. Merdeka No. 12',
        'status_pasien' => 'aktif',
    ]);

    $pasien2 = Pasien::create([
        'no_rekam_medis' => 'RM-20261102-9902',
        'nama_pasien' => 'Siti Aminah',
        'nik' => '1234567890123451',
        'tempat_lahir' => 'Bandung',
        'tanggal_lahir' => '1995-02-02',
        'jenis_kelamin' => 'P',
        'golongan_darah' => 'A',
        'alamat' => 'Jl. Merdeka No. 15',
        'status_pasien' => 'aktif',
    ]);

    $poli = Poli::where('kode_poli', 'UMU')->first();
    $dokter = MasterPetugas::where('jenis_petugas', 'Dokter')->first();

    $pendaftaran1 = Pendaftaran::create([
        'no_registrasi' => 'REG-999001',
        'pasien_id' => $pasien1->id,
        'poli_id' => $poli->id,
        'dokter_id' => $dokter->id,
        'no_antrean' => 'A-01',
        'angka_antrean' => 1,
        'status_antrean' => 'menunggu',
        'cara_bayar' => 'Umum',
        'jenis_kunjungan' => 'Baru',
        'keluhan_awal' => 'Demam',
    ]);

    $medicalRecord1 = MedicalRecord::create([
        'encounter_id' => 'ENC-999001',
        'patient_id' => $pasien1->id,
        'pendaftaran_id' => $pendaftaran1->id,
        'poli_id' => $poli->id,
        'status' => 'waiting',
        'nomor_antrean' => 'A-01',
    ]);

    // Create letters
    $letter1 = MedicalLetter::create([
        'medical_record_id' => $medicalRecord1->id,
        'pasien_id' => $pasien1->id,
        'dokter_id' => $dokter->id,
        'nomor_surat' => '001/SKU/Klinik/VI/2026',
        'jenis_surat' => 'surat_sakit',
        'meta_data' => [
            'jumlah_hari' => 3,
            'dari_tanggal' => '2026-06-16',
            'sampai_tanggal' => '2026-06-18',
            'alasan' => 'Demam',
        ],
    ]);

    $letter2 = MedicalLetter::create([
        'medical_record_id' => $medicalRecord1->id,
        'pasien_id' => $pasien2->id,
        'dokter_id' => $dokter->id,
        'nomor_surat' => '002/SKU/Klinik/VI/2026',
        'jenis_surat' => 'surat_sehat',
        'meta_data' => [
            'tinggi_badan' => 170,
            'berat_badan' => 65,
            'golongan_darah' => 'O',
            'buta_warna' => 'Tidak',
            'kesimpulan' => 'Sehat',
        ],
    ]);

    // Test Livewire searching & date filtering
    Livewire::test('admin.daftar-surat')
        ->assertSee('Budiono Siregar')
        ->assertSee('Siti Aminah')
        ->set('searchQuery', 'Budiono')
        ->assertSee('Budiono Siregar')
        ->assertDontSee('Siti Aminah')
        ->set('searchQuery', '')
        ->set('dateFilter', '2026-06-16')
        ->assertStatus(200);
});
