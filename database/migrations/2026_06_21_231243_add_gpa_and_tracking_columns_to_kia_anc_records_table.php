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
        Schema::table('kia_anc_records', function (Blueprint $table) {
            $table->integer('g')->nullable()->comment('Gravida (Kehamilan ke-)');
            $table->integer('p')->nullable()->comment('Para (Melahirkan ke-)');
            $table->integer('a')->nullable()->comment('Abortus (Keguguran ke-)');
            $table->string('imunisasi_tt')->nullable()->comment('Status/Riwayat Imunisasi TT');
            $table->integer('tablet_fe')->nullable()->comment('Jumlah Tablet Fe (zat besi)');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('kia_anc_records', function (Blueprint $table) {
            $table->dropColumn(['g', 'p', 'a', 'imunisasi_tt', 'tablet_fe']);
        });
    }
};
