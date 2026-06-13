<?php

use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Livewire\Livewire;
use Spatie\Permission\Models\Role;

beforeEach(function () {
    $this->seed(DatabaseSeeder::class);
});

test('guests are redirected to the login page for access control', function () {
    $this->get('/admin/hak-akses')
        ->assertRedirect(route('login'));
});

test('authenticated users without access_pengaturan_akses permission receive 403', function () {
    $user = User::factory()->create();
    $user->assignRole('dokter_umum');
    $this->actingAs($user);

    $this->get('/admin/hak-akses')
        ->assertStatus(403);
});

test('authenticated users with access_pengaturan_akses permission can access the page', function () {
    $user = User::whereHas('roles', function ($q) {
        $q->where('name', 'admin');
    })->first();

    $this->actingAs($user);

    $this->get('/admin/hak-akses')
        ->assertOk();
});

test('unauthorized users are blocked from specific routes based on permission', function () {
    $user = User::factory()->create();
    $user->assignRole('kasir');

    $this->actingAs($user);

    // kasir role has akses_kasir. Can access kasir index.
    $this->get(route('kasir.index'))->assertOk();

    // kasir role does NOT have akses_farmasi. Accessing farmasi index should return 403.
    $this->get(route('layanan.farmasi'))->assertStatus(403);
});

test('livewire access control component can select role and update permissions', function () {
    $user = User::whereHas('roles', function ($q) {
        $q->where('name', 'admin');
    })->first();

    $this->actingAs($user);

    $kasirRole = Role::where('name', 'kasir')->first();
    $dokterRole = Role::where('name', 'dokter_umum')->first();

    // Test selection of kasir role loads its permissions
    Livewire::test('admin.hak-akses')
        ->assertSet('selectedRoleId', Role::orderBy('id')->first()->id)
        ->call('selectRole', $kasirRole->id)
        ->assertSet('selectedRoleId', $kasirRole->id)
        ->assertSet('selectedPermissions.akses_kasir', true)
        ->assertSet('selectedPermissions.akses_farmasi', false)
        // Select dokter_umum role
        ->call('selectRole', $dokterRole->id)
        ->assertSet('selectedRoleId', $dokterRole->id)
        ->assertSet('selectedPermissions.akses_kasir', false)
        ->assertSet('selectedPermissions.akses_poli_umum', true);
});

test('livewire access control component can save synced permissions', function () {
    $user = User::whereHas('roles', function ($q) {
        $q->where('name', 'admin');
    })->first();

    $this->actingAs($user);

    $kasirRole = Role::where('name', 'kasir')->first();

    // Initially kasir only has akses_kasir
    expect($kasirRole->hasPermissionTo('akses_kasir'))->toBeTrue();
    expect($kasirRole->hasPermissionTo('akses_farmasi'))->toBeFalse();

    Livewire::test('admin.hak-akses')
        ->call('selectRole', $kasirRole->id)
        // Grant akses_farmasi and revoke akses_kasir
        ->set('selectedPermissions.akses_farmasi', true)
        ->set('selectedPermissions.akses_kasir', false)
        ->call('save')
        ->assertHasNoErrors();

    // Verify database updates
    $kasirRole->refresh();
    expect($kasirRole->hasPermissionTo('akses_farmasi'))->toBeTrue();
    expect($kasirRole->hasPermissionTo('akses_kasir'))->toBeFalse();
});
