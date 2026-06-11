<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('lab_orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('medical_record_id')->constrained('medical_records')->onDelete('cascade');
            $table->foreignId('requested_by_id')->nullable()->constrained('master_petugass')->nullOnDelete()->comment('Dokter yang meminta');
            $table->enum('status', ['pending', 'processing', 'completed'])->default('pending');
            $table->unsignedBigInteger('total_tariff')->default(0)->comment('Total tarif semua tes yang dipesan');
            $table->text('clinical_notes')->nullable()->comment('Catatan klinis dari dokter perujuk');
            $table->timestamps();

            $table->index('medical_record_id');
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('lab_orders');
    }
};
