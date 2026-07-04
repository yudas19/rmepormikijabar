<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MasterProvinsi extends Model
{
    protected $table = 'master_provinsis';

    protected $fillable = [
        'nama_provinsi',
        'kode_provinsi',
    ];

    public function provinsis()
    {
        return $this->hasMany(Provinsi::class, 'provinsi_id');
    }
}
