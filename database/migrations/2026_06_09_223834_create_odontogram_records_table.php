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
        Schema::create('odontogram_records', function (Blueprint $table) {
            $table->id();
            $table->foreignId('medical_record_id')->constrained('medical_records')->onDelete('cascade');
            $table->tinyInteger('tooth_number')->unsigned()->comment('Adult: 11-48, Child: 51-85');
            $table->string('condition_code', 5)->comment('SOU|CAR|MIS|FML|FRA|CFR|NON');
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->unique(['medical_record_id', 'tooth_number']);
            $table->index('medical_record_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('odontogram_records');
    }
};
