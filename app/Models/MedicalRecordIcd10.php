<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MedicalRecordIcd10 extends Model
{
    protected $table = 'medical_record_icd10';

    protected $guarded = ['id'];

    protected $casts = [
        'is_primary' => 'boolean',
    ];

    public function medicalRecord(): BelongsTo
    {
        return $this->belongsTo(MedicalRecord::class, 'medical_record_id');
    }

    public function masterIcd10(): BelongsTo
    {
        return $this->belongsTo(MasterIcd10::class, 'master_icd10_id');
    }
}
