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
        Schema::table('medical_records', function (Blueprint $table) {
            $table->enum('satusehat_status', ['incomplete', 'ready', 'sent', 'failed'])->default('incomplete')->index();
            $table->string('satusehat_encounter_id')->nullable();
            $table->string('satusehat_condition_id')->nullable();
            $table->text('satusehat_error_log')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('medical_records', function (Blueprint $table) {
            $table->dropColumn([
                'satusehat_status',
                'satusehat_encounter_id',
                'satusehat_condition_id',
                'satusehat_error_log',
            ]);
        });
    }
};
