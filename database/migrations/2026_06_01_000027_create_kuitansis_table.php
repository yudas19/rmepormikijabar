<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('kuitansis', function (Blueprint $table) {
            $table->id();
            $table->string('no_kuitansi', 30)->unique(); // Contoh: KW-20260526-0001
            $table->foreignId('pendaftaran_id')->constrained('pendaftarans')->onDelete('restrict');
            $table->foreignId('kasir_id')->nullable()->constrained('master_petugass')->onDelete('set null');

            // Komponen Rekap Biaya (Untuk mempermudah pelaporan tanpa re-calculate berat)
            $table->decimal('biaya_pendaftaran', 12, 2)->default(0);
            $table->decimal('biaya_tindakan', 12, 2)->default(0);
            $table->decimal('biaya_obat', 12, 2)->default(0);
            $table->decimal('biaya_lab', 12, 2)->default(0);

            // Total & Pembayaran
            $table->decimal('total_tagihan', 12, 2);
            $table->decimal('nominal_bayar', 12, 2);
            $table->decimal('kembalian', 12, 2)->default(0);

            // Metode Pembayaran
            $table->enum('metode_pembayaran', ['Tunai', 'Transfer', 'Debit', 'Jaminan_BPJS', 'Jaminan_Dinas']);
            $table->enum('status_pembayaran', ['belum_bayar', 'lunas'])->default('belum_bayar');

            $table->text('catatan_kasir')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('kuitansis');
    }
};
