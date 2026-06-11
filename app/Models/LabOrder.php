<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class LabOrder extends Model
{
    protected $table = 'lab_orders';

    protected $guarded = ['id'];

    protected $casts = [
        'total_tariff' => 'integer',
    ];

    public function medicalRecord(): BelongsTo
    {
        return $this->belongsTo(MedicalRecord::class, 'medical_record_id');
    }

    public function requester(): BelongsTo
    {
        return $this->belongsTo(MasterPetugas::class, 'requested_by_id');
    }

    public function results(): HasMany
    {
        return $this->hasMany(LabOrderResult::class, 'lab_order_id');
    }

    public function getFormattedTariffAttribute(): string
    {
        return 'Rp '.number_format($this->total_tariff, 0, ',', '.');
    }

    public function getStatusColorAttribute(): string
    {
        return match ($this->status) {
            'pending' => 'amber',
            'processing' => 'blue',
            'completed' => 'green',
            default => 'zinc',
        };
    }

    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'pending' => 'Menunggu',
            'processing' => 'Proses',
            'completed' => 'Selesai',
            default => ucfirst($this->status),
        };
    }
}
