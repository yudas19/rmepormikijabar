<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Petugas extends Model
{
    use SoftDeletes;

    protected $table = 'petugass';
    protected $guarded = ['id'];

    protected $casts = [
        'is_aktif' => 'boolean',
    ];

    // Relasi balik ke User (Akun Login)
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}