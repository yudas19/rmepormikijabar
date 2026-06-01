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
        Schema::create('master_icd9s', function (Blueprint $table) {
            $table->id();
            $table->string('kode', 15)->unique(); // Contoh: "93.57"
            $table->string('nama');               // Contoh: "Application of wound dressing"
            $table->timestamps();

            // Index untuk pencarian cepat di Livewire
            $table->index('nama');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('master_icd9s');
    }
};
