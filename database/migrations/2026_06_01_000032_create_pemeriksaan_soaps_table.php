<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pemeriksaan_soaps', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pendaftaran_id')->constrained('pendaftarans')->onDelete('cascade');
            // subjektif di isi oleh perawat
            $table->text('keluhan_utama');
            $table->text('riwayat_penyakit_sekarang')->nullable();
            $table->text('riwayat_alergi')->nullable();

            // --- 2. OBJEKTIF (Tanda-Tanda Vital & Pemeriksaan Fisik) ---
            // Umumnya dimulai oleh Perawat di ruang tensi (Triage)
            $table->string('tensi', 10)->nullable();       // Contoh: 120/80
            $table->integer('nadi')->nullable();          // x/menit
            $table->decimal('suhu', 4, 1)->nullable();     // Contoh: 36.5
            $table->integer('respirasi')->nullable();     // x/menit
            $table->decimal('berat_badan', 5, 2)->nullable(); // kg
            $table->integer('tinggi_badan')->nullable();   // cm
            $table->text('pemeriksaan_fisik_dokter')->nullable(); // Status lokalis/detail dari dokter

            // --- 3. ASESMEN (Diagnosa oleh Dokter) ---
            // Menyimpan kode ICD-10 untuk integrasi BPJS & SatuSehat
            $table->string('icd10_primer', 10)->nullable(); // Contoh: K04.0 (Pulpitis)
            $table->string('icd10_primer_nama')->nullable();
            $table->text('icd10_sekunder_json')->nullable(); // Menyimpan diagnosa penyerta jika lebih dari satu (format JSON)

            // --- 4. ASESMEN (Diagnosa oleh Dokter) ---
            // Menyimpan kode ICD-9 untuk integrasi BPJS & SatuSehat
            $table->string('icd9_primer', 10)->nullable(); // Contoh: 123.4 (Prosedur Operasi)
            $table->string('icd9_primer_nama')->nullable();
            $table->text('icd9_sekunder_json')->nullable(); // Menyimpan kode prosedur jika lebih dari satu (format JSON)

            // --- 5. PLAN (Rencana Penatalaksanaan oleh Dokter) ---
            $table->text('rencana_tatalaksana')->nullable();

            // --- Status Integrasi SatuSehat ---
            // Mengingat data SOAP dikirim terpisah (Condition & Observation) ke Kemenkes
            $table->string('satu_sehat_condition_id')->nullable(); // ID untuk Diagnosa
            $table->string('satu_sehat_observation_id')->nullable(); // ID untuk Vital Sign

            // Petugas yang bertanggung jawab
            $table->foreignId('perawat_id')->nullable()->constrained('master_petugass')->onDelete('set null');
            $table->foreignId('dokter_id')->nullable()->constrained('master_petugass')->onDelete('set null');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pemeriksaan_soaps');
    }
};
