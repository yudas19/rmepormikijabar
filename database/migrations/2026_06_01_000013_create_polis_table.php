<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('master_polis', function (Blueprint $table) {
            $table->id();
            $table->string('kode_poli', 10)->unique();
            $table->string('nama_poli', 50);
            $table->boolean('is_active')->default(true);

            // keperluan bridging bpjs dan satusehat
            $table->string('kode_poli_bpjs', 10)->nullable();
            $table->string('satu_sehat_location_id')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('master_polis');
    }
};
