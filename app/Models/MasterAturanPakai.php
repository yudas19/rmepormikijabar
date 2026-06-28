<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MasterAturanPakai extends Model
{
    protected $table = 'master_aturan_pakais';

    protected $guarded = ['id'];

    protected $casts = [
        'is_active' => 'boolean',
    ];
}
