<?php

use App\Models\FaskesProfile;
use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;

beforeEach(function () {
    $this->seed(DatabaseSeeder::class);
    // Authenticate as admin
    $admin = User::whereHas('roles', function ($q) {
        $q->where('name', 'admin');
    })->first();
    $this->actingAs($admin);
});

test('guests are redirected to login for faskes profile page', function () {
    auth()->logout();
    $this->get(route('master.faskes-profile'))
        ->assertRedirect(route('login'));
});

test('authorized users can view faskes profile page', function () {
    $this->get(route('master.faskes-profile'))
        ->assertOk();
});

test('it loads the default or existing faskes profile on mount', function () {
    $profile = FaskesProfile::first();
    if ($profile) {
        $profile->delete();
    }

    Livewire::test('master.faskes-profile')
        ->assertSet('nama_faskes', 'Klinik Pratama Pormiki')
        ->assertSet('penanggung_jawab', 'dr. Andi Wijaya');
});

test('it updates the faskes profile details successfully', function () {
    Livewire::test('master.faskes-profile')
        ->set('nama_faskes', 'Klinik Sehat Walafiat')
        ->set('alamat', 'Jl. Baru No. 10')
        ->set('penanggung_jawab', 'dr. Budi Setiawan')
        ->set('no_telp', '021-111111')
        ->set('email', 'admin@sehatwalafiat.com')
        ->set('kode_faskes_kemenkes', 'F-99999')
        ->set('latitude', '-6.914744')
        ->set('longitude', '107.609810')
        ->call('save')
        ->assertHasNoErrors();

    $profile = FaskesProfile::find(1);
    expect($profile->nama_faskes)->toBe('Klinik Sehat Walafiat');
    expect($profile->penanggung_jawab)->toBe('dr. Budi Setiawan');
    expect($profile->email)->toBe('admin@sehatwalafiat.com');
    expect(floatval($profile->latitude))->toBe(-6.914744);
    expect(floatval($profile->longitude))->toBe(107.609810);
});

test('it validates required fields', function () {
    Livewire::test('master.faskes-profile')
        ->set('nama_faskes', '')
        ->set('alamat', '')
        ->set('penanggung_jawab', '')
        ->set('no_telp', '')
        ->set('email', 'invalid-email')
        ->set('kode_faskes_kemenkes', '')
        ->call('save')
        ->assertHasErrors([
            'nama_faskes' => 'required',
            'alamat' => 'required',
            'penanggung_jawab' => 'required',
            'no_telp' => 'required',
            'email' => 'email',
            'kode_faskes_kemenkes' => 'required',
        ]);
});

test('it handles logo upload successfully', function () {
    Storage::fake('public');
    $file = UploadedFile::fake()->image('logo.png');

    Livewire::test('master.faskes-profile')
        ->set('logo', $file)
        ->call('save')
        ->assertHasNoErrors();

    $profile = FaskesProfile::find(1);
    expect($profile->logo_path)->not->toBeNull();
    Storage::disk('public')->assertExists($profile->logo_path);
});
