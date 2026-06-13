<?php

use App\Models\MasterPetugas;
use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Support\Facades\Hash;
use Livewire\Livewire;
use Spatie\Permission\Models\Role;

beforeEach(function () {
    $this->seed(DatabaseSeeder::class);
    // Authenticate as admin
    $admin = User::whereHas('roles', function ($q) {
        $q->where('name', 'admin');
    })->first();
    $this->actingAs($admin);
});

test('guests are redirected to the login page for master petugas', function () {
    auth()->logout();
    $this->get(route('master.petugas'))
        ->assertRedirect(route('login'));
});

test('authorized users can view master petugas page', function () {
    $this->get(route('master.petugas'))
        ->assertOk();
});

test('administrator can create new petugas and linked user account with spatie role', function () {
    $roleName = 'kasir';

    Livewire::test('master.petugas')
        ->set('nama_petugas', 'Kasir Baru')
        ->set('nik', '1234567890123456')
        ->set('jenis_petugas', 'Staf Administrasi')
        ->set('email', 'kasirbaru@rmepormikijabar.com')
        ->set('password', 'secretpassword')
        ->set('role', $roleName)
        ->set('is_aktif', true)
        ->call('save')
        ->assertHasNoErrors();

    // Verify User model creation
    $user = User::where('email', 'kasirbaru@rmepormikijabar.com')->first();
    expect($user)->not->toBeNull();
    expect($user->name)->toBe('Kasir Baru');
    expect($user->hasRole($roleName))->toBeTrue();

    // Verify MasterPetugas record creation
    $petugas = MasterPetugas::where('nik', '1234567890123456')->first();
    expect($petugas)->not->toBeNull();
    expect($petugas->user_id)->toBe($user->id);
    expect($petugas->nama_petugas)->toBe('Kasir Baru');
});

test('administrator can update existing petugas user account and role', function () {
    // 1. Create a petugas to edit
    $user = User::factory()->create([
        'name' => 'Dokter Umum Baru',
        'email' => 'dokterbaru@rmepormikijabar.com',
        'password' => Hash::make('oldpassword'),
    ]);
    $user->assignRole('dokter_umum');

    $petugas = MasterPetugas::create([
        'user_id' => $user->id,
        'nama_petugas' => 'Dokter Umum Baru',
        'nik' => '3201010101019999',
        'jenis_petugas' => 'Dokter',
        'is_aktif' => true,
    ]);

    // 2. Perform updates via Livewire
    Livewire::test('master.petugas')
        ->call('edit', $petugas->id)
        ->assertSet('email', 'dokterbaru@rmepormikijabar.com')
        ->assertSet('role', 'dokter_umum')
        // Change email and role
        ->set('email', 'dokter_changed@rmepormikijabar.com')
        ->set('role', 'dokter_gigi')
        // Leave password blank (should NOT overwrite old password)
        ->set('password', '')
        ->call('save')
        ->assertHasNoErrors();

    // Verify DB Updates
    $user->refresh();
    expect($user->email)->toBe('dokter_changed@rmepormikijabar.com');
    expect($user->hasRole('dokter_gigi'))->toBeTrue();
    expect($user->hasRole('dokter_umum'))->toBeFalse();
    // Verify password is still the old one
    expect(Hash::check('oldpassword', $user->password))->toBeTrue();
});

test('administrator can reset password of existing petugas', function () {
    $user = User::factory()->create([
        'name' => 'Perawat Baru',
        'email' => 'perawatbaru@rmepormikijabar.com',
        'password' => Hash::make('originalpassword'),
    ]);
    $user->assignRole('perawat');

    $petugas = MasterPetugas::create([
        'user_id' => $user->id,
        'nama_petugas' => 'Perawat Baru',
        'nik' => '3201010101018888',
        'jenis_petugas' => 'Perawat',
        'is_aktif' => true,
    ]);

    Livewire::test('master.petugas')
        ->call('edit', $petugas->id)
        // Enter a new password
        ->set('password', 'brandnewpassword')
        ->call('save')
        ->assertHasNoErrors();

    $user->refresh();
    // Verify password has changed
    expect(Hash::check('brandnewpassword', $user->password))->toBeTrue();
    expect(Hash::check('originalpassword', $user->password))->toBeFalse();
});
