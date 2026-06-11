<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StockMovement extends Model
{
    protected $table = 'stock_movements';

    protected $guarded = ['id'];

    protected $casts = [
        'opname_date' => 'date',
    ];

    public function masterObat(): BelongsTo
    {
        return $this->belongsTo(MasterObat::class, 'master_obat_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function prescription(): BelongsTo
    {
        return $this->belongsTo(MedicalRecordPrescription::class, 'prescription_id');
    }

    public function getTypeLabelAttribute(): string
    {
        return match ($this->type) {
            'in' => 'Stok Masuk',
            'out' => 'Stok Keluar',
            'opname_adjustment' => 'Opname',
            default => ucfirst($this->type),
        };
    }

    public function getTypeColorAttribute(): string
    {
        return match ($this->type) {
            'in' => 'green',
            'out' => 'red',
            'opname_adjustment' => 'blue',
            default => 'zinc',
        };
    }
}
