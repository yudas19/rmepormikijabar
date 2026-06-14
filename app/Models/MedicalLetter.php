<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MedicalLetter extends Model
{
    protected $table = 'medical_letters';

    protected $guarded = ['id'];

    protected $casts = [
        'meta_data' => 'array',
    ];

    /**
     * Get the medical record associated with this certificate.
     */
    public function medicalRecord(): BelongsTo
    {
        return $this->belongsTo(MedicalRecord::class, 'medical_record_id');
    }

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
