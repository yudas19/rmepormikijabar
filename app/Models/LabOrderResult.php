<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LabOrderResult extends Model
{
    protected $table = 'lab_order_results';

    protected $guarded = ['id'];

    protected $casts = [
        'tariff_snapshot' => 'integer',
        'is_abnormal' => 'boolean',
    ];

    public function labOrder(): BelongsTo
    {
        return $this->belongsTo(LabOrder::class, 'lab_order_id');
    }

    public function masterLabTest(): BelongsTo
    {
        return $this->belongsTo(MasterLabTest::class, 'master_lab_test_id');
    }

    public function analis(): BelongsTo
    {
        return $this->belongsTo(MasterPetugas::class, 'analis_id');
    }

    public function getFormattedTariffAttribute(): string
    {
        return 'Rp '.number_format($this->tariff_snapshot, 0, ',', '.');
    }
}
