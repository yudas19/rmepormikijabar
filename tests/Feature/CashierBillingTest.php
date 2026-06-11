<?php

use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\LabOrder;
use App\Models\MasterObat;
use App\Models\MasterTindakan;
use App\Models\MedicalRecord;
use App\Models\Pasien;
use App\Models\Pendaftaran;
use App\Models\Poli;
use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Support\Facades\DB;
use Livewire\Livewire;

beforeEach(function () {
    $this->seed(DatabaseSeeder::class);
});

test('guests are redirected to the login page for cashier billing page', function () {
    $this->get(route('kasir.index'))
        ->assertRedirect(route('login'));
});

test('authenticated cashiers/users can visit the cashier billing page', function () {
    $user = User::first();
    $this->actingAs($user);

    $this->get(route('kasir.index'))
        ->assertOk();
});

test('cashier payment submission validates input requirements', function () {
    $user = User::first();
    $this->actingAs($user);

    // Create a mock completed medical record
    $pasien = Pasien::create([
        'no_rekam_medis' => 'RM-20261102-9901',
        'nama_pasien' => 'Billing Patient',
        'nik' => '1234567890123499',
        'tempat_lahir' => 'Bandung',
        'tanggal_lahir' => '1990-01-01',
        'jenis_kelamin' => 'L',
        'golongan_darah' => 'O',
        'alamat' => 'Jl. Billing 12',
        'status_pasien' => 'aktif',
    ]);

    $record = MedicalRecord::create([
        'encounter_id' => 'ENC-9901',
        'patient_id' => $pasien->id,
        'poli_id' => Poli::first()->id,
        'status' => 'completed',
        'nomor_antrean' => 'K-01',
    ]);

    Livewire::test('layanan.kasir')
        ->call('selectRecord', $record->id)
        ->set('paymentMethod', '')
        ->call('submitPayment')
        ->assertHasErrors([
            'paymentMethod' => 'required',
        ])
        ->set('paymentMethod', 'tunai')
        ->set('amountTendered', 1000) // Less than default admin fee (15000)
        ->call('submitPayment')
        ->assertHasErrors([
            'amountTendered' => 'min',
        ]);
});

test('billing aggregation engine correctly sums and processes invoice', function () {
    $user = User::first();
    $this->actingAs($user);

    // 1. Create Patient & Pendaftaran & MedicalRecord
    $pasien = Pasien::create([
        'no_rekam_medis' => 'RM-20261102-9902',
        'nama_pasien' => 'Rich Patient',
        'nik' => '1234567890123498',
        'tempat_lahir' => 'Jakarta',
        'tanggal_lahir' => '1995-05-15',
        'jenis_kelamin' => 'P',
        'golongan_darah' => 'A',
        'alamat' => 'Jl. Kemang Raya',
        'status_pasien' => 'aktif',
    ]);

    $pendaftaran = Pendaftaran::create([
        'no_registrasi' => 'REG-9902',
        'pasien_id' => $pasien->id,
        'poli_id' => Poli::first()->id,
        'dokter_id' => 1,
        'no_antrean' => 'A-99',
        'angka_antrean' => 99,
        'status_antrean' => 'selesai',
        'cara_bayar' => 'Umum',
        'jenis_kunjungan' => 'Baru',
    ]);

    $record = MedicalRecord::create([
        'encounter_id' => 'ENC-9902',
        'patient_id' => $pasien->id,
        'pendaftaran_id' => $pendaftaran->id,
        'poli_id' => Poli::first()->id,
        'status' => 'completed',
        'nomor_antrean' => 'A-99',
    ]);

    // 2. Add Polyclinic Procedure
    $tindakan = MasterTindakan::where('nama_tindakan', 'Konsultasi Dokter Umum')->first();
    $record->tindakans()->attach($tindakan->id, [
        'qty' => 1,
        'subtotal' => $tindakan->tarif,
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    // 3. Add completed Lab Order with tariff_snapshot
    $labOrder = LabOrder::create([
        'medical_record_id' => $record->id,
        'requested_by_id' => 1,
        'status' => 'completed',
        'total_tariff' => 125000,
    ]);

    DB::table('lab_order_results')->insert([
        'lab_order_id' => $labOrder->id,
        'master_lab_test_id' => 1,
        'test_name_snapshot' => 'Darah Lengkap',
        'normal_range_snapshot' => '12-16',
        'unit_snapshot' => 'g/dl',
        'tariff_snapshot' => 125000,
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    // 4. Add dispensed Prescription item with subtotal
    $prescription = DB::table('medical_record_prescriptions')->insertGetId([
        'medical_record_id' => $record->id,
        'type' => 'non-racikan',
        'aturan_pakai' => '3x1',
        'dispensing_status' => 'dispensed',
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    $obat = MasterObat::first();
    DB::table('medical_record_prescription_items')->insert([
        'prescription_id' => $prescription,
        'requested_obat_id' => $obat->id,
        'requested_qty' => 10,
        'satuan' => $obat->satuan,
        'dispensed_obat_id' => $obat->id,
        'dispensed_qty' => 10,
        'subtotal_price' => 10 * $obat->harga_jual,
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    // Check total calculation
    // Admin: 15,000
    // Tindakan: Konsultasi Dokter Umum (30,000)
    // Lab: 125,000
    // Obat: 10 * obat price
    $expectedSubtotal = 15000 + $tindakan->tarif + 125000 + (10 * $obat->harga_jual);

    Livewire::test('layanan.kasir')
        ->call('selectRecord', $record->id)
        ->assertSet('adminFee', 15000)
        ->assertSet('subtotal', (float) $expectedSubtotal)
        ->set('discount', 10000)
        ->assertSet('grandTotal', (float) ($expectedSubtotal - 10000))
        ->set('paymentMethod', 'tunai')
        ->set('amountTendered', $expectedSubtotal)
        ->call('submitPayment')
        ->assertHasNoErrors()
        ->assertSet('activeView', 'queue');

    // Assert Invoice creation
    $invoice = Invoice::where('medical_record_id', $record->id)->first();
    expect($invoice)->not->toBeNull();
    expect((float) $invoice->subtotal)->toBe((float) $expectedSubtotal);
    expect((float) $invoice->discount)->toBe(10000.00);
    expect((float) $invoice->grand_total)->toBe((float) ($expectedSubtotal - 10000));
    expect($invoice->status)->toBe('paid');

    // Assert Invoice Items
    $items = InvoiceItem::where('invoice_id', $invoice->id)->get();
    expect($items->count())->toBe(4); // admin, tindakan, lab, obat

    // Assert medical record status update to completed_all
    $record->refresh();
    expect($record->status)->toBe('completed_all');

    // Assert pendaftaran status_antrean update to selesai
    $pendaftaran->refresh();
    expect($pendaftaran->status_antrean)->toBe('selesai');
});
