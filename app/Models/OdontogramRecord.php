<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OdontogramRecord extends Model
{
    protected $table = 'odontogram_records';

    protected $guarded = ['id'];

    protected $casts = [
        'tooth_number' => 'integer',
    ];

    /**
     * Valid condition codes for dental status.
     *
     * @var array<string, string>
     */
    public const CONDITION_CODES = [
        'SOU' => 'Sound (Sehat)',
        'CAR' => 'Caries (Karies)',
        'MIS' => 'Missing (Hilang/Cabut)',
        'FML' => 'Filled (Ditambal)',
        'FRA' => 'Fracture (Fraktur)',
        'CFR' => 'Crown Fracture',
        'NON' => 'Not Recorded',
    ];

    /**
     * Tailwind color classes per condition code.
     *
     * @var array<string, string>
     */
    public const CONDITION_COLORS = [
        'SOU' => 'green',
        'CAR' => 'red',
        'MIS' => 'zinc',
        'FML' => 'blue',
        'FRA' => 'orange',
        'CFR' => 'yellow',
        'NON' => 'zinc',
    ];

    public function medicalRecord(): BelongsTo
    {
        return $this->belongsTo(MedicalRecord::class, 'medical_record_id');
    }
}
