<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SuratRujukan extends Model
{
    protected $table = 'surat_rujukans';

    protected $guarded = ['id'];

    public function pendaftaran(): BelongsTo
    {
        return $this->belongsTo(Pendaftaran::class);
    }

    public function pasien(): BelongsTo
    {
        return $this->belongsTo(Pasien::class);
    }

    public function dokter(): BelongsTo
    {
        return $this->belongsTo(MasterPetugas::class, 'dokter_id');
    }
}
