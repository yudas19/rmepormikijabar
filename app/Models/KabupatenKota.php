<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class KabupatenKota extends Model
{
    protected $table = 'kabupaten_kotas';

    protected $fillable = [
        'kode_kabupaten_kota',
        'nama_kabupaten_kota',
    ];
}
