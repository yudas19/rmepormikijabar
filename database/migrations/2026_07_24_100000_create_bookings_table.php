<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bookings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pasien_id')->constrained('pasiens')->onDelete('cascade');
            $table->date('booking_date');
            $table->enum('status', ['pending', 'confirmed', 'cancelled'])->default('pending');
            $table->unsignedBigInteger('confirmed_by')->nullable();
            $table->foreign('confirmed_by')->references('id')->on('users')->onDelete('set null');
            $table->timestamp('confirmed_at')->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->foreign('created_by')->references('id')->on('users')->onDelete('set null');
            $table->timestamps();

            $table->index(['booking_date', 'status']);
            $table->index('pasien_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bookings');
    }
};
