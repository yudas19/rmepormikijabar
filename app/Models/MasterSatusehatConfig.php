<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MasterSatusehatConfig extends Model
{
    protected $table = 'master_satusehat_configs';

    protected $guarded = ['id'];

    protected $casts = [
        'is_active' => 'boolean',
    ];
}
