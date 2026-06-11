<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MedicalRecordPrescriptionItem extends Model
{
    protected $table = 'medical_record_prescription_items';

    protected $guarded = ['id'];

    protected $casts = [
        'requested_qty' => 'decimal:2',
        'dispensed_qty' => 'decimal:2',
        'subtotal_price' => 'decimal:2',
    ];

    public function prescription(): BelongsTo
    {
        return $this->belongsTo(MedicalRecordPrescription::class, 'prescription_id');
    }

    public function requestedObat(): BelongsTo
    {
        return $this->belongsTo(MasterObat::class, 'requested_obat_id');
    }

    public function dispensedObat(): BelongsTo
    {
        return $this->belongsTo(MasterObat::class, 'dispensed_obat_id');
    }

    public function apoteker(): BelongsTo
    {
        return $this->belongsTo(User::class, 'apoteker_id');
    }
}
