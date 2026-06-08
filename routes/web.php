<?php

use App\Http\Controllers\MedicalRecordController;
use App\Http\Controllers\PrintController;
use Illuminate\Support\Facades\Route;

Route::view('/', 'welcome')->name('home');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::view('dashboard', 'dashboard')->name('dashboard');
    Route::view('/master', 'master.index')->name('master.index');

    Route::livewire('/master/petugas', 'master.petugas')->name('master.petugas');
    Route::livewire('/master/obat', 'master.obat')->name('master.obat');
    Route::livewire('/master/poli', 'master.poli')->name('master.poli');
    Route::livewire('/master/laboratorium', 'master.laboratorium')->name('master.laboratorium');
    Route::livewire('/master/pcarebpjs', 'master.pcarebpjs')->name('master.pcarebpjs');
    Route::livewire('/master/cara-pakai-obat', 'master.cara-pakai-obat')->name('master.cara-pakai-obat');
    Route::livewire('/master/pekerjaan', 'master.pekerjaan')->name('master.pekerjaan');
    Route::livewire('/master/pendidikan', 'master.pendidikan')->name('master.pendidikan');
    Route::livewire('/master/satusehat', 'master.satusehat')->name('master.satusehat');
    Route::livewire('/master/tindakan', 'master.tindakan')->name('master.tindakan');
    Route::livewire('/master/provinsi', 'master.provinsi')->name('master.provinsi');
    Route::livewire('/master/kabupaten-kota', 'master.kabupaten-kota')->name('master.kabupaten-kota');
});

Route::livewire('/pendaftaran', '⚡pendaftaran')->name('pendaftaran.index');

Route::get('/print/consent/{id}', [PrintController::class, 'printConsent'])->name('print.consent');
Route::get('/print/referral/{id}', [PrintController::class, 'printReferral'])->name('print.referral');
Route::get('/print/certificate/{id}', [PrintController::class, 'printCertificate'])->name('print.certificate');
Route::get('/print/queue-ticket/{id}', [PrintController::class, 'printQueueTicket'])->name('print.queue-ticket');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/poli/{poliklinik}/examine/{encounter_id}', [MedicalRecordController::class, 'examine'])->name('medical-record.examine');
    Route::livewire('/poli/{poliklinik}', 'poliklinik-queue')->name('poli.queue');
    Route::livewire('/layanan/laboratorium', 'layanan.laboratorium')->name('layanan.laboratorium');
    Route::livewire('/layanan/farmasi', 'layanan.farmasi')->name('layanan.farmasi');
});

require __DIR__.'/settings.php';
