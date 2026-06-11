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
        $isSqlite = Schema::getConnection()->getDriverName() === 'sqlite';

        if (! $isSqlite) {
            Schema::table('medical_record_prescription_items', function (Blueprint $table) {
                // Drop existing foreign key
                $table->dropForeign('medical_record_prescription_items_master_obat_id_foreign');
            });
        }

        Schema::table('medical_record_prescription_items', function (Blueprint $table) {
            // Rename columns
            $table->renameColumn('master_obat_id', 'requested_obat_id');
            $table->renameColumn('jumlah', 'requested_qty');
        });

        Schema::table('medical_record_prescription_items', function (Blueprint $table) use ($isSqlite) {
            $table->string('requested_signa')->nullable()->after('satuan')->comment('Signa original dari dokter');
            if ($isSqlite) {
                $table->unsignedBigInteger('apoteker_id')->nullable()->after('subtotal_price');
            } else {
                $table->foreign('requested_obat_id')->references('id')->on('master_obats')->onDelete('restrict');
                $table->foreignId('apoteker_id')->nullable()->after('subtotal_price')->constrained('users')->nullOnDelete()->comment('Apoteker yang meracik/menyerahkan obat ini');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $isSqlite = Schema::getConnection()->getDriverName() === 'sqlite';

        Schema::table('medical_record_prescription_items', function (Blueprint $table) use ($isSqlite) {
            if (! $isSqlite) {
                $table->dropForeign(['apoteker_id']);
                $table->dropForeign('medical_record_prescription_items_requested_obat_id_foreign');
            }
            $table->dropColumn(['apoteker_id', 'requested_signa']);
        });

        Schema::table('medical_record_prescription_items', function (Blueprint $table) {
            $table->renameColumn('requested_obat_id', 'master_obat_id');
            $table->renameColumn('requested_qty', 'jumlah');
        });

        if (! $isSqlite) {
            Schema::table('medical_record_prescription_items', function (Blueprint $table) {
                $table->foreign('master_obat_id')->references('id')->on('master_obats')->onDelete('restrict');
            });
        }
    }
};
