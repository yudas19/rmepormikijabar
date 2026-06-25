<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::rename('provinsis', 'master_provinsis');
        Schema::rename('kabupaten_kotas', 'master_kabupaten_kotas');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::rename('master_provinsis', 'provinsis');
        Schema::rename('master_kabupaten_kotas', 'kabupaten_kotas');
    }
};
