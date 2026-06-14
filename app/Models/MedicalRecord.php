<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

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
        'tanggal_kunjungan' => 'date',
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

    public function dokter(): BelongsTo
    {
        return $this->belongsTo(MasterPetugas::class, 'dokter_id');
    }

    public function perawat(): BelongsTo
    {
        return $this->belongsTo(MasterPetugas::class, 'perawat_id');
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

    public function odontogramRecords(): HasMany
    {
        return $this->hasMany(OdontogramRecord::class, 'medical_record_id');
    }

    public function kiaAncRecord(): HasOne
    {
        return $this->hasOne(KiaAncRecord::class, 'medical_record_id');
    }

    public function labOrders(): HasMany
    {
        return $this->hasMany(LabOrder::class, 'medical_record_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updater(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function tindakans(): BelongsToMany
    {
        return $this->belongsToMany(MasterTindakan::class, 'medical_record_tindakans', 'medical_record_id', 'master_tindakan_id')
            ->withPivot(['qty', 'subtotal'])
            ->withTimestamps();
    }

    public function invoice(): HasOne
    {
        return $this->hasOne(Invoice::class, 'medical_record_id');
    }

    /**
     * Evaluate if the medical record is valid for transmission to SatuSehat Kemenkes.
     *
     * @return array{status: string, missing: string[]}
     */
    public function evaluateSatusehatValidation(): array
    {
        $missing = [];

        // 1. Patient NIK & IHS Validation
        $patient = $this->pasien;
        if (! $patient) {
            $missing[] = 'Pasien Tidak Ditemukan';
        } else {
            if (empty($patient->nik)) {
                $missing[] = 'NIK Pasien Kosong';
            } elseif (strlen($patient->nik) !== 16) {
                $missing[] = 'NIK Pasien Harus 16 Digit';
            }

            if (empty($patient->ihs_number)) {
                $missing[] = 'IHS Pasien Kosong';
            }
        }

        // 2. Doctor IHS Practitioner Validation
        $dokter = $this->dokter;
        if (! $dokter) {
            $missing[] = 'Dokter Pemeriksa Kosong';
        } else {
            if (empty($dokter->ihs_number_practitioner)) {
                $missing[] = 'IHS Dokter Kosong';
            }
        }

        // 3. Location/Poli Location ID Validation
        $poli = $this->poli;
        if (! $poli) {
            $missing[] = 'Poli Kosong';
        } else {
            $locationId = $poli->satu_sehat_location_id ?: ($poli->satusehat?->satusehat_location_id ?? null);
            if (empty($locationId)) {
                $missing[] = 'ID Lokasi SatuSehat Poli Kosong';
            }
        }

        // 4. Vital Signs (TTV) Validation: Suhu, Tensi, Nadi
        if (is_null($this->temperature)) {
            $missing[] = 'Suhu Tubuh Belum Diisi';
        }
        if (is_null($this->tensi_sistole) || is_null($this->tensi_diastole)) {
            $missing[] = 'Tekanan Darah Belum Diisi';
        }
        if (is_null($this->pulse_rate)) {
            $missing[] = 'Nadi Belum Diisi';
        }

        // 5. Diagnosis Validation: Minimum one ICD-10
        if (! $this->icd10s()->exists()) {
            $missing[] = 'ICD-10 Belum Diisi';
        }

        $status = empty($missing) ? 'ready' : 'incomplete';

        return [
            'status' => $status,
            'missing' => $missing,
        ];
    }
}
