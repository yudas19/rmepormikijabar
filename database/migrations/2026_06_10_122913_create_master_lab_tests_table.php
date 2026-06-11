<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('master_lab_tests', function (Blueprint $table) {
            $table->id();
            $table->string('test_name')->comment('Nama pemeriksaan, e.g. Gula Darah Sewaktu');
            $table->string('category', 100)->comment('Kategori: Hematologi, Kimia Darah, Urinalisis, dll.');
            $table->unsignedBigInteger('tariff')->default(0)->comment('Tarif dalam rupiah');
            $table->string('default_normal_range', 100)->nullable()->comment('Nilai rujukan normal, e.g. 70-140');
            $table->string('default_unit', 30)->nullable()->comment('Satuan hasil, e.g. mg/dL, g/dL, %');
            $table->boolean('is_aktif')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('master_lab_tests');
    }
};
