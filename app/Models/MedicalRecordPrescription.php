<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MedicalRecordPrescription extends Model
{
    protected $table = 'medical_record_prescriptions';

    protected $guarded = ['id'];

    public function medicalRecord(): BelongsTo
    {
        return $this->belongsTo(MedicalRecord::class, 'medical_record_id');
    }

    public function metodeRacik(): BelongsTo
    {
        return $this->belongsTo(MasterMetodeRacik::class, 'metode_racik_id');
    }

    public function items(): HasMany
    {
        return $this->hasMany(MedicalRecordPrescriptionItem::class, 'prescription_id');
    }
}
