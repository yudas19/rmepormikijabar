<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{

public function up(): void
    {
        Schema::create('pendaftarans', function (Blueprint $table) {
            $table->id();
            $table->string('no_registrasi', 20)->unique(); // Contoh: REG-20260525-0001
            $table->foreignId('pasien_id')->constrained('pasiens')->onDelete('restrict');
            $table->unsignedBigInteger('poli_id');
            $table->unsignedBigInteger('dokter_id');
            
            // Sistem Antrean
            $table->string('no_antrean', 5);
            $table->integer('angka_antrean');
            $table->enum('status_antrean', ['menunggu', 'dipanggil','pemeriksaan ttv', 'diperiksa', 'selesai', 'batal'])->default('menunggu');
            
            // Metode Pembayaran & Bridging BPJS
            $table->enum('cara_bayar', ['Umum', 'BPJS', 'Dinas/Instansi']);
            $table->string('no_sep', 30)->nullable();
            $table->string('no_rujukan', 30)->nullable();
            
            // Bridging SatuSehat (Encounter/Kunjungan ID)
            $table->string('satu_sehat_encounter_id')->nullable();
            
            $table->enum('jenis_kunjungan', ['Baru', 'Lama', 'Kontrol']);
            $table->string('keluhan_awal')->nullable();
            
            // Tracking User & Waktu
            $table->unsignedBigInteger('created_by')->nullable();
            $table->foreign('created_by')->references('id')->on('users')->onDelete('set null');
            $table->timestamps();
            
            // Indexing untuk performa pencarian harian dan antrean
            $table->index('status_antrean');
            $table->index('cara_bayar');
            $table->index(['created_at', 'poli_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pendaftarans');
    }
};