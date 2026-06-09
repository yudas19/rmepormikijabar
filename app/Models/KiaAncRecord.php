<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class KiaAncRecord extends Model
{
    protected $table = 'kia_anc_records';

    protected $guarded = ['id'];

    protected $casts = [
        'hpht' => 'date',
        'tp' => 'date',
        'uk_minggu' => 'integer',
        'tfu' => 'decimal:1',
        'lila' => 'decimal:1',
        'djj' => 'integer',
        'riwayat_sc' => 'boolean',
    ];

    public function medicalRecord(): BelongsTo
    {
        return $this->belongsTo(MedicalRecord::class, 'medical_record_id');
    }
}
