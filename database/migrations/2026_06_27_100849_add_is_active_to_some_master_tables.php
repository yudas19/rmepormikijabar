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
        Schema::table('master_aturan_pakais', function (Blueprint $table) {
            $table->boolean('is_active')->default(true)->after('nama_aturan_pakai');
        });

        Schema::table('master_pekerjaans', function (Blueprint $table) {
            $table->boolean('is_active')->default(true)->after('nama_pekerjaan');
        });

        Schema::table('master_pendidikans', function (Blueprint $table) {
            $table->boolean('is_active')->default(true)->after('nama_pendidikan');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('master_aturan_pakais', function (Blueprint $table) {
            $table->dropColumn('is_active');
        });

        Schema::table('master_pekerjaans', function (Blueprint $table) {
            $table->dropColumn('is_active');
        });

        Schema::table('master_pendidikans', function (Blueprint $table) {
            $table->dropColumn('is_active');
        });
    }
};
