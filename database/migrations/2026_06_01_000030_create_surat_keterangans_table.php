<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('surat_keterangans', function (Blueprint $table) {
            $table->id();
            $table->string('no_surat', 50)->unique(); // Contoh: 001/SKD/Klinik/V/2026
            $table->foreignId('pendaftaran_id')->constrained('pendaftarans')->onDelete('cascade');
            $table->foreignId('pasien_id')->constrained('pasiens')->onDelete('cascade');
            $table->foreignId('dokter_id')->constrained('master_petugass')->onDelete('cascade');

            $table->enum('jenis_surat', ['sehat', 'sakit', 'bebas_narkoba']);

            // Isi fleksibel tergantung jenis surat (disimpan dalam bentuk JSON)
            // Misal untuk surat sakit: tgl_mulai, tgl_selesai, jumlah_hari
            // Misal untuk surat sehat: tinggi_badan, berat_badan, buta_warna (ya/tidak)
            $table->json('konten_surat');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('surat_keterangans');
    }
};
