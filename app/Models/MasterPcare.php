<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MasterPcare extends Model
{
    protected $table = 'master_pcares';

    protected $guarded = ['id'];

    protected $casts = [
        'is_bpjs' => 'boolean',
        'is_active' => 'boolean',
        'kode_provinsi' => 'string',
        'kode_kabupaten' => 'string',
        'kode_kecamatan' => 'string',
    ];
}
