<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MasterJadwalDokter extends Model
{
    protected $table = 'master_jadwal_dokters';

    protected $guarded = ['id'];

    public function petugas(): BelongsTo
    {
        return $this->belongsTo(MasterPetugas::class, 'petugas_id');
    }

    public function poli(): BelongsTo
    {
        return $this->belongsTo(Poli::class, 'poli_id');
    }
}
