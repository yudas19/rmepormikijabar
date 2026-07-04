<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MasterLabTest extends Model
{
    protected $table = 'master_lab_tests';

    protected $guarded = ['id'];

    protected $casts = [
        'tariff' => 'integer',
        'is_aktif' => 'boolean',
    ];

    /**
     * @var array<string, string>
     */
    public const CATEGORIES = [
        'Hematologi' => 'Hematologi',
        'Kimia Darah' => 'Kimia Darah',
        'Urinalisis' => 'Urinalisis',
        'Serologi' => 'Serologi',
        'Mikrobiologi' => 'Mikrobiologi',
        'Hormon' => 'Hormon',
        'Elektrolit' => 'Elektrolit',
        'Lain-lain' => 'Lain-lain',
    ];

    public function orderResults(): HasMany
    {
        return $this->hasMany(LabOrderResult::class, 'master_lab_test_id');
    }

    public function getTariffAttribute(): int
    {
        return (int) ($this->attributes['tarif_umum'] ?? $this->attributes['tariff'] ?? 0);
    }

    public function getFormattedTariffAttribute(): string
    {
        return 'Rp '.number_format($this->tariff, 0, ',', '.');
    }
}
