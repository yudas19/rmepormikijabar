<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Add dispensing status to prescriptions
        Schema::table('medical_record_prescriptions', function (Blueprint $table) {
            $table->enum('dispensing_status', ['waiting', 'dispensed'])->default('waiting')->after('catatan');
            $table->foreignId('apoteker_id')->nullable()->after('dispensing_status')->constrained('master_petugass')->nullOnDelete()->comment('Apoteker yang meracik/menyerahkan');
            $table->timestamp('dispensed_at')->nullable()->after('apoteker_id');
        });

        // Add dispensed columns to prescription items (doctor request vs pharmacist actual)
        Schema::table('medical_record_prescription_items', function (Blueprint $table) {
            // The existing master_obat_id + jumlah = doctor's request
            // New columns = pharmacist's actual dispensed
            $table->foreignId('dispensed_obat_id')->nullable()->after('satuan')->constrained('master_obats')->nullOnDelete()->comment('Obat yang sebenarnya diberikan (bisa beda dari permintaan dokter)');
            $table->decimal('dispensed_qty', 8, 2)->nullable()->after('dispensed_obat_id')->comment('Jumlah yang sebenarnya diberikan');
            $table->string('dispensed_signa')->nullable()->after('dispensed_qty')->comment('Aturan pakai yang diubah apoteker');
            $table->decimal('subtotal_price', 12, 2)->default(0)->after('dispensed_signa')->comment('harga_jual * dispensed_qty');
        });
    }

    public function down(): void
    {
        Schema::table('medical_record_prescription_items', function (Blueprint $table) {
            $table->dropForeign(['dispensed_obat_id']);
            $table->dropColumn(['dispensed_obat_id', 'dispensed_qty', 'dispensed_signa', 'subtotal_price']);
        });

        Schema::table('medical_record_prescriptions', function (Blueprint $table) {
            $table->dropForeign(['apoteker_id']);
            $table->dropColumn(['dispensing_status', 'apoteker_id', 'dispensed_at']);
        });
    }
};
