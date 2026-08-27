<?php

use Illuminate\Contracts\Console\Kernel;
use App\Models\User;
use App\Models\Pasien;
use App\Models\Poli;
use App\Models\MasterPetugas;
use App\Models\Pendaftaran;
use App\Models\MedicalRecord;
use Illuminate\Support\Facades\Auth;

require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Kernel::class);
$kernel->bootstrap();

$user = User::first();
if (!$user) {
    $user = User::create([
        'name' => 'Test User',
        'email' => 'test@example.com',
        'password' => bcrypt('password'),
    ]);
}

// Give permission if using spatie permissions
if (method_exists($user, 'givePermissionTo')) {
    // Check if permission exists first
    try {
        \Spatie\Permission\Models\Permission::findOrCreate('akses_rekam_medis');
        $user->givePermissionTo('akses_rekam_medis');
    } catch (\Throwable $e) {}
}

$pasien = Pasien::create([
    'no_rekam_medis' => 'RM-TEST-' . rand(),
    'nama_pasien' => 'John Doe',
    'nik' => '999' . rand(1000000000000, 9999999999999),
    'tempat_lahir' => 'Bandung',
    'tanggal_lahir' => '1990-01-01',
    'jenis_kelamin' => 'L',
    'golongan_darah' => 'O',
    'alamat' => 'Jl. Merdeka No. 12',
    'status_pasien' => 'aktif',
]);

$poli = Poli::where('kode_poli', 'UMU')->first();
$dokter = MasterPetugas::where('jenis_petugas', 'like', '%Dokter%')->first();

$pendaftaran = Pendaftaran::create([
    'no_registrasi' => 'REG-' . rand(),
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
    'encounter_id' => 'ENC-' . rand(),
    'patient_id' => $pasien->id,
    'pendaftaran_id' => $pendaftaran->id,
    'poli_id' => $poli->id,
    'status' => 'waiting',
    'nomor_antrean' => 'A-01',
]);

Auth::login($user);

// Send request acting as user
$request = \Illuminate\Http\Request::create(route('medical-record.examine', [
    'poliklinik' => 'umum',
    'encounter_id' => $medicalRecord->encounter_id,
]));
$request->setUserResolver(fn() => $user);

// Make sure session has the authenticated user
$request->setLaravelSession(app('session')->driver());
app('session')->driver()->put(Auth::getName(), $user->getAuthIdentifier());

$response = app()->handle($request);
echo "Status code: " . $response->getStatusCode() . "\n";
$html = $response->getContent();

if ($response->getStatusCode() !== 200) {
    echo "Content:\n" . substr($html, 0, 1000) . "\n";
} else {
    echo "Content Length: " . strlen($html) . "\n";
    // Check if "⚡" or "medical-record" is in the HTML
    echo "Contains '⚡': " . (str_contains($html, '⚡') ? 'YES' : 'NO') . "\n";
    echo "Contains 'medical-record': " . (str_contains($html, 'medical-record') ? 'YES' : 'NO') . "\n";
    
    // Find where the livewire component is initialized
    if (preg_match('/&quot;name&quot;:&quot;[^&]+&quot;/', $html, $m)) {
        echo "Found component in HTML: " . $m[0] . "\n";
    }
}
