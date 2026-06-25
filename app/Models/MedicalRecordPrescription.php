<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOneThrough;

class MedicalRecordPrescription extends Model
{
    protected $table = 'medical_record_prescriptions';

    protected $guarded = ['id'];

    protected $casts = [
        'dispensed_at' => 'datetime',
    ];

    public function medicalRecord(): BelongsTo
    {
        return $this->belongsTo(MedicalRecord::class, 'medical_record_id');
    }

    public function pendaftaran(): HasOneThrough
    {
        return $this->hasOneThrough(
            Pendaftaran::class,
            MedicalRecord::class,
            'id',
            'id',
            'medical_record_id',
            'pendaftaran_id'
        );
    }

    public function metodeRacik(): BelongsTo
    {
        return $this->belongsTo(MasterMetodeRacik::class, 'metode_racik_id');
    }

    public function items(): HasMany
    {
        return $this->hasMany(MedicalRecordPrescriptionItem::class, 'prescription_id');
    }

    public function apoteker(): BelongsTo
    {
        return $this->belongsTo(MasterPetugas::class, 'apoteker_id');
    }

    public function getDispensingStatusLabelAttribute(): string
    {
        return match ($this->dispensing_status) {
            'dispensed' => 'Selesai',
            default => 'Menunggu Obat',
        };
    }

    public function getDispensingStatusColorAttribute(): string
    {
        return match ($this->dispensing_status) {
            'dispensed' => 'green',
            default => 'amber',
        };
    }
}
