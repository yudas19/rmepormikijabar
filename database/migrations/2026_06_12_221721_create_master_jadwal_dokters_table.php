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
        Schema::create('master_jadwal_dokters', function (Blueprint $table) {
            $table->id();
            $table->foreignId('petugas_id')->constrained('master_petugass')->onDelete('cascade');
            $table->foreignId('poli_id')->constrained('master_polis')->onDelete('cascade');
            $table->string('hari'); // 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu', 'Minggu'
            $table->time('jam_mulai');
            $table->time('jam_selesai');
            $table->integer('kuota_pasien')->default(30);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('master_jadwal_dokters');
    }
};
