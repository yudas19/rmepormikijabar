<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SuratPersetujuan extends Model
{
    protected $table = 'surat_persetujuans';

    protected $guarded = ['id'];

    public function pendaftaran(): BelongsTo
    {
        return $this->belongsTo(Pendaftaran::class);
    }

    public function petugas(): BelongsTo
    {
        return $this->belongsTo(MasterPetugas::class, 'petugas_id');
    }
}
