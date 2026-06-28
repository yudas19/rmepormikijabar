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
        Schema::table('master_pcares', function (Blueprint $table) {
            $table->string('bpjs_env', 20)->default('development')->after('is_bpjs');
            $table->string('bpjs_cons_id', 50)->nullable()->after('bpjs_env');
            $table->string('bpjs_secret_key', 100)->nullable()->after('bpjs_cons_id');
            $table->string('bpjs_user_key', 100)->nullable()->after('bpjs_secret_key');
            $table->string('pcare_username', 100)->nullable()->after('bpjs_user_key');
            $table->string('pcare_password', 100)->nullable()->after('pcare_username');
            $table->string('user_mjkn', 100)->nullable()->after('pcare_password');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('master_pcares', function (Blueprint $table) {
            $table->dropColumn([
                'bpjs_env',
                'bpjs_cons_id',
                'bpjs_secret_key',
                'bpjs_user_key',
                'pcare_username',
                'pcare_password',
                'user_mjkn',
            ]);
        });
    }
};
