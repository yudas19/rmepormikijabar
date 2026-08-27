<?php

use Illuminate\Contracts\Console\Kernel;
use App\Models\User;
use App\Models\Pasien;
use App\Models\Poli;
use App\Models\MasterPetugas;
use App\Models\Pendaftaran;
use App\Models\MedicalRecord;

require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Kernel::class);
$kernel->bootstrap();

$user = User::first();
$pasien = Pasien::create([
    'no_rekam_medis' => 'RM-TEST-' . rand(),
    'nama_pasien' => 'John Doe',
    'nik' => '1234567890123456',
    'tempat_lahir' => 'Bandung',
    'tanggal_lahir' => '1990-01-01',
    'jenis_kelamin' => 'L',
    'golongan_darah' => 'O',
    'alamat' => 'Jl. Merdeka No. 12',
    'status_pasien' => 'aktif',
]);

$poli = Poli::where('kode_poli', 'UMU')->first();
$dokter = MasterPetugas::where('jenis_petugas', 'Dokter')->first();

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

$response = app('router')->get('/medical-records/examine/{poliklinik}/{encounter_id}', [
    'uses' => 'App\Http\Controllers\MedicalRecordController@examine',
    'as' => 'medical-record.examine',
]);

// Send request acting as user
$request = \Illuminate\Http\Request::create(route('medical-record.examine', [
    'poliklinik' => 'umum',
    'encounter_id' => $medicalRecord->encounter_id,
]));
$request->setUserResolver(fn() => $user);

$response = app()->handle($request);
echo "Status Code: " . $response->getStatusCode() . "\n";
$html = $response->getContent();

if ($response->getStatusCode() >= 400 || $response->isRedirection()) {
    echo "Content snippet:\n" . substr($html, 0, 1000) . "\n";
} else {
    // Find where "poli-umum" or "medical-record" is in HTML
    $pos = strpos($html, 'poli-umum');
    if ($pos !== false) {
        echo "Found 'poli-umum' at pos $pos. Snippet:\n";
        echo substr($html, $pos - 100, 200) . "\n";
    } else {
        echo "Not found 'poli-umum' in HTML! Length: " . strlen($html) . "\n";
    }
}
