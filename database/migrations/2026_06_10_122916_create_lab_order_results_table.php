<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('lab_order_results', function (Blueprint $table) {
            $table->id();
            $table->foreignId('lab_order_id')->constrained('lab_orders')->onDelete('cascade');
            $table->foreignId('master_lab_test_id')->constrained('master_lab_tests')->onDelete('restrict');

            // Snapshot columns — copied from master at time of ordering for audit trail
            $table->string('test_name_snapshot')->comment('Snapshot nama tes saat dipesan');
            $table->string('normal_range_snapshot', 100)->nullable()->comment('Snapshot nilai rujukan saat dipesan');
            $table->string('unit_snapshot', 30)->nullable()->comment('Snapshot satuan saat dipesan');
            $table->unsignedBigInteger('tariff_snapshot')->default(0)->comment('Snapshot tarif saat dipesan');

            // Result — filled by analyst
            $table->string('result_value', 100)->nullable()->comment('Nilai hasil yang diinput analis');
            $table->boolean('is_abnormal')->default(false)->comment('Apakah nilai di luar rentang normal');
            $table->foreignId('analis_id')->nullable()->constrained('master_petugass')->nullOnDelete()->comment('Petugas analis yang mengerjakan');

            $table->timestamps();

            $table->index('lab_order_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('lab_order_results');
    }
};
