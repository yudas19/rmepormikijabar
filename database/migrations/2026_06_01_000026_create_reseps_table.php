<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. TABEL MASTER OBAT & ALKES (Stok Gudang Farmasi)
        Schema::create('master_obats', function (Blueprint $table) {
            $table->id();
            $table->string('nama_obat');
            $table->string('satuan', 20); // Tablet, Sirup, Kapsul, Pcs, dll
            
            // Logika Harga & Stok
            $table->integer('stok')->default(0);
            $table->decimal('harga_beli', 12, 2)->default(0);
            $table->decimal('harga_jual', 12, 2)->default(0);
            
            // Keperluan Bridging SatuSehat
            $table->string('kode_kfa', 50)->nullable(); // Kode Kamus Farmasi & Alkes Kemenkes
            $table->string('nama_kfa')->nullable();
            
            $table->boolean('is_aktif')->default(true);
            $table->timestamps();
        });

        // 2. TABEL TRANSAKSI RESEP UTAMA (Induk Resep per Kunjungan)
        Schema::create('reseps', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pendaftaran_id')->constrained('pendaftarans')->onDelete('cascade');
            $table->foreignId('dokter_id')->nullable()->constrained('master_petugass')->onDelete('set null');
            
            $table->enum('status_resep', ['antrean', 'diproses', 'siap', 'diserahkan'])->default('antrean');
            $table->timestamps();
        });

        // 3. TABEL DETAIL RESEP (Item Obat yang Diberikan / Pivot)
        Schema::create('resep_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('resep_id')->constrained('reseps')->onDelete('cascade');
            $table->foreignId('master_obat_id')->constrained('master_obats')->onDelete('restrict');
            
            // Aturan Pakai
            $table->integer('jumlah_obat'); // Misal: 10
            $table->string('signa', 30);   // Aturan pakai, contoh: "3 x 1" atau "2 x 1/2"
            $table->string('catatan_pakai')->nullable(); // Contoh: "Sesudah makan", "Malam hari"
            
            // Kunci Harga untuk Kasir (Sama seperti logika tindakan)
            $table->decimal('harga_penerapan', 12, 2); 
            
            // Bridging SatuSehat (MedicationDispense Resource)
            $table->string('satu_sehat_medication_id')->nullable();
            
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('resep_details');
        Schema::dropIfExists('reseps');
        Schema::dropIfExists('master_obats');
    }
};