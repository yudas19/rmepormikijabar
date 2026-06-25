<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Poli extends Model
{
    protected $table = 'master_polis';

    protected $fillable = [
        'kode_poli',
        'nama_poli',
        'kode_poli_bpjs',
        'satu_sehat_location_id',
        'is_active',
        'jenis_unit',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function pendaftaran()
    {
        return $this->hasMany(Pendaftaran::class, 'poli_id');
    }

    public function satusehat()
    {
        return $this->hasOne(MasterPoliSatusehat::class, 'master_poli_id');
    }
}
