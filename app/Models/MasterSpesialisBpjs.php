<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MasterSpesialisBpjs extends Model
{
    protected $table = 'master_spesialis_bpjs';

    protected $guarded = ['id'];

    protected $casts = [
        'is_active' => 'boolean',
    ];
}
