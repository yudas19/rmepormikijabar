<?php

use App\Models\KiaAncRecord;
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

test('KIA ANC section is rendered for kia poliklinik', function () {
    $user = User::first();
    $this->actingAs($user);

    $pasien = Pasien::create([
        'no_rekam_medis' => 'RM-KIA-0001',
        'nama_pasien' => 'Siti Hamil',
        'nik' => '3201234567890002',
        'tempat_lahir' => 'Karawang',
        'tanggal_lahir' => '1998-05-15',
        'jenis_kelamin' => 'P',
        'golongan_darah' => 'A',
        'alamat' => 'Jl. Bougainville No. 5',
        'status_pasien' => 'aktif',
    ]);

    $poli = Poli::where('kode_poli', 'KIA')->first();
    $dokter = MasterPetugas::where('jenis_petugas', 'Dokter')->first();

    $pendaftaran = Pendaftaran::create([
        'no_registrasi' => 'REG-KIA-0001',
        'pasien_id' => $pasien->id,
        'poli_id' => $poli->id,
        'dokter_id' => $dokter->id,
        'no_antrean' => 'K-01',
        'angka_antrean' => 1,
        'status_antrean' => 'menunggu',
        'cara_bayar' => 'BPJS',
        'jenis_kunjungan' => 'Lama',
        'keluhan_awal' => 'Kontrol kehamilan',
    ]);

    $medicalRecord = MedicalRecord::create([
        'encounter_id' => 'ENC-KIA-0001',
        'patient_id' => $pasien->id,
        'pendaftaran_id' => $pendaftaran->id,
        'poli_id' => $poli->id,
        'status' => 'waiting',
        'nomor_antrean' => 'K-01',
    ]);

    Livewire::test('⚡medical-record.poli-umum', ['record' => $medicalRecord])
        ->assertSet('poliklinik', 'kia')
        ->assertSee('Pemeriksaan ANC');
});

test('ANC HPHT update auto-calculates taksiran persalinan via Naegele Rule', function () {
    $user = User::first();
    $this->actingAs($user);

    $pasien = Pasien::create([
        'no_rekam_medis' => 'RM-KIA-0002',
        'nama_pasien' => 'Ani Rahayu',
        'nik' => '3201234567890003',
        'tempat_lahir' => 'Bandung',
        'tanggal_lahir' => '2000-03-10',
        'jenis_kelamin' => 'P',
        'golongan_darah' => 'B',
        'alamat' => 'Jl. Cempaka No. 7',
        'status_pasien' => 'aktif',
    ]);

    $poli = Poli::where('kode_poli', 'KIA')->first();
    $dokter = MasterPetugas::where('jenis_petugas', 'Dokter')->first();

    $pendaftaran = Pendaftaran::create([
        'no_registrasi' => 'REG-KIA-0002',
        'pasien_id' => $pasien->id,
        'poli_id' => $poli->id,
        'dokter_id' => $dokter->id,
        'no_antrean' => 'K-02',
        'angka_antrean' => 2,
        'status_antrean' => 'menunggu',
        'cara_bayar' => 'BPJS',
        'jenis_kunjungan' => 'Baru',
        'keluhan_awal' => 'ANC pertama',
    ]);

    $medicalRecord = MedicalRecord::create([
        'encounter_id' => 'ENC-KIA-0002',
        'patient_id' => $pasien->id,
        'pendaftaran_id' => $pendaftaran->id,
        'poli_id' => $poli->id,
        'status' => 'anamnesis',
        'nomor_antrean' => 'K-02',
    ]);

    // HPHT = 2026-01-01 → Naegele TP = 2026-10-08 (Jan+7=8, Jan-3=Oct, year+1=2027... wait)
    // Naegele: Day+7, Month-3, Year+1 → 2026-01-01 → 01+7=08, Jan(1)-3=-2(Oct prev year no)...
    // Actually: HPHT 2026-01-01 → addDays(7)=2026-01-08 → subMonths(3)=2025-10-08 → addYear=2026-10-08
    $hpht = '2026-01-01';
    $expectedTp = '2026-10-08'; // 2026-01-01 + 7days - 3months + 1year

    Livewire::test('⚡medical-record.poli-umum', ['record' => $medicalRecord])
        ->set('anc_hpht', $hpht)
        ->assertSet('anc_tp', $expectedTp);
});

test('ANC data is saved and persisted correctly on saveDraft', function () {
    $user = User::first();
    $this->actingAs($user);

    $pasien = Pasien::create([
        'no_rekam_medis' => 'RM-KIA-0003',
        'nama_pasien' => 'Dewi Sartika',
        'nik' => '3201234567890004',
        'tempat_lahir' => 'Jakarta',
        'tanggal_lahir' => '1995-07-20',
        'jenis_kelamin' => 'P',
        'golongan_darah' => 'O',
        'alamat' => 'Jl. Mawar No. 3',
        'status_pasien' => 'aktif',
    ]);

    $poli = Poli::where('kode_poli', 'KIA')->first();
    $dokter = MasterPetugas::where('jenis_petugas', 'Dokter')->first();

    $pendaftaran = Pendaftaran::create([
        'no_registrasi' => 'REG-KIA-0003',
        'pasien_id' => $pasien->id,
        'poli_id' => $poli->id,
        'dokter_id' => $dokter->id,
        'no_antrean' => 'K-03',
        'angka_antrean' => 3,
        'status_antrean' => 'menunggu',
        'cara_bayar' => 'Umum',
        'jenis_kunjungan' => 'Baru',
        'keluhan_awal' => 'Mual pagi hari',
    ]);

    $medicalRecord = MedicalRecord::create([
        'encounter_id' => 'ENC-KIA-0003',
        'patient_id' => $pasien->id,
        'pendaftaran_id' => $pendaftaran->id,
        'poli_id' => $poli->id,
        'status' => 'anamnesis',
        'nomor_antrean' => 'K-03',
    ]);

    Livewire::test('⚡medical-record.poli-umum', ['record' => $medicalRecord])
        ->set('anc_hpht', '2026-01-01')
        ->set('anc_tfu', '28.5')
        ->set('anc_lila', '24.0')
        ->set('anc_djj', '148')
        ->set('anc_presentasi', 'Kepala')
        ->set('anc_leopold_1', 'Teraba kepala di fundus')
        ->set('anc_catatan_bidan', 'Kehamilan normal, lanjutkan ANC')
        ->call('saveDraft');

    $anc = KiaAncRecord::where('medical_record_id', $medicalRecord->id)->first();

    expect($anc)->not->toBeNull()
        ->and((string) $anc->hpht->format('Y-m-d'))->toBe('2026-01-01')
        ->and((float) $anc->tfu)->toBe(28.5)
        ->and((float) $anc->lila)->toBe(24.0)
        ->and($anc->djj)->toBe(148)
        ->and($anc->presentasi)->toBe('Kepala')
        ->and($anc->leopold_1)->toBe('Teraba kepala di fundus')
        ->and($anc->catatan_bidan)->toBe('Kehamilan normal, lanjutkan ANC');
});

test('ANC data is preloaded on component mount', function () {
    $user = User::first();
    $this->actingAs($user);

    $pasien = Pasien::create([
        'no_rekam_medis' => 'RM-KIA-0004',
        'nama_pasien' => 'Rina Kusuma',
        'nik' => '3201234567890005',
        'tempat_lahir' => 'Bogor',
        'tanggal_lahir' => '1993-11-11',
        'jenis_kelamin' => 'P',
        'golongan_darah' => 'AB',
        'alamat' => 'Jl. Kenanga No. 9',
        'status_pasien' => 'aktif',
    ]);

    $poli = Poli::where('kode_poli', 'KIA')->first();
    $dokter = MasterPetugas::where('jenis_petugas', 'Dokter')->first();

    $pendaftaran = Pendaftaran::create([
        'no_registrasi' => 'REG-KIA-0004',
        'pasien_id' => $pasien->id,
        'poli_id' => $poli->id,
        'dokter_id' => $dokter->id,
        'no_antrean' => 'K-04',
        'angka_antrean' => 4,
        'status_antrean' => 'menunggu',
        'cara_bayar' => 'Umum',
        'jenis_kunjungan' => 'Lama',
        'keluhan_awal' => 'Kontrol rutin',
    ]);

    $medicalRecord = MedicalRecord::create([
        'encounter_id' => 'ENC-KIA-0004',
        'patient_id' => $pasien->id,
        'pendaftaran_id' => $pendaftaran->id,
        'poli_id' => $poli->id,
        'status' => 'anamnesis',
        'nomor_antrean' => 'K-04',
    ]);

    // Pre-create ANC record
    KiaAncRecord::create([
        'medical_record_id' => $medicalRecord->id,
        'hpht' => '2025-12-01',
        'tp' => '2026-09-08',
        'tfu' => 30.0,
        'djj' => 150,
        'presentasi' => 'Kepala',
    ]);

    Livewire::test('⚡medical-record.poli-umum', ['record' => $medicalRecord])
        ->assertSet('anc_hpht', '2025-12-01')
        ->assertSet('anc_tp', '2026-09-08')
        ->assertSet('anc_djj', '150')
        ->assertSet('anc_presentasi', 'Kepala');
});
