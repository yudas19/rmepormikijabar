<?php

use App\Models\MasterJadwalDokter;
use App\Models\MasterPetugas;
use App\Models\Poli;
use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Livewire\Livewire;

beforeEach(function () {
    $this->seed(DatabaseSeeder::class);
    // Authenticate as admin
    $admin = User::whereHas('roles', function ($q) {
        $q->where('name', 'admin');
    })->first();
    $this->actingAs($admin);
});

test('guests are redirected to login for master jadwal dokter page', function () {
    auth()->logout();
    $this->get(route('master.jadwal-dokter'))
        ->assertRedirect(route('login'));
});

test('authorized users can view master jadwal dokter page', function () {
    $this->get(route('master.jadwal-dokter'))
        ->assertOk();
});

test('it lists active doctors and active polikliniks in selection options', function () {
    // Create active doctor
    $userDoc = User::factory()->create(['name' => 'Dr. Active']);
    $userDoc->assignRole('dokter_umum');
    $doctor = MasterPetugas::create([
        'user_id' => $userDoc->id,
        'nama_petugas' => 'Dr. Active',
        'nik' => '1234567890123411',
        'jenis_petugas' => 'Dokter',
        'is_aktif' => true,
    ]);

    // Create inactive doctor
    $userInactiveDoc = User::factory()->create(['name' => 'Dr. Inactive']);
    $userInactiveDoc->assignRole('dokter_umum');
    $inactiveDoctor = MasterPetugas::create([
        'user_id' => $userInactiveDoc->id,
        'nama_petugas' => 'Dr. Inactive',
        'nik' => '1234567890123422',
        'jenis_petugas' => 'Dokter',
        'is_aktif' => false,
    ]);

    // Create active and inactive polis
    $activePoli = Poli::create([
        'nama_poli' => 'Poli Aktif',
        'kode_poli' => 'PAKT',
        'is_active' => true,
    ]);
    $inactivePoli = Poli::create([
        'nama_poli' => 'Poli Tidak Aktif',
        'kode_poli' => 'PTAKT',
        'is_active' => false,
    ]);

    Livewire::test('master.jadwal-dokter')
        ->assertViewHas('doctors', function ($doctors) use ($doctor, $inactiveDoctor) {
            return $doctors->contains($doctor) && ! $doctors->contains($inactiveDoctor);
        })
        ->assertViewHas('polis', function ($polis) use ($activePoli, $inactivePoli) {
            return $polis->contains($activePoli) && ! $polis->contains($inactivePoli);
        });
});

test('it creates a new doctor schedule with validation', function () {
    $userDoc = User::factory()->create(['name' => 'Dr. Budi']);
    $doctor = MasterPetugas::create([
        'user_id' => $userDoc->id,
        'nama_petugas' => 'Dr. Budi',
        'nik' => '1234567890123433',
        'jenis_petugas' => 'Dokter',
        'is_aktif' => true,
    ]);

    $poli = Poli::create([
        'nama_poli' => 'Poli Anak',
        'kode_poli' => 'ANAK',
        'is_active' => true,
    ]);

    Livewire::test('master.jadwal-dokter')
        // Clear pre-filled fields to trigger required validation errors
        ->set('hari', '')
        ->set('jam_mulai', '')
        ->set('jam_selesai', '')
        ->call('save')
        ->assertHasErrors([
            'petugas_id' => 'required',
            'poli_id' => 'required',
            'hari' => 'required',
            'jam_mulai' => 'required',
            'jam_selesai' => 'required',
        ])
        // Fill form with invalid values
        ->set('petugas_id', $doctor->id)
        ->set('poli_id', $poli->id)
        ->set('hari', 'HariKiamat')
        ->set('jam_mulai', '10:00')
        ->set('jam_selesai', '09:00') // End before start
        ->set('kuota_pasien', 0)
        ->call('save')
        ->assertHasErrors([
            'hari' => 'in',
            'jam_selesai' => 'after',
            'kuota_pasien' => 'min',
        ])
        // Fill valid inputs
        ->set('hari', 'Senin')
        ->set('jam_selesai', '12:00')
        ->set('kuota_pasien', 20)
        ->call('save')
        ->assertHasNoErrors();

    $this->assertDatabaseHas('master_jadwal_dokters', [
        'petugas_id' => $doctor->id,
        'poli_id' => $poli->id,
        'hari' => 'Senin',
        'jam_mulai' => '10:00', // Actual value set during validation test
        'jam_selesai' => '12:00',
        'kuota_pasien' => 20,
    ]);
});

test('it updates an existing doctor schedule', function () {
    $userDoc = User::factory()->create(['name' => 'Dr. Citra']);
    $doctor = MasterPetugas::create([
        'user_id' => $userDoc->id,
        'nama_petugas' => 'Dr. Citra',
        'nik' => '1234567890123444',
        'jenis_petugas' => 'Dokter',
        'is_aktif' => true,
    ]);

    $poli = Poli::create([
        'nama_poli' => 'Poli Gigi',
        'kode_poli' => 'GIGI',
        'is_active' => true,
    ]);

    $schedule = MasterJadwalDokter::create([
        'petugas_id' => $doctor->id,
        'poli_id' => $poli->id,
        'hari' => 'Selasa',
        'jam_mulai' => '09:00',
        'jam_selesai' => '14:00',
        'kuota_pasien' => 15,
    ]);

    Livewire::test('master.jadwal-dokter')
        ->call('edit', $schedule->id)
        ->assertSet('petugas_id', $doctor->id)
        ->assertSet('hari', 'Selasa')
        ->assertSet('jam_mulai', '09:00')
        ->assertSet('jam_selesai', '14:00')
        ->set('hari', 'Rabu')
        ->set('kuota_pasien', 25)
        ->call('save')
        ->assertHasNoErrors();

    $schedule->refresh();
    expect($schedule->hari)->toBe('Rabu');
    expect($schedule->kuota_pasien)->toBe(25);
});

test('it deletes a doctor schedule', function () {
    $userDoc = User::factory()->create(['name' => 'Dr. Dodi']);
    $doctor = MasterPetugas::create([
        'user_id' => $userDoc->id,
        'nama_petugas' => 'Dr. Dodi',
        'nik' => '1234567890123455',
        'jenis_petugas' => 'Dokter',
        'is_aktif' => true,
    ]);

    $poli = Poli::create([
        'nama_poli' => 'Poli Kandungan',
        'kode_poli' => 'KAND',
        'is_active' => true,
    ]);

    $schedule = MasterJadwalDokter::create([
        'petugas_id' => $doctor->id,
        'poli_id' => $poli->id,
        'hari' => 'Kamis',
        'jam_mulai' => '08:00',
        'jam_selesai' => '13:00',
        'kuota_pasien' => 10,
    ]);

    Livewire::test('master.jadwal-dokter')
        ->call('delete', $schedule->id)
        ->assertHasNoErrors();

    $this->assertDatabaseMissing('master_jadwal_dokters', [
        'id' => $schedule->id,
    ]);
});
