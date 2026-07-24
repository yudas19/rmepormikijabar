<?php

use App\Models\Booking;
use App\Models\Pasien;
use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Livewire\Livewire;

beforeEach(function () {
    $this->seed(DatabaseSeeder::class);
});

test('can open booking modal and save a booking for a future date', function () {
    $user = User::first();
    $this->actingAs($user);

    $pasien = Pasien::create([
        'no_rekam_medis' => 'RM-BK-0001',
        'nama_pasien' => 'Booking Tester',
        'nik' => '3301010101010001',
        'tempat_lahir' => 'Bandung',
        'tanggal_lahir' => '1990-01-01',
        'jenis_kelamin' => 'L',
        'golongan_darah' => 'O',
        'alamat' => 'Jl. Test No. 1',
        'status_pasien' => 'aktif',
    ]);

    $component = Livewire::test('⚡pendaftaran');

    $component->call('openBookingModal', $pasien->id);

    $component->assertSet('showBookingModal', true)
        ->assertSet('selectedPasienId', $pasien->id);

    $futureDate = now()->addDays(2)->format('Y-m-d');

    $component->set('bookingDate', $futureDate)
        ->call('saveBooking');

    $component->assertSet('showBookingModal', false);

    $this->assertDatabaseHas('bookings', [
        'pasien_id' => $pasien->id,
        'booking_date' => $futureDate . ' 00:00:00',
        'status' => 'pending',
    ]);
});

test('cannot create booking for today or past dates', function () {
    $user = User::first();
    $this->actingAs($user);

    $pasien = Pasien::create([
        'no_rekam_medis' => 'RM-BK-0002',
        'nama_pasien' => 'Past Date Tester',
        'nik' => '3301010101010002',
        'tempat_lahir' => 'Jakarta',
        'tanggal_lahir' => '1995-05-15',
        'jenis_kelamin' => 'P',
        'golongan_darah' => 'A',
        'alamat' => 'Jl. Test No. 2',
        'status_pasien' => 'aktif',
    ]);

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

    $pasien = Pasien::create([
        'no_rekam_medis' => 'RM-BK-0003',
        'nama_pasien' => 'Duplicate Tester',
        'nik' => '3301010101010003',
        'tempat_lahir' => 'Surabaya',
        'tanggal_lahir' => '1988-03-20',
        'jenis_kelamin' => 'L',
        'golongan_darah' => 'B',
        'alamat' => 'Jl. Test No. 3',
        'status_pasien' => 'aktif',
    ]);

    $futureDate = now()->addDays(3)->format('Y-m-d');

    Booking::create([
        'pasien_id' => $pasien->id,
        'booking_date' => $futureDate,
        'status' => 'pending',
        'created_by' => $user->id,
    ]);

    Livewire::test('⚡pendaftaran')
        ->call('openBookingModal', $pasien->id)
        ->set('bookingDate', $futureDate)
        ->call('saveBooking')
        ->assertHasErrors('bookingDate');

    expect(Booking::where('pasien_id', $pasien->id)->whereDate('booking_date', $futureDate)->count())->toBe(1);
});

test('can confirm booking on the booking date and opens registration modal', function () {
    $user = User::first();
    $this->actingAs($user);

    $pasien = Pasien::create([
        'no_rekam_medis' => 'RM-BK-0004',
        'nama_pasien' => 'Confirm Tester',
        'nik' => '3301010101010004',
        'tempat_lahir' => 'Medan',
        'tanggal_lahir' => '1992-07-10',
        'jenis_kelamin' => 'L',
        'golongan_darah' => 'AB',
        'alamat' => 'Jl. Test No. 4',
        'status_pasien' => 'aktif',
    ]);

    $booking = Booking::create([
        'pasien_id' => $pasien->id,
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

    $pasien = Pasien::create([
        'no_rekam_medis' => 'RM-BK-0005',
        'nama_pasien' => 'Future Confirm Tester',
        'nik' => '3301010101010005',
        'tempat_lahir' => 'Semarang',
        'tanggal_lahir' => '1985-11-25',
        'jenis_kelamin' => 'P',
        'golongan_darah' => 'O',
        'alamat' => 'Jl. Test No. 5',
        'status_pasien' => 'aktif',
    ]);

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

    $pasien = Pasien::create([
        'no_rekam_medis' => 'RM-BK-0006',
        'nama_pasien' => 'Cancel Tester',
        'nik' => '3301010101010006',
        'tempat_lahir' => 'Yogyakarta',
        'tanggal_lahir' => '1998-04-18',
        'jenis_kelamin' => 'L',
        'golongan_darah' => 'A',
        'alamat' => 'Jl. Test No. 6',
        'status_pasien' => 'aktif',
    ]);

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
