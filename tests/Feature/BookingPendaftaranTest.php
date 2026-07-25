<?php

use App\Models\Booking;
use App\Models\MasterPetugas;
use App\Models\Pasien;
use App\Models\Poli;
use App\Models\User;
use Carbon\Carbon;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Support\Facades\DB;
use Livewire\Livewire;

beforeEach(function () {
    $this->seed(DatabaseSeeder::class);
});

/**
 * Helper: buat poli medis beserta dokter + jadwal sesuai hari yang diminta.
 */
function createPoliAndDokterWithSchedule(string $namaHari): array
{
    $poli = Poli::create([
        'kode_poli' => 'TES',
        'nama_poli' => 'Poli Test',
        'jenis_unit' => 'medis',
        'is_active' => true,
    ]);

    $dokter = MasterPetugas::create([
        'nama_petugas' => 'dr. Test Dokter',
        'jenis_petugas' => 'Dokter',
        'nomor_sip' => 'SIP-TEST-001',
        'is_aktif' => true,
    ]);

    DB::table('master_jadwal_dokters')->insert([
        'petugas_id' => $dokter->id,
        'poli_id' => $poli->id,
        'hari' => $namaHari,
        'jam_mulai' => '08:00:00',
        'jam_selesai' => '14:00:00',
        'kuota_pasien' => 30,
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    return [$poli, $dokter];
}

/**
 * Helper: buat pasien baru dengan NIK unik.
 */
function createTestPasien(string $noRm, string $nik): Pasien
{
    return Pasien::create([
        'no_rekam_medis' => $noRm,
        'nama_pasien' => 'Booking Tester '.$noRm,
        'nik' => $nik,
        'tempat_lahir' => 'Bandung',
        'tanggal_lahir' => '1990-01-01',
        'jenis_kelamin' => 'L',
        'golongan_darah' => 'O',
        'alamat' => 'Jl. Test No. 1',
        'status_pasien' => 'aktif',
    ]);
}

test('can open booking modal and save a booking for a future date', function () {
    $user = User::first();
    $this->actingAs($user);

    $pasien = createTestPasien('RM-BK-0001', '3301010101010001');

    // Tentukan tanggal 2 hari kedepan dan dapatkan nama harinya
    $futureDate = now()->addDays(2)->format('Y-m-d');
    $dayMap = [0 => 'Minggu', 1 => 'Senin', 2 => 'Selasa', 3 => 'Rabu', 4 => 'Kamis', 5 => 'Jumat', 6 => 'Sabtu'];
    $namaHari = $dayMap[Carbon::parse($futureDate)->dayOfWeek];

    [$poli, $dokter] = createPoliAndDokterWithSchedule($namaHari);

    $component = Livewire::test('⚡pendaftaran');
    $component->call('openBookingModal', $pasien->id);

    $component->assertSet('showBookingModal', true)
        ->assertSet('selectedPasienId', $pasien->id);

    $component->set('bookingDate', $futureDate)
        ->set('booking_poli_id', $poli->id)
        ->set('booking_dokter_id', $dokter->id)
        ->call('saveBooking');

    $component->assertSet('showBookingModal', false);

    $this->assertDatabaseHas('bookings', [
        'pasien_id' => $pasien->id,
        'poli_id' => $poli->id,
        'dokter_id' => $dokter->id,
        'booking_date' => $futureDate.' 00:00:00',
        'status' => 'pending',
    ]);
});

test('cannot create booking for today or past dates', function () {
    $user = User::first();
    $this->actingAs($user);

    $pasien = createTestPasien('RM-BK-0002', '3301010101010002');

    Livewire::test('⚡pendaftaran')
        ->call('openBookingModal', $pasien->id)
        ->set('bookingDate', now()->format('Y-m-d'))
        ->call('saveBooking')
        ->assertHasErrors('bookingDate');

    $this->assertDatabaseMissing('bookings', [
        'pasien_id' => $pasien->id,
    ]);
});

test('cannot create duplicate booking for same patient on same date', function () {
    $user = User::first();
    $this->actingAs($user);

    $pasien = createTestPasien('RM-BK-0003', '3301010101010003');
    $futureDate = now()->addDays(3)->format('Y-m-d');
    $dayMap = [0 => 'Minggu', 1 => 'Senin', 2 => 'Selasa', 3 => 'Rabu', 4 => 'Kamis', 5 => 'Jumat', 6 => 'Sabtu'];
    $namaHari = $dayMap[Carbon::parse($futureDate)->dayOfWeek];

    [$poli, $dokter] = createPoliAndDokterWithSchedule($namaHari);

    Booking::create([
        'pasien_id' => $pasien->id,
        'poli_id' => $poli->id,
        'dokter_id' => $dokter->id,
        'booking_date' => $futureDate,
        'status' => 'pending',
        'created_by' => $user->id,
    ]);

    Livewire::test('⚡pendaftaran')
        ->call('openBookingModal', $pasien->id)
        ->set('bookingDate', $futureDate)
        ->set('booking_poli_id', $poli->id)
        ->set('booking_dokter_id', $dokter->id)
        ->call('saveBooking')
        ->assertHasErrors('bookingDate');

    expect(Booking::where('pasien_id', $pasien->id)->whereDate('booking_date', $futureDate)->count())->toBe(1);
});

test('can confirm booking on the booking date and opens registration modal', function () {
    $user = User::first();
    $this->actingAs($user);

    $pasien = createTestPasien('RM-BK-0004', '3301010101010004');
    $namaHari = [0 => 'Minggu', 1 => 'Senin', 2 => 'Selasa', 3 => 'Rabu', 4 => 'Kamis', 5 => 'Jumat', 6 => 'Sabtu'][now()->dayOfWeek];
    [$poli, $dokter] = createPoliAndDokterWithSchedule($namaHari);

    $booking = Booking::create([
        'pasien_id' => $pasien->id,
        'poli_id' => $poli->id,
        'dokter_id' => $dokter->id,
        'booking_date' => now()->format('Y-m-d'),
        'status' => 'pending',
        'created_by' => $user->id,
    ]);

    Livewire::test('⚡pendaftaran')
        ->call('confirmBooking', $booking->id)
        ->assertSet('showRegisterModal', true)
        ->assertSet('selectedPasienId', $pasien->id)
        ->assertSet('reg_tanggal_kunjungan', now()->format('Y-m-d'));

    $booking->refresh();
    expect($booking->status)->toBe('confirmed');
    expect($booking->confirmed_by)->toBe($user->id);
    expect($booking->confirmed_at)->not->toBeNull();
});

test('cannot confirm booking on a different day', function () {
    $user = User::first();
    $this->actingAs($user);

    $pasien = createTestPasien('RM-BK-0005', '3301010101010005');

    $booking = Booking::create([
        'pasien_id' => $pasien->id,
        'booking_date' => now()->addDays(2)->format('Y-m-d'),
        'status' => 'pending',
        'created_by' => $user->id,
    ]);

    Livewire::test('⚡pendaftaran')
        ->call('confirmBooking', $booking->id)
        ->assertSet('showRegisterModal', false);

    $booking->refresh();
    expect($booking->status)->toBe('pending');
});

test('can cancel a pending booking', function () {
    $user = User::first();
    $this->actingAs($user);

    $pasien = createTestPasien('RM-BK-0006', '3301010101010006');

    $booking = Booking::create([
        'pasien_id' => $pasien->id,
        'booking_date' => now()->addDay()->format('Y-m-d'),
        'status' => 'pending',
        'created_by' => $user->id,
    ]);

    Livewire::test('⚡pendaftaran')
        ->call('cancelBooking', $booking->id);

    $booking->refresh();
    expect($booking->status)->toBe('cancelled');
});
