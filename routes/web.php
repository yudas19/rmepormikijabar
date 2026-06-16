<?php

use App\Http\Controllers\MedicalLetterController;
use App\Http\Controllers\MedicalRecordController;
use App\Http\Controllers\PrintController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\SatuSehatDashboardController;
use App\Models\Pasien;
use App\Models\Pendaftaran;
use App\Models\Poli;
use Illuminate\Support\Facades\Route;

Route::view('/', 'welcome')->name('home');

Route::livewire('/display-antrean', '⚡display-antrean')->name('display-antrean');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/dashboard', function () {
        $todayCount = Pendaftaran::whereDate('created_at', today())
            ->where('status_antrean', '!=', 'batal')
            ->count();

        $totalPatients = Pasien::count();

        $poliStats = Poli::withCount(['pendaftaran' => function ($query) {
            $query->whereDate('created_at', today())
                ->where('status_antrean', '!=', 'batal');
        }])->get();

        $jobStats = Pasien::selectRaw('COALESCE(NULLIF(pekerjaan, ""), "Tidak Mengisi") as pekerjaan, count(*) as count')
            ->groupBy('pekerjaan')
            ->orderByDesc('count')
            ->take(8)
            ->get();

        return view('dashboard', compact('todayCount', 'totalPatients', 'poliStats', 'jobStats'));
    })->name('dashboard');
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
    Route::livewire('/master/faskes-profile', 'master.faskes-profile')->name('master.faskes-profile');
    Route::livewire('/master/jadwal-dokter', 'master.jadwal-dokter')->name('master.jadwal-dokter');

    // Access Control Management Route
    Route::livewire('/admin/hak-akses', 'admin.hak-akses')
        ->middleware('permission:akses_pengaturan_akses')
        ->name('admin.hak-akses');

    // SatuSehat Bridging Dashboard Routes
    Route::get('/admin/satusehat-dashboard', [SatuSehatDashboardController::class, 'index'])
        ->name('admin.satusehat-dashboard');
    Route::post('/admin/satusehat-dashboard/{record}/dispatch', [SatuSehatDashboardController::class, 'dispatchRecord'])
        ->name('admin.satusehat-dashboard.dispatch');
    Route::post('/admin/satusehat-dashboard/dispatch-all-ready', [SatuSehatDashboardController::class, 'dispatchAllReady'])
        ->name('admin.satusehat-dashboard.dispatch-all-ready');

    // Medical Letters Routes
    Route::livewire('/admin/daftar-surat', 'admin.daftar-surat')->name('admin.daftar-surat');
    Route::post('/medical-letters', [MedicalLetterController::class, 'store'])->name('medical-letters.store');
    Route::get('/medical-letters/{id}/print', [MedicalLetterController::class, 'print'])->name('medical-letters.print');

    // Executive Report Route
    Route::get('/admin/laporan-eksekutif', [ReportController::class, 'index'])
        ->middleware('permission:akses_pengaturan_akses')
        ->name('admin.laporan-eksekutif');
});

// Pendaftaran (Requires auth, verified, and akses_pendaftaran)
Route::livewire('/pendaftaran', '⚡pendaftaran')
    ->middleware(['auth', 'verified', 'permission:akses_pendaftaran'])
    ->name('pendaftaran.index');

Route::get('/print/consent/{id}', [PrintController::class, 'printConsent'])->name('print.consent');
Route::get('/print/referral/{id}', [PrintController::class, 'printReferral'])->name('print.referral');
Route::get('/print/certificate/{id}', [PrintController::class, 'printCertificate'])->name('print.certificate');
Route::get('/print/queue-ticket/{id}', [PrintController::class, 'printQueueTicket'])->name('print.queue-ticket');

Route::middleware(['auth', 'verified'])->group(function () {
    // Poliklinik (dynamic permissions checked inside component mount)
    Route::livewire('/poli/{poliklinik}', 'poliklinik-queue')->name('poli.queue');

    // Rekam Medis (Examine workspace)
    Route::get('/poli/{poliklinik}/examine/{encounter_id}', [MedicalRecordController::class, 'examine'])
        ->middleware('permission:akses_rekam_medis')
        ->name('medical-record.examine');

    // Laboratorium
    Route::middleware('permission:akses_laboratorium')->group(function () {
        Route::livewire('/layanan/laboratorium', 'layanan.laboratorium')->name('layanan.laboratorium');
        Route::livewire('/layanan/laboratorium/{labOrder}/hasil', 'layanan.lab-hasil')->name('lab.hasil');
    });

    // Farmasi
    Route::middleware('permission:akses_farmasi')->group(function () {
        Route::livewire('/layanan/farmasi', 'layanan.farmasi')->name('layanan.farmasi');
        Route::livewire('/layanan/farmasi/dispensing/{prescription}', 'layanan.farmasi-dispensing')->name('farmasi.dispensing');
    });

    // Stock Opname / Farmasi Stok
    Route::livewire('/layanan/farmasi/stok', 'layanan.farmasi-stok')
        ->middleware('permission:akses_stock_opname')
        ->name('farmasi.stok');

    // Kasir
    Route::livewire('/layanan/kasir', 'layanan.kasir')
        ->middleware('permission:akses_kasir')
        ->name('kasir.index');
});

require __DIR__.'/settings.php';
