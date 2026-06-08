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
        Schema::create('master_satusehat_configs', function (Blueprint $table) {
            $table->id();
            $table->string('environment', 20)->default('sandbox');
            $table->string('client_id', 100);
            $table->string('client_secret', 100);
            $table->string('organization_id', 50);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('satusehat_tokens', function (Blueprint $table) {
            $table->id();
            $table->text('access_token');
            $table->timestamp('expires_at');
            $table->timestamps();
        });

        Schema::create('master_poli_satusehats', function (Blueprint $table) {
            $table->id();
            $table->foreignId('master_poli_id')->constrained('master_polis')->onDelete('cascade');
            $table->string('satusehat_location_id', 50);
            $table->timestamps();
        });

        Schema::create('satusehat_resource_mappings', function (Blueprint $table) {
            $table->id();
            // PERBAIKAN: Nama indeks disingkat menjadi 'ss_resource_idx' (hanya 15 karakter)
            $table->morphs('resourcable', 'ss_resource_idx');
            $table->string('satusehat_resource_id', 50)->index();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('satusehat_resource_mappings');
        Schema::dropIfExists('master_poli_satusehats');
        Schema::dropIfExists('satusehat_tokens');
        Schema::dropIfExists('master_satusehat_configs');
    }
};
