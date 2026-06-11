<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MasterObat extends Model
{
    protected $table = 'master_obats';

    protected $guarded = ['id'];

    protected $casts = [
        'harga_beli' => 'decimal:2',
        'harga_jual' => 'decimal:2',
        'is_aktif' => 'boolean',
        'tanggal_kadaluarsa' => 'date',
    ];

    public function stockMovements(): HasMany
    {
        return $this->hasMany(StockMovement::class, 'master_obat_id');
    }

    /**
     * Stock status: 'habis' | 'hampir_habis' | 'normal'
     */
    public function getStockStatusAttribute(): string
    {
        if ($this->stok_saat_ini <= 0) {
            return 'habis';
        }

        if ($this->stok_saat_ini <= $this->stok_minimal) {
            return 'hampir_habis';
        }

        return 'normal';
    }

    public function getStockStatusColorAttribute(): string
    {
        return match ($this->stock_status) {
            'habis' => 'red',
            'hampir_habis' => 'amber',
            default => 'green',
        };
    }

    public function getStockStatusLabelAttribute(): string
    {
        return match ($this->stock_status) {
            'habis' => 'Habis',
            'hampir_habis' => 'Hampir Habis',
            default => 'Tersedia',
        };
    }

    /**
     * Expiry status: 'expired' | 'expiring_soon' | 'ok' | null
     */
    public function getExpiryStatusAttribute(): ?string
    {
        if (! $this->tanggal_kadaluarsa) {
            return null;
        }

        if ($this->tanggal_kadaluarsa->isPast()) {
            return 'expired';
        }

        if ($this->tanggal_kadaluarsa->diffInMonths(now()) <= 6) {
            return 'expiring_soon';
        }

        return 'ok';
    }

    public function getFormattedPriceAttribute(): string
    {
        return 'Rp '.number_format($this->harga_jual, 0, ',', '.');
    }
}
