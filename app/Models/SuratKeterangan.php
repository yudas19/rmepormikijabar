<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SuratKeterangan extends Model
{
    protected $table = 'surat_keterangans';

    protected $guarded = ['id'];

    protected $casts = [
        'konten_surat' => 'array',
    ];

    /**
     * Get the patient associated with this certificate.
     */
    public function pasien(): BelongsTo
    {
        return $this->belongsTo(Pasien::class, 'pasien_id');
    }

    /**
     * Get the doctor associated with this certificate.
     */
    public function dokter(): BelongsTo
    {
        return $this->belongsTo(MasterPetugas::class, 'dokter_id');
    }
}
