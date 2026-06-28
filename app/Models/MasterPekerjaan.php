<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MasterPekerjaan extends Model
{
    protected $table = 'master_pekerjaans';

    protected $guarded = ['id'];

    protected $casts = [
        'is_active' => 'boolean',
    ];
}
