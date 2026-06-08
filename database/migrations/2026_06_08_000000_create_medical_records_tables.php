<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. medical_records table
        Schema::create('medical_records', function (Blueprint $table) {
            $table->id();
            $table->string('encounter_id')->unique();
            $table->foreignId('patient_id')->constrained('pasiens')->onDelete('cascade');
            $table->foreignId('pendaftaran_id')->nullable()->constrained('pendaftarans')->onDelete('set null');
            $table->foreignId('poli_id')->constrained('master_polis')->onDelete('restrict');
            $table->enum('status', ['waiting', 'anamnesis', 'waiting_doctor', 'examination', 'completed'])->default('waiting');
            $table->string('nomor_antrean');

            // Vital Signs (TTV)
            $table->integer('tensi_sistole')->nullable();
            $table->integer('tensi_diastole')->nullable();
            $table->integer('pulse_rate')->nullable();
            $table->integer('respiratory_rate')->nullable();
            $table->decimal('temperature', 4, 1)->nullable();
            $table->decimal('weight', 5, 2)->nullable();
            $table->integer('height')->nullable();
            $table->decimal('bmi', 4, 1)->nullable();

            // General Condition & Consciousness/GCS
            $table->string('keadaan_umum')->nullable(); // Good, Moderate, Weak
            $table->string('kesadaran_gcs')->nullable(); // Compos Mentis, Apatis, Somnolen, Sopor, Coma
            $table->integer('gcs_eye')->nullable();
            $table->integer('gcs_verbal')->nullable();
            $table->integer('gcs_motor')->nullable();
            $table->integer('gcs_score')->nullable();

            // SOAPE
            $table->text('subjective')->nullable();
            $table->text('objective')->nullable();
            $table->text('assessment')->nullable();
            $table->text('plan')->nullable();
            $table->text('evaluation')->nullable();

            // Audit
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->foreign('created_by')->references('id')->on('users')->onDelete('set null');
            $table->foreign('updated_by')->references('id')->on('users')->onDelete('set null');
            $table->timestamps();

            // Indexing
            $table->index('poli_id');
            $table->index('status');
            $table->index('encounter_id');
            $table->index('created_at');
        });

        // 2. medical_record_icd10 (Diagnoses)
        Schema::create('medical_record_icd10', function (Blueprint $table) {
            $table->id();
            $table->foreignId('medical_record_id')->constrained('medical_records')->onDelete('cascade');
            $table->foreignId('master_icd10_id')->constrained('master_icd10s')->onDelete('cascade');
            $table->string('icd10_code');
            $table->string('icd10_name');
            $table->boolean('is_primary')->default(false);
            $table->timestamps();
        });

        // 3. medical_record_icd9 (Procedures)
        Schema::create('medical_record_icd9', function (Blueprint $table) {
            $table->id();
            $table->foreignId('medical_record_id')->constrained('medical_records')->onDelete('cascade');
            $table->foreignId('master_icd9_id')->constrained('master_icd9s')->onDelete('cascade');
            $table->string('icd9_code');
            $table->string('icd9_name');
            $table->timestamps();
        });

        // 4. medical_record_prescriptions
        Schema::create('medical_record_prescriptions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('medical_record_id')->constrained('medical_records')->onDelete('cascade');
            $table->enum('type', ['non-racikan', 'racikan']);
            $table->string('nama_racikan')->nullable();
            $table->foreignId('metode_racik_id')->nullable()->constrained('master_metode_raciks')->onDelete('set null');
            $table->integer('jumlah_kemasan')->nullable();
            $table->string('aturan_pakai');
            $table->string('catatan')->nullable();
            $table->timestamps();
        });

        // 5. medical_record_prescription_items
        Schema::create('medical_record_prescription_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('prescription_id')->constrained('medical_record_prescriptions')->onDelete('cascade');
            $table->foreignId('master_obat_id')->constrained('master_obats')->onDelete('restrict');
            $table->decimal('jumlah', 8, 2);
            $table->string('satuan')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('medical_record_prescription_items');
        Schema::dropIfExists('medical_record_prescriptions');
        Schema::dropIfExists('medical_record_icd9');
        Schema::dropIfExists('medical_record_icd10');
        Schema::dropIfExists('medical_records');
    }
};
