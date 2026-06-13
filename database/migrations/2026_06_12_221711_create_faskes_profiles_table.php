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
        Schema::create('faskes_profiles', function (Blueprint $table) {
            $table->id();
            $table->string('nama_faskes');
            $table->string('logo_path')->nullable();
            $table->string('alamat');
            $table->string('penanggung_jawab');
            $table->string('no_telp');
            $table->string('email');
            $table->string('kode_faskes_kemenkes');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('faskes_profiles');
    }
};
