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
        // 1. Drop old foreign key constraints pointing to provinsis and kabupaten_kotas
        Schema::table('pasiens', function (Blueprint $table) {
            $table->dropForeign(['provinsi_id']);
            $table->dropForeign(['kabupaten_kota_id']);
        });

        // 2. Re-establish foreign keys to master_provinsis and master_kabupaten_kotas, drop string columns, and add new foreign keys
        Schema::table('pasiens', function (Blueprint $table) {
            $table->foreign('provinsi_id')->references('id')->on('master_provinsis')->onDelete('set null');
            $table->foreign('kabupaten_kota_id')->references('id')->on('master_kabupaten_kotas')->onDelete('set null');

            $table->dropColumn(['tempat_lahir', 'agama', 'pendidikan', 'pekerjaan']);

            $table->foreignId('tempat_lahir_kabupaten_id')->nullable()->constrained('master_kabupaten_kotas')->onDelete('set null');
            $table->foreignId('master_agama_id')->nullable()->constrained('master_agamas')->onDelete('set null');
            $table->foreignId('master_pendidikan_id')->nullable()->constrained('master_pendidikans')->onDelete('set null');
            $table->foreignId('master_pekerjaan_id')->nullable()->constrained('master_pekerjaans')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pasiens', function (Blueprint $table) {
            $table->dropForeign(['provinsi_id']);
            $table->dropForeign(['kabupaten_kota_id']);
            $table->dropForeign(['tempat_lahir_kabupaten_id']);
            $table->dropForeign(['master_agama_id']);
            $table->dropForeign(['master_pendidikan_id']);
            $table->dropForeign(['master_pekerjaan_id']);

            $table->dropColumn([
                'tempat_lahir_kabupaten_id',
                'master_agama_id',
                'master_pendidikan_id',
                'master_pekerjaan_id',
            ]);
        });

        Schema::table('pasiens', function (Blueprint $table) {
            $table->string('tempat_lahir')->nullable();
            $table->string('agama', 20)->nullable();
            $table->string('pendidikan', 30)->nullable();
            $table->string('pekerjaan', 50)->nullable();

            $table->foreign('provinsi_id')->references('id')->on('provinsis')->onDelete('set null');
            $table->foreign('kabupaten_kota_id')->references('id')->on('kabupaten_kotas')->onDelete('set null');
        });
    }
};
