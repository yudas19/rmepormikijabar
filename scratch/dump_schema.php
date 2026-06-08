<?php

use Illuminate\Contracts\Console\Kernel;
use Illuminate\Support\Facades\Schema;

require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Kernel::class);
$kernel->bootstrap();

$tables = [
    'master_aturan_pakais',
    'kabupaten_kotas',
    'master_labs',
    'master_obats',
    'master_pcares',
    'master_pekerjaans',
    'master_pendidikans',
    'master_petugass',
    'master_polis',
    'provinsis',
    'master_satusehats',
    'master_tindakans',
];

foreach ($tables as $table) {
    echo "=========================================\n";
    echo "Table: $table\n";
    echo "=========================================\n";
    if (Schema::hasTable($table)) {
        $columns = Schema::getColumnListing($table);
        foreach ($columns as $column) {
            $type = Schema::getColumnType($table, $column);
            echo " - $column ($type)\n";
        }
    } else {
        echo "Table does not exist!\n";
    }
    echo "\n";
}
