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
        Schema::create('kia_anc_records', function (Blueprint $table) {
            $table->id();
            $table->foreignId('medical_record_id')->constrained('medical_records')->onDelete('cascade')->unique();
            $table->date('hpht')->nullable()->comment('Hari Pertama Haid Terakhir');
            $table->date('tp')->nullable()->comment('Taksiran Persalinan (Naegele Rule)');
            $table->integer('uk_minggu')->nullable()->comment('Usia Kehamilan dalam minggu');
            $table->decimal('tfu', 4, 1)->nullable()->comment('Tinggi Fundus Uteri (cm)');
            $table->decimal('lila', 4, 1)->nullable()->comment('Lingkar Lengan Atas (cm)');
            $table->integer('djj')->nullable()->comment('Denyut Jantung Janin (bpm)');
            $table->string('presentasi')->nullable()->comment('Presentasi janin (Kepala/Bokong/Lintang)');
            $table->text('leopold_1')->nullable()->comment('Leopold I: Fundus uteri');
            $table->text('leopold_2')->nullable()->comment('Leopold II: Bagian samping');
            $table->text('leopold_3')->nullable()->comment('Leopold III: Bagian terbawah');
            $table->text('leopold_4')->nullable()->comment('Leopold IV: Masuk PAP');
            $table->string('golongan_darah')->nullable();
            $table->boolean('riwayat_sc')->default(false)->comment('Riwayat Sesar sebelumnya');
            $table->text('catatan_bidan')->nullable();
            $table->timestamps();

            $table->index('medical_record_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('kia_anc_records');
    }
};
