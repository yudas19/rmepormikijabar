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
        Schema::table('master_pekerjaans', function (Blueprint $table) {
            $table->foreignId('clinic_id')->nullable()->constrained('faskes_profiles')->onDelete('cascade');
        });

        Schema::table('master_pendidikans', function (Blueprint $table) {
            $table->foreignId('clinic_id')->nullable()->constrained('faskes_profiles')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('master_pekerjaans', function (Blueprint $table) {
            $table->dropForeign(['clinic_id']);
            $table->dropColumn('clinic_id');
        });

        Schema::table('master_pendidikans', function (Blueprint $table) {
            $table->dropForeign(['clinic_id']);
            $table->dropColumn('clinic_id');
        });
    }
};
