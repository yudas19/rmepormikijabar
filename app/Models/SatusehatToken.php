<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SatusehatToken extends Model
{
    protected $table = 'satusehat_tokens';

    protected $guarded = ['id'];

    protected $casts = [
        'expires_at' => 'datetime',
    ];
}
