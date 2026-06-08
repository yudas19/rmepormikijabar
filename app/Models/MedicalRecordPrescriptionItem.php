<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MedicalRecordPrescriptionItem extends Model
{
    protected $table = 'medical_record_prescription_items';

    protected $guarded = ['id'];

    protected $casts = [
        'jumlah' => 'decimal:2',
    ];

    public function prescription(): BelongsTo
    {
        return $this->belongsTo(MedicalRecordPrescription::class, 'prescription_id');
    }

    public function masterObat(): BelongsTo
    {
        return $this->belongsTo(MasterObat::class, 'master_obat_id');
    }
}
