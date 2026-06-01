<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. TABEL MASTER TINDAKAN & TARIF
        Schema::create('master_tindakans', function (Blueprint $table) {
            $table->id();
            $table->string('nama_tindakan');
            $table->decimal('tarif', 12, 2)->default(0); // Nominal biaya tindakan
            
            // Keperluan Bridging
            $table->string('kode_icd9', 10)->constrained('icd9s')->onDelete('set null')->nullable(); // Kode ICD-9 CM standar BPJS/SatuSehat (contoh: 93.57)
            $table->string('nama_icd9')->nullable();
            
            $table->boolean('is_aktif')->default(true);
            $table->timestamps();
        });

        // 2. TABEL TRANSAKSI TINDAKAN PASIEN (Relasi Banyak ke Banyak / Pivot)
        Schema::create('tindakan_pasiens', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pendaftaran_id')->constrained('pendaftarans')->onDelete('cascade');
            $table->foreignId('master_tindakan_id')->constrained('master_tindakans')->onDelete('restrict');
            
            $table->integer('jumlah')->default(1); // Jumlah tindakan yang dilakukan
            $table->decimal('tarif_penerapan', 12, 2); // Mengunci harga saat tindakan dilakukan (mengantisipasi jika harga master naik di kemudian hari)
            $table->text('catatan')->nullable(); // Detail tambahan dari dokter
            
            // Bridging SatuSehat (Procedure Resource)
            $table->string('satu_sehat_procedure_id')->nullable();
            
            $table->foreignId('dokter_id')->nullable()->constrained('master_petugass')->onDelete('set null'); // Dokter eksekutor
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tindakan_pasiens');
        Schema::dropIfExists('master_tindakans');
    }
};