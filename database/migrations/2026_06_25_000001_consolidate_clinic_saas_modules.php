<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class ConsolidateClinicSaasModules extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. master_polis: add jenis_unit
        if (! Schema::hasColumn('master_polis', 'jenis_unit')) {
            Schema::table('master_polis', function (Blueprint $table) {
                $table->enum('jenis_unit', ['medis', 'penunjang'])->default('medis')->after('is_active');
            });
        }

        // 2. lab_orders: make medical_record_id nullable and add pendaftaran_id
        Schema::table('lab_orders', function (Blueprint $table) {
            $table->foreignId('medical_record_id')->nullable()->change();
            if (! Schema::hasColumn('lab_orders', 'pendaftaran_id')) {
                $table->foreignId('pendaftaran_id')->nullable()->after('medical_record_id')->constrained('pendaftarans')->onDelete('cascade');
            }
        });

        // 3. invoices: make medical_record_id nullable and add pendaftaran_id
        Schema::table('invoices', function (Blueprint $table) {
            $table->foreignId('medical_record_id')->nullable()->change();
            if (! Schema::hasColumn('invoices', 'pendaftaran_id')) {
                $table->foreignId('pendaftaran_id')->nullable()->after('medical_record_id')->constrained('pendaftarans')->onDelete('restrict');
            }
        });

        // 4. master_tindakans: add tarif_umum and tarif_bpjs
        if (! Schema::hasColumn('master_tindakans', 'tarif_umum')) {
            Schema::table('master_tindakans', function (Blueprint $table) {
                $table->decimal('tarif_umum', 12, 2)->default(0)->after('tarif');
                $table->decimal('tarif_bpjs', 12, 2)->default(0)->after('tarif_umum');
            });
            // Copy existing tarif to new columns
            DB::statement('UPDATE master_tindakans SET tarif_umum = tarif, tarif_bpjs = tarif');
        }

        // 5. master_lab_tests: add tarif_umum and tarif_bpjs
        if (! Schema::hasColumn('master_lab_tests', 'tarif_umum')) {
            Schema::table('master_lab_tests', function (Blueprint $table) {
                $table->unsignedBigInteger('tarif_umum')->default(0)->after('tariff');
                $table->unsignedBigInteger('tarif_bpjs')->default(0)->after('tarif_umum');
            });
            // Copy existing tariff to new columns
            DB::statement('UPDATE master_lab_tests SET tarif_umum = tariff, tarif_bpjs = tariff');
        }

        // 6. invoice_items: add cara_bayar_item
        if (! Schema::hasColumn('invoice_items', 'cara_bayar_item')) {
            Schema::table('invoice_items', function (Blueprint $table) {
                $table->enum('cara_bayar_item', ['umum', 'bpjs'])->default('umum')->after('unit_price');
                $table->index('cara_bayar_item');
            });
        }

        // 7. medical_record_tindakans: add cara_bayar_item
        if (! Schema::hasColumn('medical_record_tindakans', 'cara_bayar_item')) {
            Schema::table('medical_record_tindakans', function (Blueprint $table) {
                $table->enum('cara_bayar_item', ['umum', 'bpjs'])->default('umum')->after('subtotal');
            });
        }

        // 8. lab_order_results: add cara_bayar_item
        if (! Schema::hasColumn('lab_order_results', 'cara_bayar_item')) {
            Schema::table('lab_order_results', function (Blueprint $table) {
                $table->enum('cara_bayar_item', ['umum', 'bpjs'])->default('umum')->after('result_value');
            });
        }

        // 9. medical_record_prescription_items: add cara_bayar_item
        if (! Schema::hasColumn('medical_record_prescription_items', 'cara_bayar_item')) {
            Schema::table('medical_record_prescription_items', function (Blueprint $table) {
                $table->enum('cara_bayar_item', ['umum', 'bpjs'])->default('umum')->after('subtotal_price');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasColumn('master_polis', 'jenis_unit')) {
            Schema::table('master_polis', function (Blueprint $table) {
                $table->dropColumn('jenis_unit');
            });
        }

        Schema::table('lab_orders', function (Blueprint $table) {
            if (Schema::hasColumn('lab_orders', 'pendaftaran_id')) {
                $table->dropForeign(['pendaftaran_id']);
                $table->dropColumn('pendaftaran_id');
            }
        });

        Schema::table('invoices', function (Blueprint $table) {
            if (Schema::hasColumn('invoices', 'pendaftaran_id')) {
                $table->dropForeign(['pendaftaran_id']);
                $table->dropColumn('pendaftaran_id');
            }
        });

        if (Schema::hasColumn('master_tindakans', 'tarif_umum')) {
            Schema::table('master_tindakans', function (Blueprint $table) {
                $table->dropColumn(['tarif_umum', 'tarif_bpjs']);
            });
        }

        if (Schema::hasColumn('master_lab_tests', 'tarif_umum')) {
            Schema::table('master_lab_tests', function (Blueprint $table) {
                $table->dropColumn(['tarif_umum', 'tarif_bpjs']);
            });
        }

        if (Schema::hasColumn('invoice_items', 'cara_bayar_item')) {
            Schema::table('invoice_items', function (Blueprint $table) {
                $table->dropColumn('cara_bayar_item');
            });
        }

        if (Schema::hasColumn('medical_record_tindakans', 'cara_bayar_item')) {
            Schema::table('medical_record_tindakans', function (Blueprint $table) {
                $table->dropColumn('cara_bayar_item');
            });
        }

        if (Schema::hasColumn('lab_order_results', 'cara_bayar_item')) {
            Schema::table('lab_order_results', function (Blueprint $table) {
                $table->dropColumn('cara_bayar_item');
            });
        }

        if (Schema::hasColumn('medical_record_prescription_items', 'cara_bayar_item')) {
            Schema::table('medical_record_prescription_items', function (Blueprint $table) {
                $table->dropColumn('cara_bayar_item');
            });
        }
    }
}
