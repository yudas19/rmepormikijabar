<?php

use App\Models\MasterObat;
use App\Models\MasterPetugas;
use App\Models\MedicalRecord;
use App\Models\MedicalRecordPrescription;
use App\Models\MedicalRecordPrescriptionItem;
use App\Models\Pasien;
use App\Models\Pendaftaran;
use App\Models\Poli;
use App\Models\StockMovement;
use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Livewire\Livewire;

beforeEach(function () {
    $this->seed(DatabaseSeeder::class);
});

test('multiple prescriptions under same medical record show as a single grouped queue row', function () {
    $user = User::first();
    $this->actingAs($user);

    $pasien = Pasien::create([
        'no_rekam_medis' => 'RM-PHARM-0001',
        'nama_pasien' => 'Grouped Rx Patient',
        'nik' => '9902123456789012',
        'tempat_lahir' => 'Jakarta',
        'tanggal_lahir' => '1995-01-01',
        'jenis_kelamin' => 'L',
        'alamat' => 'Alamat test',
        'status_pasien' => 'aktif',
    ]);

    $poli = Poli::where('kode_poli', 'UMU')->first();

    $dokter = MasterPetugas::where('jenis_petugas', 'Dokter')->first();

    $pendaftaran = Pendaftaran::create([
        'no_registrasi' => 'REG-PHARM-0001',
        'pasien_id' => $pasien->id,
        'poli_id' => $poli->id,
        'dokter_id' => $dokter->id,
        'no_antrean' => 'A-10',
        'angka_antrean' => 10,
        'status_antrean' => 'pemeriksaan ttv',
        'cara_bayar' => 'Umum',
        'jenis_kunjungan' => 'Baru',
    ]);

    $mr = MedicalRecord::create([
        'encounter_id' => 'ENC-PHARM-0001',
        'patient_id' => $pasien->id,
        'pendaftaran_id' => $pendaftaran->id,
        'poli_id' => $poli->id,
        'status' => 'waiting',
        'nomor_antrean' => 'A-10',
        'tanggal_kunjungan' => date('Y-m-d'),
    ]);

    $obat1 = MasterObat::first();
    $obat2 = MasterObat::skip(1)->first();

    $presc1 = MedicalRecordPrescription::create([
        'medical_record_id' => $mr->id,
        'type' => 'non-racikan',
        'aturan_pakai' => '3x1',
        'dispensing_status' => 'waiting',
    ]);

    MedicalRecordPrescriptionItem::create([
        'prescription_id' => $presc1->id,
        'requested_obat_id' => $obat1->id,
        'requested_qty' => 10,
        'satuan' => $obat1->satuan,
    ]);

    $presc2 = MedicalRecordPrescription::create([
        'medical_record_id' => $mr->id,
        'type' => 'non-racikan',
        'aturan_pakai' => '2x1',
        'dispensing_status' => 'waiting',
    ]);

    MedicalRecordPrescriptionItem::create([
        'prescription_id' => $presc2->id,
        'requested_obat_id' => $obat2->id,
        'requested_qty' => 5,
        'satuan' => $obat2->satuan,
    ]);

    Livewire::test('layanan.farmasi')
        ->assertViewHas('prescriptions', fn ($prescs) => count($prescs->items()) === 1)
        ->assertSee('Grouped Rx Patient')
        ->assertSee($obat1->nama_obat)
        ->assertSee($obat2->nama_obat);
});

test('bulk dispensing dispensingRows load correctly and finalize reduces stock in transaction', function () {
    $user = User::first();
    $this->actingAs($user);

    $pasien = Pasien::create([
        'no_rekam_medis' => 'RM-PHARM-0002',
        'nama_pasien' => 'Bulk Dispense Patient',
        'nik' => '9902123456789013',
        'tempat_lahir' => 'Jakarta',
        'tanggal_lahir' => '1995-01-01',
        'jenis_kelamin' => 'P',
        'alamat' => 'Alamat test',
        'status_pasien' => 'aktif',
    ]);

    $poli = Poli::where('kode_poli', 'UMU')->first();

    $dokter = MasterPetugas::where('jenis_petugas', 'Dokter')->first();

    $pendaftaran = Pendaftaran::create([
        'no_registrasi' => 'REG-PHARM-0002',
        'pasien_id' => $pasien->id,
        'poli_id' => $poli->id,
        'dokter_id' => $dokter->id,
        'no_antrean' => 'A-11',
        'angka_antrean' => 11,
        'status_antrean' => 'pemeriksaan ttv',
        'cara_bayar' => 'Umum',
        'jenis_kunjungan' => 'Baru',
    ]);

    $mr = MedicalRecord::create([
        'encounter_id' => 'ENC-PHARM-0002',
        'patient_id' => $pasien->id,
        'pendaftaran_id' => $pendaftaran->id,
        'poli_id' => $poli->id,
        'status' => 'waiting',
        'nomor_antrean' => 'A-11',
        'tanggal_kunjungan' => date('Y-m-d'),
    ]);

    $obat1 = MasterObat::first();
    $obat2 = MasterObat::skip(1)->first();

    $initialStock1 = $obat1->stok_saat_ini;
    $initialStock2 = $obat2->stok_saat_ini;

    $presc1 = MedicalRecordPrescription::create([
        'medical_record_id' => $mr->id,
        'type' => 'non-racikan',
        'aturan_pakai' => '3x1',
        'dispensing_status' => 'waiting',
    ]);

    MedicalRecordPrescriptionItem::create([
        'prescription_id' => $presc1->id,
        'requested_obat_id' => $obat1->id,
        'requested_qty' => 10,
        'satuan' => $obat1->satuan,
    ]);

    $presc2 = MedicalRecordPrescription::create([
        'medical_record_id' => $mr->id,
        'type' => 'non-racikan',
        'aturan_pakai' => '2x1',
        'dispensing_status' => 'waiting',
    ]);

    MedicalRecordPrescriptionItem::create([
        'prescription_id' => $presc2->id,
        'requested_obat_id' => $obat2->id,
        'requested_qty' => 5,
        'satuan' => $obat2->satuan,
    ]);

    Livewire::test('layanan.farmasi-dispensing', ['prescription' => $presc1])
        ->assertSet('dispensingRows', fn ($rows) => count($rows) === 2)
        ->call('finalize')
        ->assertHasNoErrors();

    expect($obat1->fresh()->stok_saat_ini)->toBe($initialStock1 - 10);
    expect($obat2->fresh()->stok_saat_ini)->toBe($initialStock2 - 5);

    expect($presc1->fresh()->dispensing_status)->toBe('dispensed');
    expect($presc2->fresh()->dispensing_status)->toBe('dispensed');

    expect(StockMovement::where('prescription_id', $presc1->id)->exists())->toBeTrue();
    expect(StockMovement::where('prescription_id', $presc2->id)->exists())->toBeTrue();
});
