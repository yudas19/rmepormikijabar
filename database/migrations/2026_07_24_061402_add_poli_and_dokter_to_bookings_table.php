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
        Schema::table('bookings', function (Blueprint $table) {
            $table->unsignedBigInteger('poli_id')->nullable()->after('pasien_id');
            $table->unsignedBigInteger('dokter_id')->nullable()->after('poli_id');

            $table->foreign('poli_id')->references('id')->on('master_polis')->onDelete('set null');
            $table->foreign('dokter_id')->references('id')->on('master_petugass')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->dropForeign(['dokter_id']);
            $table->dropForeign(['poli_id']);
            $table->dropColumn(['dokter_id', 'poli_id']);
        });
    }
};
