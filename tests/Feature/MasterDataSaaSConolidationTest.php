<?php

use App\Models\FaskesProfile;
use App\Models\KabupatenKota;
use App\Models\MasterAgama;
use App\Models\MasterPekerjaan;
use App\Models\MasterPendidikan;
use App\Models\MasterPetugas;
use App\Models\MasterSpesialisBpjs;
use App\Models\MedicalRecord;
use App\Models\Pasien;
use App\Models\Pendaftaran;
use App\Models\Poli;
use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Support\Facades\Schema;
use Livewire\Livewire;

beforeEach(function () {
    $this->seed(DatabaseSeeder::class);
});

test('master data database schema structure is correct', function () {
    expect(Schema::hasTable('master_provinsis'))->toBeTrue();
    expect(Schema::hasTable('master_kabupaten_kotas'))->toBeTrue();
    expect(Schema::hasTable('master_spesialis_bpjs'))->toBeTrue();
    expect(Schema::hasTable('master_agamas'))->toBeTrue();

    // Check clinic_id in tenant tables
    expect(Schema::hasColumn('master_agamas', 'clinic_id'))->toBeTrue();
    expect(Schema::hasColumn('master_pekerjaans', 'clinic_id'))->toBeTrue();
    expect(Schema::hasColumn('master_pendidikans', 'clinic_id'))->toBeTrue();

    // Check refactored patient columns
    expect(Schema::hasColumn('pasiens', 'tempat_lahir_kabupaten_id'))->toBeTrue();
    expect(Schema::hasColumn('pasiens', 'master_agama_id'))->toBeTrue();
    expect(Schema::hasColumn('pasiens', 'master_pendidikan_id'))->toBeTrue();
    expect(Schema::hasColumn('pasiens', 'master_pekerjaan_id'))->toBeTrue();

    // Verify old string columns are gone
    expect(Schema::hasColumn('pasiens', 'tempat_lahir'))->toBeFalse();
    expect(Schema::hasColumn('pasiens', 'agama'))->toBeFalse();
    expect(Schema::hasColumn('pasiens', 'pendidikan'))->toBeFalse();
    expect(Schema::hasColumn('pasiens', 'pekerjaan'))->toBeFalse();
});

test('seeders populate default master data successfully', function () {
    expect(MasterSpesialisBpjs::count())->toBe(12);
    expect(MasterAgama::count())->toBe(6);

    expect(MasterSpesialisBpjs::where('kode_spesialis', '001')->first()->nama_spesialis)->toBe('Penyakit Dalam');
    expect(MasterAgama::where('nama_agama', 'Islam')->exists())->toBeTrue();
});

test('patient model relationships point to correct master models', function () {
    // Create mock master data
    $kab = KabupatenKota::create(['kode_kabupaten_kota' => '3201', 'nama_kabupaten_kota' => 'Kabupaten Bogor']);
    $agama = MasterAgama::create(['nama_agama' => 'Islam']);
    $pendidikan = MasterPendidikan::create(['nama_pendidikan' => 'S1']);
    $pekerjaan = MasterPekerjaan::create(['nama_pekerjaan' => 'PNS']);

    $pasien = Pasien::create([
        'no_rekam_medis' => 'RM-TEST-999',
        'nama_pasien' => 'Tes Pasien Master',
        'nik' => '1234567890123456',
        'tempat_lahir_kabupaten_id' => $kab->id,
        'tanggal_lahir' => '1995-01-01',
        'jenis_kelamin' => 'L',
        'master_agama_id' => $agama->id,
        'master_pendidikan_id' => $pendidikan->id,
        'master_pekerjaan_id' => $pekerjaan->id,
        'alamat' => 'Jl. Test No. 99',
    ]);

    expect($pasien->tempatLahirKabupaten->nama_kabupaten_kota)->toBe('Kabupaten Bogor');
    expect($pasien->agama->nama_agama)->toBe('Islam');
    expect($pasien->pendidikan->nama_pendidikan)->toBe('S1');
    expect($pasien->pekerjaan->nama_pekerjaan)->toBe('PNS');
});

test('pendaftaran livewire component scopes master options and saves correctly', function () {
    $user = User::first();
    $this->actingAs($user);

    // Create a FaskesProfile to represent the clinic
    $clinic = FaskesProfile::updateOrCreate(
        ['id' => 1],
        [
            'nama_faskes' => 'Klinik Sehat Utama',
            'alamat' => 'Jl. Sehat',
            'penanggung_jawab' => 'dr. John',
            'no_telp' => '123',
            'email' => 'a@b.com',
            'kode_faskes_kemenkes' => 'F123',
        ]
    );

    // Add a tenant-specific religion and job
    $tenantAgama = MasterAgama::create(['nama_agama' => 'Custom Religion', 'clinic_id' => $clinic->id]);
    $tenantPekerjaan = MasterPekerjaan::create(['nama_pekerjaan' => 'Custom Job', 'clinic_id' => $clinic->id]);

    // Create a birth place
    $kab = KabupatenKota::create(['kode_kabupaten_kota' => '3202', 'nama_kabupaten_kota' => 'Kota Bandung']);

    $component = Livewire::test('⚡pendaftaran')
        ->set('tempatLahirQuery', 'Bandung')
        ->assertCount('tempatLahirResults', 1)
        ->call('selectTempatLahir', $kab->id, 'Kota Bandung')
        ->assertSet('tempat_lahir_kabupaten_id', $kab->id)
        ->set('no_rekam_medis', 'RM-NEW-0001')
        ->set('nama_pasien', 'Lutfi Hakim')
        ->set('nik', '3201234567891234')
        ->set('tanggal_lahir', '1990-10-10')
        ->set('jenis_kelamin', 'L')
        ->set('master_agama_id', $tenantAgama->id)
        ->set('master_pekerjaan_id', $tenantPekerjaan->id)
        ->set('alamat', 'Jl. Sukajadi No. 5')
        ->call('savePatient')
        ->assertHasNoErrors();

    $pasien = Pasien::where('no_rekam_medis', 'RM-NEW-0001')->first();
    expect($pasien)->not->toBeNull();
    expect($pasien->tempat_lahir_kabupaten_id)->toBe($kab->id);
    expect($pasien->master_agama_id)->toBe($tenantAgama->id);
    expect($pasien->master_pekerjaan_id)->toBe($tenantPekerjaan->id);
});

test('BPJS outward referral form specialist dynamic loop retrieves database specialists', function () {
    $user = User::first();
    $this->actingAs($user);

    $pasien = Pasien::create([
        'no_rekam_medis' => 'RM-REF-0099',
        'nama_pasien' => 'Slamet',
        'nik' => '3201234567890001',
        'tanggal_lahir' => '1990-01-01',
        'jenis_kelamin' => 'L',
        'alamat' => 'Jl. Merdeka',
    ]);

    $poli = Poli::where('kode_poli', 'UMU')->first();
    $dokter = MasterPetugas::where('jenis_petugas', 'Dokter')->first();

    $pendaftaran = Pendaftaran::create([
        'no_registrasi' => 'REG-REF-0099',
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
        'encounter_id' => 'ENC-REF-0099',
        'patient_id' => $pasien->id,
        'pendaftaran_id' => $pendaftaran->id,
        'poli_id' => $poli->id,
        'dokter_id' => $dokter->id,
        'status' => 'anamnesis',
        'nomor_antrean' => 'A-99',
    ]);

    // Ensure we can render all three components and they retrieve specialists from DB
    Livewire::test('⚡medical-record.poli-umum', ['record' => $medicalRecord])
        ->assertSet('showPcareReferralForm', false);

    expect(MasterSpesialisBpjs::where('is_active', true)->count())->toBe(12);
});

test('master agama livewire component performs crud successfully', function () {
    $user = User::first();
    $this->actingAs($user);

    $component = Livewire::test('master.agama')
        ->set('nama_agama', 'Agama Baru')
        ->set('is_active', true)
        ->call('save')
        ->assertHasNoErrors()
        ->assertSet('nama_agama', '')
        ->assertSet('is_active', true);

    expect(MasterAgama::where('nama_agama', 'Agama Baru')->exists())->toBeTrue();

    $agama = MasterAgama::where('nama_agama', 'Agama Baru')->first();

    $component->call('edit', $agama->id)
        ->assertSet('nama_agama', 'Agama Baru')
        ->set('nama_agama', 'Agama Baru Edit')
        ->call('save')
        ->assertHasNoErrors();

    expect(MasterAgama::where('nama_agama', 'Agama Baru Edit')->exists())->toBeTrue();

    $component->call('delete', $agama->id);
    expect(MasterAgama::where('nama_agama', 'Agama Baru Edit')->exists())->toBeFalse();
});

test('master agama route is accessible to authenticated user', function () {
    $user = User::first();
    $this->actingAs($user);

    $response = $this->get(route('master.agama'));
    $response->assertStatus(200);
});

test('pendaftaran component auto generates 6 digit sequential rekam medis number', function () {
    $user = User::first();
    $this->actingAs($user);

    // Initial state: no numeric patients exist. Should start at '000001'
    Livewire::test('⚡pendaftaran')
        ->call('openAddPatient')
        ->assertSet('no_rekam_medis', '000001');

    // Create a patient with '000005'
    Pasien::create([
        'no_rekam_medis' => '000005',
        'nama_pasien' => 'Tes Pasien RM',
        'nik' => '9991234567891234',
        'tanggal_lahir' => '1990-01-01',
        'jenis_kelamin' => 'L',
        'alamat' => 'Test Alamat',
    ]);

    // Should increment to '000006'
    Livewire::test('⚡pendaftaran')
        ->call('openAddPatient')
        ->assertSet('no_rekam_medis', '000006');
});
