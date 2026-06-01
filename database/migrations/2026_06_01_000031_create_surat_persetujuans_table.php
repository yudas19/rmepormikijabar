<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('surat_persetujuans', function (Blueprint $table) {
            $table->id();
            $table->string('no_surat', 50)->unique();
            $table->foreignId('pendaftaran_id')->constrained('pendaftarans')->onDelete('cascade');
            
            $table->enum('jenis_persetujuan', ['general_consent', 'informed_consent_tindakan']);
            
            // Data Penanggung Jawab / Yang Memberi Persetujuan
            $table->string('nama_penanggung_jawab'); // Bisa pasien sendiri atau keluarga
            $table->string('hubungan_penanggung_jawab'); // diri sendiri, suami, istri, ayah, ibu
            $table->string('nik_penanggung_jawab', 16)->nullable();
            
            // Teks deskripsi tindakan jika jenisnya informed_consent_tindakan
            $table->string('nama_tindakan_medis')->nullable(); 
            
            // Status Persetujuan
            $table->enum('pernyataan', ['setuju', 'menolak'])->default('setuju');
            
            // Tanda Tangan Digital (Bisa simpan dalam bentuk koordinat data:image/png;base64)
            $table->longText('ttd_pasien_atau_keluarga')->nullable();
            $table->longText('ttd_saksi_klinik')->nullable(); // Perawat atau petugas admin
            
            $table->foreignId('petugas_id')->constrained('master_petugass')->onDelete('restrict');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('surat_persetujuans');
    }
};
