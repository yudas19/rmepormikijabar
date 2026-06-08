<?php

use Illuminate\Contracts\Console\Kernel;

require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Kernel::class);
$kernel->bootstrap();

$testNames = [
    'master.petugas',
    'master.⚡petugas',
    'master.obat',
    'master.⚡obat',
    'master.poli',
    'master.⚡poli',
    'master.laboratorium',
    'master.⚡laboratorium',
    'master.pcarebpjs',
    'master.⚡pcarebpjs',
    'master.pekerjaan',
    'master.⚡pekerjaan',
    'master.pendidikan',
    'master.⚡pendidikan',
    'master.satusehat',
    'master.⚡satusehat',
    'master.cara-pakai-obat',
    'master.⚡cara-pakai-obat',
    'master.provinsi',
    'master.⚡provinsi',
    'master.kabupaten-kota',
    'master.⚡kabupaten-kota',
    'medical-record.poli-umum',
    '⚡medical-record.poli-umum',
    '⚡medical-record.⚡poli-umum',
];

foreach ($testNames as $name) {
    try {
        $resolved = app('livewire')->new($name);
        echo "Resolved '$name' successfully! Class: ".get_class($resolved)."\n";
    } catch (Exception $e) {
        echo "Failed to resolve '$name': ".$e->getMessage()."\n";
    }
}
