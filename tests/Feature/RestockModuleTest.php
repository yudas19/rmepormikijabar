<?php

use App\Models\MasterObat;
use App\Models\StockMovement;
use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Livewire\Livewire;

beforeEach(function () {
    $this->seed(DatabaseSeeder::class);
});

test('guests are redirected to the login page for farmasi stock management', function () {
    $this->get(route('farmasi.stok'))
        ->assertRedirect(route('login'));
});

test('authenticated users can visit the farmasi stock page', function () {
    $user = User::first();
    $this->actingAs($user);

    $this->get(route('farmasi.stok'))
        ->assertOk();
});

test('restock form validates input requirements', function () {
    $user = User::first();
    $this->actingAs($user);

    Livewire::test('layanan.farmasi-stok')
        ->set('activeTab', 'restock')
        ->call('submitRestock')
        ->assertHasErrors([
            'restockObatId' => 'required',
            'restockQuantity' => 'required',
            'restockExpiryDate' => 'required',
        ])
        ->set('restockObatId', 99999) // non-existent
        ->set('restockQuantity', 0)   // quantity must be > 0
        ->set('restockExpiryDate', 'invalid-date')
        ->call('submitRestock')
        ->assertHasErrors([
            'restockObatId' => 'exists',
            'restockQuantity' => 'min',
            'restockExpiryDate' => 'date',
        ]);
});

test('restock drug autocomplete search works as expected', function () {
    $user = User::first();
    $this->actingAs($user);

    Livewire::test('layanan.farmasi-stok')
        ->set('restockObatQuery', 'Para')
        ->assertSet('restockObatResults', function ($results) {
            return count($results) > 0 && $results[0]['nama_obat'] === 'Paracetamol 500 mg';
        });
});

test('pharmacists can successfully restock a medicine', function () {
    $user = User::first();
    $this->actingAs($user);

    $obat = MasterObat::where('kode_obat', 'OBT-001')->first();
    $initialStock = $obat->stok_saat_ini;
    $newExpiry = '2028-12-31';
    $restockQty = 150;
    $notes = 'Restock dari PBF Biofarma';

    Livewire::test('layanan.farmasi-stok')
        ->set('activeTab', 'restock')
        ->call('openRestockModal')
        ->set('restockObatId', $obat->id)
        ->set('restockQuantity', $restockQty)
        ->set('restockExpiryDate', $newExpiry)
        ->set('restockNotes', $notes)
        ->call('submitRestock')
        ->assertHasNoErrors()
        ->assertSet('showRestockModal', false);

    // Assert database updates on MasterObat
    $obat->refresh();
    expect($obat->stok_saat_ini)->toBe($initialStock + $restockQty);
    expect($obat->tanggal_kadaluarsa->toDateString())->toBe($newExpiry);

    // Assert StockMovement audit trail record
    $movement = StockMovement::where('master_obat_id', $obat->id)
        ->where('type', 'in')
        ->latest()
        ->first();

    expect($movement)->not->toBeNull();
    expect($movement->quantity)->toBe($restockQty);
    expect($movement->previous_stock)->toBe($initialStock);
    expect($movement->current_stock)->toBe($initialStock + $restockQty);
    expect($movement->notes)->toBe($notes);
    expect($movement->user_id)->toBe($user->id);
});
