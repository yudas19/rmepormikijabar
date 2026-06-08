<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MedicalRecord extends Model
{
    protected $table = 'medical_records';

    protected $guarded = ['id'];

    protected $casts = [
        'temperature' => 'decimal:1',
        'weight' => 'decimal:2',
        'bmi' => 'decimal:1',
        'tensi_sistole' => 'integer',
        'tensi_diastole' => 'integer',
        'pulse_rate' => 'integer',
        'respiratory_rate' => 'integer',
        'height' => 'integer',
        'gcs_eye' => 'integer',
        'gcs_verbal' => 'integer',
        'gcs_motor' => 'integer',
        'gcs_score' => 'integer',
    ];

    public function pasien(): BelongsTo
    {
        return $this->belongsTo(Pasien::class, 'patient_id');
    }

    public function pendaftaran(): BelongsTo
    {
        return $this->belongsTo(Pendaftaran::class, 'pendaftaran_id');
    }

    public function poli(): BelongsTo
    {
        return $this->belongsTo(Poli::class, 'poli_id');
    }

    public function getPoliklinikTypeAttribute(): string
    {
        if (! $this->relationLoaded('poli')) {
            $this->load('poli');
        }

        $nama = strtolower($this->poli->nama_poli ?? '');
        if (strpos($nama, 'gigi') !== false) {
            return 'gigi';
        }
        if (strpos($nama, 'kia') !== false || strpos($nama, 'anak') !== false || strpos($nama, 'ibu') !== false) {
            return 'kia';
        }

        return 'umum';
    }

    public function icd10s(): HasMany
    {
        return $this->hasMany(MedicalRecordIcd10::class, 'medical_record_id');
    }

    public function icd9s(): HasMany
    {
        return $this->hasMany(MedicalRecordIcd9::class, 'medical_record_id');
    }

    public function prescriptions(): HasMany
    {
        return $this->hasMany(MedicalRecordPrescription::class, 'medical_record_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updater(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }
}
