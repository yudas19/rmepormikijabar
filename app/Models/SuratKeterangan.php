<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SuratKeterangan extends Model
{
    protected $table = 'surat_keterangans';
    protected $guarded = ['id'];

    protected $casts = [
        'konten_surat' => 'array'
    ];
}
