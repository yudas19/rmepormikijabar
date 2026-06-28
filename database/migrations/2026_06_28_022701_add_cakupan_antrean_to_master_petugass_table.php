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
        if (! Schema::hasColumn('master_petugass', 'cakupan_antrean')) {
            Schema::table('master_petugass', function (Blueprint $table) {
                $table->enum('cakupan_antrean', ['semua_poli', 'hanya_poli_terpilih', 'hanya_dokter_bersangkutan'])
                    ->default('hanya_poli_terpilih')
                    ->after('jenis_petugas');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasColumn('master_petugass', 'cakupan_antrean')) {
            Schema::table('master_petugass', function (Blueprint $table) {
                $table->dropColumn('cakupan_antrean');
            });
        }
    }
};
