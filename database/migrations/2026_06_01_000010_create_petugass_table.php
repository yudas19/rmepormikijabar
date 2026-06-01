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
        Schema::create('master_petugass', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('set null'); // Link ke akun login
            
            $table->string('nama_petugas');
            $table->string('nik', 16)->unique();
            $table->string('tempat_lahir')->nullable();
            $table->date('tanggal_lahir')->nullable();
            $table->string('alamat')->nullable();
            $table->string('telepon', 20)->nullable();
            $table->date('bekerja_sejak', 50)->nullable();
            $table->enum('jenis_petugas', ['admin','rekam_medis', 'dokter_umum', 'dokter_gigi', 'perawat', 'bidan', 'analis_lab', 'apoteker', 'kasir']);
            
            // Keperluan Medis & Bridging
            $table->string('nomor_str', 50)->nullable();
            $table->string('nomor_sip', 50)->nullable();
            $table->string('ihs_number_practitioner')->nullable();
            
            $table->string('no_hp', 15)->nullable();
            $table->boolean('is_aktif')->default(true);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('master_petugass');
    }
};
