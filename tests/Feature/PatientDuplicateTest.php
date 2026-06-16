<?php

use App\Models\Pasien;

test('multiple patients can be created with empty ihs_number and no_bpjs without duplication errors', function () {
    $pasien1 = Pasien::create([
        'no_rekam_medis' => 'RM-20261102-0001',
        'nama_pasien' => 'Budiono',
        'nik' => '1234567890123456',
        'tempat_lahir' => 'Bandung',
        'tanggal_lahir' => '1990-01-01',
        'jenis_kelamin' => 'L',
        'golongan_darah' => 'O',
        'alamat' => 'Jl. Merdeka No. 12',
        'status_pasien' => 'aktif',
        'no_bpjs' => '',
        'ihs_number' => '  ',
    ]);

    expect($pasien1->no_bpjs)->toBeNull();
    expect($pasien1->ihs_number)->toBeNull();

    $pasien2 = Pasien::create([
        'no_rekam_medis' => 'RM-20261102-0002',
        'nama_pasien' => 'Siti',
        'nik' => '1234567890123457',
        'tempat_lahir' => 'Bandung',
        'tanggal_lahir' => '1992-02-02',
        'jenis_kelamin' => 'P',
        'golongan_darah' => 'A',
        'alamat' => 'Jl. Kebon No. 4',
        'status_pasien' => 'aktif',
        'no_bpjs' => '',
        'ihs_number' => '',
    ]);

    expect($pasien2->no_bpjs)->toBeNull();
    expect($pasien2->ihs_number)->toBeNull();
});
