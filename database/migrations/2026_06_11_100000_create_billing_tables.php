<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Add kategori to master_tindakans
        Schema::table('master_tindakans', function (Blueprint $table) {
            $table->string('kategori')->nullable()->after('tarif');
        });

        // Seed some basic data
        DB::table('master_tindakans')->insert([
            [
                'nama_tindakan' => 'Konsultasi Dokter Umum',
                'tarif' => 30000.00,
                'kategori' => 'Konsultasi',
                'is_aktif' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nama_tindakan' => 'Konsultasi Dokter Gigi',
                'tarif' => 50000.00,
                'kategori' => 'Konsultasi',
                'is_aktif' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);

        // 2. Change medical_records status from enum to string to support 'completed_all'
        Schema::table('medical_records', function (Blueprint $table) {
            $table->string('status', 50)->default('waiting')->change();
        });

        // 3. Create medical_record_tindakans pivot table
        Schema::create('medical_record_tindakans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('medical_record_id')->constrained('medical_records')->onDelete('cascade');
            $table->foreignId('master_tindakan_id')->constrained('master_tindakans')->onDelete('restrict');
            $table->integer('qty')->default(1);
            $table->decimal('subtotal', 12, 2);
            $table->timestamps();
        });

        // 4. Create invoices table
        Schema::create('invoices', function (Blueprint $table) {
            $table->id();
            $table->foreignId('medical_record_id')->constrained('medical_records')->onDelete('restrict');
            $table->string('invoice_number')->unique();
            $table->decimal('subtotal', 12, 2);
            $table->decimal('discount', 12, 2)->default(0);
            $table->decimal('grand_total', 12, 2);
            $table->enum('payment_method', ['tunai', 'qris', 'transfer', 'asuransi'])->nullable();
            $table->decimal('amount_tendered', 12, 2)->nullable();
            $table->decimal('change_amount', 12, 2)->nullable();
            $table->enum('status', ['unpaid', 'paid'])->default('unpaid');
            $table->foreignId('cashier_id')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('paid_at')->nullable();
            $table->timestamps();
        });

        // 5. Create invoice_items table
        Schema::create('invoice_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('invoice_id')->constrained('invoices')->onDelete('cascade');
            $table->enum('item_type', ['admin', 'tindakan', 'lab', 'obat']);
            $table->string('description');
            $table->integer('qty');
            $table->decimal('unit_price', 12, 2);
            $table->decimal('subtotal', 12, 2);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('invoice_items');
        Schema::dropIfExists('invoices');
        Schema::dropIfExists('medical_record_tindakans');

        // Note: doctrine/dbal or native change of column type might have issues in some setups when reverting string back to enum.
        // We will just recreate the status column as enum.
        Schema::table('medical_records', function (Blueprint $table) {
            $table->enum('status', ['waiting', 'anamnesis', 'waiting_doctor', 'examination', 'completed'])->default('waiting')->change();
        });

        Schema::table('master_tindakans', function (Blueprint $table) {
            $table->dropColumn('kategori');
        });
    }
};
