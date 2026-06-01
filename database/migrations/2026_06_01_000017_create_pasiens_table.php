<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{

public function up(): void
    {
        Schema::create('pasiens', function (Blueprint $table) {
            $table->id();
            $table->string('no_rekam_medis', 20)->unique();
            $table->string('nama_pasien', 100);
            $table->string('panggilan')->nullable();

            // Identitas Resmi & Bridging
            $table->string('nik', 16)->unique();
            $table->string('no_bpjs', 13)->unique()->nullable();
            $table->string('ihs_number')->unique()->nullable();

            // Biodata Dasar
            $table->string('gelar')->nullable();
            $table->string('tempat_lahir')->constrained('kabupaten_kotas')->onDelete('set null');
            $table->date('tanggal_lahir');
            $table->enum('jenis_kelamin', ['L', 'P']);
            $table->enum('golongan_darah', ['A', 'B', 'AB', 'O', 'Tidak Tahu'])->default('Tidak Tahu');

            // Kebutuhan Institusi (TNI/Polri/Dinas)
            $table->string('nama_orangtua')->nullable();
            $table->string('nrp', 30)->nullable();
            $table->enum('keluarga_anggota', ['ya', 'tidak'])->default('tidak');
            $table->string('hubungan_keluarga')->nullable();

            // Data Sosial Pasien (SatuSehat Resource & Akreditasi)
            $table->string('status_perkawinan', 30)->nullable();
            $table->string('suku', 50)->nullable();
            $table->string('agama', 20)->nullable();
            $table->string('pendidikan', 30)->nullable();
            $table->string('pekerjaan', 50)->nullable();
            $table->string('kewarganegaraan', 3)->default('WNI');
            $table->string('bahasa', 50)->default('Indonesia');
            
            // Kontak (Gunakan no_whatsapp agar seragam dengan rencana awal)

            $table->string('no_whatsapp', 15)->nullable();
            $table->string('email')->nullable();
            
            // Alamat Lengkap
            $table->text('alamat');
            $table->foreignId('provinsi_id')->nullable()->constrained('provinsis')->onDelete('set null');
            $table->foreignId('kabupaten_kota_id')->nullable()->constrained('kabupaten_kotas')->onDelete('set null');
            $table->string('kode_pos', 10)->nullable();
            
            // Status Akun / Rekam Medis
            $table->enum('status_pasien', ['aktif', 'nonaktif', 'meninggal'])->default('aktif');
            
            // Keamanan & Tracking User
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->foreign('created_by')->references('id')->on('users')->onDelete('set null');
            $table->foreign('updated_by')->references('id')->on('users')->onDelete('set null');
            
            $table->timestamps();
            $table->softDeletes();
            
            // Indexing untuk kecepatan pencarian data puluhan ribu pasien
            $table->index('nama_pasien');
            $table->index('nik');
            $table->index('no_bpjs');
            $table->index('tanggal_lahir');
            $table->index('no_whatsapp');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pasiens');
    }
};
