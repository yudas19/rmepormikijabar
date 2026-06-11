<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('master_obats', function (Blueprint $table) {
            $table->string('kode_obat', 50)->nullable()->after('id')->comment('Kode internal obat');
            $table->integer('stok_minimal')->default(100)->after('stok')->comment('Threshold warning stok minimum');
            $table->date('tanggal_kadaluarsa')->nullable()->after('stok_minimal')->comment('Expiry date obat');
        });

        // Rename stok → stok_saat_ini for clarity
        Schema::table('master_obats', function (Blueprint $table) {
            $table->renameColumn('stok', 'stok_saat_ini');
        });
    }

    public function down(): void
    {
        Schema::table('master_obats', function (Blueprint $table) {
            $table->renameColumn('stok_saat_ini', 'stok');
        });

        Schema::table('master_obats', function (Blueprint $table) {
            $table->dropColumn(['kode_obat', 'stok_minimal', 'tanggal_kadaluarsa']);
        });
    }
};
