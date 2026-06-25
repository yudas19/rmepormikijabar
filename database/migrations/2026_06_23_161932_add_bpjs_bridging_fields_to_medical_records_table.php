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
            $table->enum('bpjs_status', ['pending', 'processing', 'sent', 'failed'])
                ->default('pending')
                ->index()
                ->after('satusehat_error_log');
            $table->string('bpjs_kunjungan_no', 255)
                ->nullable()
                ->unique()
                ->after('bpjs_status');
            $table->text('bpjs_error_log')
                ->nullable()
                ->after('bpjs_kunjungan_no');
            $table->integer('bpjs_retry_count')
                ->default(0)
                ->after('bpjs_error_log');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('medical_records', function (Blueprint $table) {
            $table->dropColumn([
                'bpjs_status',
                'bpjs_kunjungan_no',
                'bpjs_error_log',
                'bpjs_retry_count',
            ]);
        });
    }
};
