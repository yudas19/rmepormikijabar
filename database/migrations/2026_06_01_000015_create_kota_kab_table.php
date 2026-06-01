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
        Schema::create('kabupaten_kotas', function (Blueprint $table) {
            $table->id();
            $table->string('kode_kabupaten_kota')->unique()->nullable();
            $table->string('nama_kabupaten_kota');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('kabupaten_kotas');
    }
};
