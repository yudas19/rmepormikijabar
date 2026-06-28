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
        Schema::create('patient_referrals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('medical_record_id')->constrained('medical_records')->onDelete('cascade');
            $table->string('no_rujukan', 50)->unique();
            $table->string('ppk_dirujuk_kode', 20);
            $table->string('ppk_dirujuk_nama');
            $table->string('spesialis_kode', 20);
            $table->string('spesialis_nama');
            $table->string('subspesialis_kode', 20)->nullable();
            $table->string('sarana_kode', 20)->nullable();
            $table->string('sarana_nama')->nullable();
            $table->date('tgl_est_rujukan');
            $table->boolean('is_tacc')->default(false);
            $table->string('tacc_jenis', 50)->nullable();
            $table->text('tacc_alasan')->nullable();
            $table->string('diagnosa_utama_kode', 20);
            $table->string('diagnosa_utama_nama')->nullable();
            $table->text('diagnosa_sekunder')->nullable(); // JSON list of secondary ICD-10s
            $table->text('response_json')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('patient_referrals');
    }
};
