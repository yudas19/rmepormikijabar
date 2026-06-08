<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MedicalRecordIcd9 extends Model
{
    protected $table = 'medical_record_icd9';

    protected $guarded = ['id'];

    public function medicalRecord(): BelongsTo
    {
        return $this->belongsTo(MedicalRecord::class, 'medical_record_id');
    }

    public function masterIcd9(): BelongsTo
    {
        return $this->belongsTo(MasterIcd9::class, 'master_icd9_id');
    }
}
