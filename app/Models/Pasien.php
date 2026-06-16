<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Pasien extends Model
{
    use SoftDeletes;

    protected $table = 'pasiens';

    // Guarded kosong artinya semua kolom boleh diisi (mass assignment)
    protected $guarded = ['id'];

    // Cast tanggal_lahir ke format date otomatis saat diambil dari database
    protected $casts = [
        'tanggal_lahir' => 'date',
    ];

    // Relasi: Satu pasien bisa memiliki riwayat banyak pendaftaran/kunjungan
    public function pendaftarans(): HasMany
    {
        return $this->hasMany(Pendaftaran::class, 'pasien_id');
    }

    /**
     * Bootstrap the model and its traits.
     */
    protected static function booted(): void
    {
        static::saving(function (Pasien $pasien) {
            if (is_null($pasien->ihs_number) || trim($pasien->ihs_number) === '') {
                $pasien->ihs_number = null;
            }
            if (is_null($pasien->no_bpjs) || trim($pasien->no_bpjs) === '') {
                $pasien->no_bpjs = null;
            }
        });
    }
}
