<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('stock_movements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('master_obat_id')->constrained('master_obats')->onDelete('cascade');
            $table->enum('type', ['in', 'out', 'opname_adjustment'])->comment('in=restock, out=dispensing, opname_adjustment=stock opname');
            $table->integer('quantity')->comment('Positive for in/adjustment up, negative for out/adjustment down');
            $table->integer('previous_stock')->comment('Stock sebelum perubahan');
            $table->integer('current_stock')->comment('Stock setelah perubahan');
            $table->string('notes')->nullable()->comment('Keterangan, e.g. "Dispensing resep #123" or "Opname fisik"');
            $table->date('opname_date')->nullable()->comment('Tanggal opname jika type=opname_adjustment');
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete()->comment('Petugas yang melakukan');
            $table->foreignId('prescription_id')->nullable()->constrained('medical_record_prescriptions')->nullOnDelete()->comment('Link ke resep jika type=out');
            $table->timestamps();

            $table->index(['master_obat_id', 'type']);
            $table->index('created_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('stock_movements');
    }
};
