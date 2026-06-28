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
        if (! Schema::hasColumn('medical_record_tindakans', 'cara_bayar_item')) {
            Schema::table('medical_record_tindakans', function (Blueprint $table) {
                $table->enum('cara_bayar_item', ['umum', 'bpjs'])->default('umum')->after('subtotal');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasColumn('medical_record_tindakans', 'cara_bayar_item')) {
            Schema::table('medical_record_tindakans', function (Blueprint $table) {
                $table->dropColumn('cara_bayar_item');
            });
        }
    }
};
