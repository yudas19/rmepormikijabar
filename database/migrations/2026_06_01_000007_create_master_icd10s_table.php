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
        Schema::create('master_icd10s', function (Blueprint $table) {
            $table->id();
            $table->string('kode', 15)->unique(); // Contoh: "A00.0"
            $table->string('nama_penyakit');               // Contoh: "Cholera due to Vibrio cholerae 01, biovar cholerae"
            $table->string('nama_penyakit_indonesia')->nullable(); // Terjemahan bahasa Indonesia (opsional, sangat berguna untuk dokter)
            $table->timestamps();

            // Index untuk pencarian cepat di Livewire
            $table->index('nama_penyakit');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('master_icd10s');
    }
};
