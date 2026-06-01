<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pendaftaran extends Model
{
    protected $table = 'pendaftaran';
    protected $guarded = ['id'];

    public function pasien(): BelongsTo { return $this->belongsTo(Pasien::class); }
    public function poli(): BelongsTo { return $this->belongsTo(Poli::class); }
    public function dokter(): BelongsTo { return $this->belongsTo(Pegawai::class, 'dokter_id'); }
}
