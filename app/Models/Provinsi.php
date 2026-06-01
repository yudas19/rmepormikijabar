<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Provinsi extends Model
{
    protected $table = 'provinsis';

    protected $fillable = [
        'nama_provinsi',
        'kode_provinsi',
    ];

    public function provinsis()
    {
        return $this->hasMany(Provinsi::class, 'provinsi_id');
    }
}
