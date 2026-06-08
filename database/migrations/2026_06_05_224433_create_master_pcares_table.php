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
        Schema::create('master_pcares', function (Blueprint $table) {
            $table->id();
            $table->string('kode_pcare', 20)->unique();
            $table->string('nama_pcare', 200);
            $table->string('kode_rs', 10);
            $table->string('kode_wilayah', 50);
            $table->string('kode_provinsi', 2);
            $table->string('kode_kabupaten', 4);
            $table->string('kode_kecamatan', 7);
            $table->string('nama_propinsi', 50);
            $table->string('nama_kabupaten', 50);
            $table->string('nama_kecamatan', 50);
            $table->string('alamat', 255)->nullable();
            $table->string('telepon', 20)->nullable();
            $table->string('email', 100)->nullable();
            $table->string('kode_faskes', 20)->nullable();
            $table->string('nama_faskes', 200)->nullable();
            $table->string('jenis_faskes', 20)->nullable();
            $table->string('tipe_faskes', 20)->nullable();
            $table->string('tipe_layanan', 20)->nullable();
            $table->boolean('is_bpjs')->default(false);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('master_pcares');
    }
};
