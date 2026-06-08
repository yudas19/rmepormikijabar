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
        Schema::create('surat_rujukans', function (Blueprint $table) {
            $table->id();
            $table->string('no_surat', 50)->unique();
            $table->foreignId('pendaftaran_id')->constrained('pendaftarans')->onDelete('cascade');
            $table->foreignId('pasien_id')->constrained('pasiens')->onDelete('cascade');
            $table->foreignId('dokter_id')->constrained('master_petugass')->onDelete('cascade');
            $table->string('faskes_tujuan');
            $table->string('diagnosa');
            $table->text('catatan')->nullable();
            $table->date('tanggal_rujukan');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('surat_rujukans');
    }
};
