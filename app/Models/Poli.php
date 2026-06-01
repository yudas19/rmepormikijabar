<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class poli extends Model
{
    protected $table = 'polis';

    protected $fillable = [
        'nama_poli',
        'kode_poli_bpjs',
        'satu_sehat_location_id',
        'is_aktif',
    ];

    protected $casts = [
        'is_aktif' => 'boolean',
    ];

    public function pendaftaran()
    {
        return $this->hasMany(Pendaftaran::class, 'poli_id');
    }

}
