<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('medical_records', function (Blueprint $table) {
            $table->date('tanggal_kunjungan')->nullable()->after('pendaftaran_id');
            $table->string('status_panggilan')->default('belum')->after('status');
        });

        // Populate existing records with their created_at date
        DB::table('medical_records')->update([
            'tanggal_kunjungan' => DB::raw('date(created_at)'),
        ]);
    }

    public function down(): void
    {
        Schema::table('medical_records', function (Blueprint $table) {
            $table->dropColumn(['tanggal_kunjungan', 'status_panggilan']);
        });
    }
};
