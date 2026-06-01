<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. MASTER PARAMETER LAB (Misal: Darah Rutin, Gula Darah, Kolesterol)
        Schema::create('master_labs', function (Blueprint $table) {
            $table->id();
            $table->string('nama_pemeriksaan');
            $table->string('nilai_normal')->nullable(); // Contoh: "70 - 110 mg/dL" atau "Negatif"
            $table->string('satuan', 20)->nullable();    // Contoh: "mg/dL", "g/dL"
            $table->decimal('tarif', 12, 2)->default(0);
            $table->boolean('is_aktif')->default(true);
            $table->timestamps();
        });

        // 2. INDUK PERMINTAAN LAB PER KUNJUNGAN
        Schema::create('permintaan_labs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pendaftaran_id')->constrained('pendaftarans')->onDelete('cascade');
            $table->foreignId('dokter_id')->nullable()->constrained('master_petugass')->onDelete('set null');
            $table->foreignId('analis_id')->nullable()->constrained('master_petugass')->onDelete('set null'); // Analis yang memeriksa
            
            $table->enum('status_lab', ['permintaan', 'selesai', 'batal'])->default('permintaan');
            $table->timestamps();
        });

        // 3. DETAIL HASIL LAB PER PARAMETER (Pivot/Detail)
        Schema::create('permintaan_lab_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('permintaan_lab_id')->constrained('permintaan_labs')->onDelete('cascade');
            $table->foreignId('master_lab_id')->constrained('master_labs')->onDelete('restrict');
            
            // Hasil Pemeriksaan oleh Analis
            $table->string('hasil_analis')->nullable(); // Nilai aktual yang keluar dari lab
            $table->text('kesan_kesimpulan')->nullable();
            
            // Kunci Tarif untuk Kasir
            $table->decimal('tarif_penerapan', 12, 2);
            
            // Bridging SatuSehat (DiagnosticReport / Observation Resource)
            $table->string('satu_sehat_lab_id')->nullable();
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('permintaan_labs');
        Schema::dropIfExists('permintaan_lab_details');
    }
};
