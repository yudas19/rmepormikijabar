<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Pasien extends Model
{
    use SoftDeletes;

    protected $table = 'pasiens';

    protected $fillable = [
        'no_rekam_medis',
        'nama_pasien',
        'panggilan',
        'nik',
        'no_bpjs',
        'ihs_number',
        'gelar',
        'tempat_lahir_kabupaten_id',
        'tanggal_lahir',
        'jenis_kelamin',
        'golongan_darah',
        'nama_orangtua',
        'nrp',
        'keluarga_anggota',
        'hubungan_keluarga',
        'status_perkawinan',
        'suku',
        'master_agama_id',
        'master_pendidikan_id',
        'master_pekerjaan_id',
        'kewarganegaraan',
        'bahasa',
        'no_whatsapp',
        'email',
        'alamat',
        'provinsi_id',
        'kabupaten_kota_id',
        'kode_pos',
        'status_pasien',
        'created_by',
        'updated_by',
    ];

    // Cast tanggal_lahir ke format date otomatis saat diambil dari database
    protected $casts = [
        'tanggal_lahir' => 'date',
    ];

    // Relasi: Satu pasien bisa memiliki riwayat banyak pendaftaran/kunjungan
    public function pendaftarans(): HasMany
    {
        return $this->hasMany(Pendaftaran::class, 'pasien_id');
    }

    public function tempatLahirKabupaten(): BelongsTo
    {
        return $this->belongsTo(KabupatenKota::class, 'tempat_lahir_kabupaten_id');
    }

    public function agama(): BelongsTo
    {
        return $this->belongsTo(MasterAgama::class, 'master_agama_id');
    }

    public function pendidikan(): BelongsTo
    {
        return $this->belongsTo(MasterPendidikan::class, 'master_pendidikan_id');
    }

    public function pekerjaan(): BelongsTo
    {
        return $this->belongsTo(MasterPekerjaan::class, 'master_pekerjaan_id');
    }

    public function provinsi(): BelongsTo
    {
        return $this->belongsTo(Provinsi::class, 'provinsi_id');
    }

    public function kabupatenKota(): BelongsTo
    {
        return $this->belongsTo(KabupatenKota::class, 'kabupaten_kota_id');
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
