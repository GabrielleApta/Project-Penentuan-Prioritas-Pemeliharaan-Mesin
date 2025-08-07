<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\{
    Auth\LoginController,
    Auth\RegisteredUserController,
    DashboardController,
    DepresiasiController,
    KriteriaController,
    KerusakanTahunanController,
    LaporanController,
    MesinController,
    PenilaianMesinController,
    PrioritasController,
    ProfileController,
    UserController,
    JadwalPemeliharaanController,
    HistoryPemeliharaanController,
    RiwayatStraightLineController,
    RiwayatSawController,
};

// 🔒 Redirect root ke login
Route::get('/', fn() => redirect()->route('login'));

// 🔐 Autentikasi Routes
Route::controller(LoginController::class)->group(function () {
    Route::get('/login', 'showLoginForm')->name('login');
    Route::post('/login', 'login');
    Route::post('/logout', 'logout')->name('logout');
});

Route::controller(RegisteredUserController::class)->group(function () {
    Route::get('/register', 'create')->name('register');
    Route::post('/register', 'store')->name('register.store');
    Route::get('/register-admin', 'createAdmin')->name('register.admin');
    Route::post('/register-admin', 'storeAdmin')->name('register.admin.store');
});

// 🔐 Protected Routes
Route::middleware(['auth', 'verified'])->group(function () {

    // 🏠 Dashboard Routes
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/dashboard/top-depresiasi', [DashboardController::class, 'ajaxTopDepresiasi'])->name('dashboard.top-depresiasi');

    // Dashboard khusus untuk regu mekanik
    Route::middleware('role:regu_mekanik')
        ->get('/regu/dashboard', [DashboardController::class, 'index'])
        ->name('dashboard.index');

    // Dashboard khusus untuk koordinator mekanik
    Route::middleware('role:koordinator_mekanik')
        ->get('/koordinator/dashboard', [DashboardController::class, 'userDashboard'])
        ->name('user.dashboard');

    // 🔧 Mesin Routes
    Route::resource('mesin', MesinController::class)->except(['show']);
    Route::prefix('mesin')->name('mesin.')->group(function () {
        Route::get('/export-excel', [MesinController::class, 'exportExcel'])->name('exportExcel');
        Route::get('/export-pdf', [MesinController::class, 'exportPDF'])->name('mesin_pdf');
        Route::post('/import', [MesinController::class, 'import'])->name('import');
        Route::get('/samjin', [MesinController::class, 'samjin'])->name('samjin');
        Route::get('/twisting', [MesinController::class, 'twisting'])->name('twisting');
        Route::get('/aktif', [MesinController::class, 'Aktif'])->name('aktif');
        Route::get('/tidak-aktif', [MesinController::class, 'TidakAktif'])->name('tidakaktif');
    });

    // 📉 Depresiasi Routes
    Route::prefix('depresiasi')->name('depresiasi.')->group(function () {
        Route::get('/', [DepresiasiController::class, 'index'])->name('index');
        Route::post('/hitung', [DepresiasiController::class, 'hitung'])->name('hitung');
        Route::post('/simpan', [DepresiasiController::class, 'simpanKeRiwayat'])->name('simpan');
        Route::get('/reset', [DepresiasiController::class, 'reset'])->name('reset');
        Route::get('/grafik', [DepresiasiController::class, 'grafik'])->name('grafik');
        Route::get('/export-excel', [DepresiasiController::class, 'exportExcel'])->name('exportExcel');
        Route::get('/export-pdf', [DepresiasiController::class, 'exportPdf'])->name('exportPdf');
        Route::get('/{mesin_id}', [DepresiasiController::class, 'show'])->name('show');
    });

    // 🔥 Prioritas SAW Routes
    Route::prefix('prioritas')->name('prioritas.')->group(function () {
        Route::get('/', [PrioritasController::class, 'index'])->name('index');
        Route::post('/proses', [PrioritasController::class, 'proses'])->name('proses');
        Route::get('/hitung', [PrioritasController::class, 'hitungSAW'])->name('hitung');
        Route::get('/{mesin_id}/detail', [PrioritasController::class, 'detailSAW'])->name('detail');
        Route::get('/export', [PrioritasController::class, 'exportExcel'])->name('export');
        Route::get('/{mesin_id}/export', [PrioritasController::class, 'exportDetailExcel'])->name('exportDetail');
        Route::get('/print-pdf', [PrioritasController::class, 'printPDF'])->name('printPDF');
        Route::get('/{mesin_id}/detail/pdf', [PrioritasController::class, 'detailPDF'])->name('detailPDF');
        Route::get('/grafik/saw', [PrioritasController::class, 'grafikSaw'])->name('grafik.saw');
    });

    // 📊 Kriteria Routes
    Route::resource('kriteria', KriteriaController::class)
        ->parameters(['kriteria' => 'kriteria'])
        ->except(['show']);

    // 📝 Penilaian Mesin Routes
    Route::prefix('penilaian')->name('penilaian.')->group(function () {
        Route::get('/', [PenilaianMesinController::class, 'index'])->name('index');
        Route::get('/create', [PenilaianMesinController::class, 'create'])->name('create');
        Route::post('/store', [PenilaianMesinController::class, 'store'])->name('store');
        Route::put('/{id}', [PenilaianMesinController::class, 'update'])->name('update');
        Route::delete('/{id}', [PenilaianMesinController::class, 'destroy'])->name('destroy');
        Route::post('/generate', [PenilaianMesinController::class, 'generatePenilaian'])->name('generate');
        Route::get('/normalisasi', [PenilaianMesinController::class, 'normalisasi'])->name('normalisasi');
        Route::get('/export-excel', [PenilaianMesinController::class, 'exportExcel'])->name('exportExcel');
    });

    // 📅 Kerusakan Tahunan Routes
    Route::resource('kerusakan-tahunan', KerusakanTahunanController::class)->except(['show']);
    Route::prefix('kerusakan-tahunan')->name('kerusakan-tahunan.')->group(function () {
        Route::get('/rata-rata', [KerusakanTahunanController::class, 'rataRataSkor'])->name('rata-rata');
        Route::get('/export-excel', [KerusakanTahunanController::class, 'exportExcel'])->name('exportExcel');
        Route::get('/export-pdf', [KerusakanTahunanController::class, 'exportPDF'])->name('pdf');
        Route::post('/export-pdf-filter', [KerusakanTahunanController::class, 'exportPdfFiltered'])->name('exportPdfFiltered');
    });

    // Import Kerusakan Tahunan
    Route::prefix('kerusakan')->name('kerusakan.')->group(function () {
        Route::get('/', [KerusakanTahunanController::class, 'index'])->name('index');
    Route::get('/create', [KerusakanTahunanController::class, 'create'])->name('create');
    Route::post('/', [KerusakanTahunanController::class, 'store'])->name('store');
    Route::get('/{id}/edit', [KerusakanTahunanController::class, 'edit'])->name('edit');
    Route::put('/{id}', [KerusakanTahunanController::class, 'update'])->name('update');
    Route::delete('/{id}', [KerusakanTahunanController::class, 'destroy'])->name('destroy');
        Route::get('/import', [KerusakanTahunanController::class, 'showImportForm'])->name('import.form');
        Route::post('/import', [KerusakanTahunanController::class, 'import'])->name('import');
    });

    // 📅 Jadwal Pemeliharaan Routes
    Route::prefix('jadwal')->name('jadwal.')->group(function () {
        Route::get('/', [JadwalPemeliharaanController::class, 'index'])->name('index');
        Route::get('/create', [JadwalPemeliharaanController::class, 'create'])->name('create');
        Route::post('/', [JadwalPemeliharaanController::class, 'store'])->name('store');
        Route::get('/generate/saw', [JadwalPemeliharaanController::class, 'generateDariSAW'])->name('generate.saw');
        Route::patch('/{id}/status', [JadwalPemeliharaanController::class, 'updateStatus'])->name('updateStatus');
        Route::get('/export-pdf', [JadwalPemeliharaanController::class, 'cetakJadwalPDF'])->name('printPDF');
        Route::get('/clean-orphan', [JadwalPemeliharaanController::class, 'cleanOrphanData'])->name('clean.orphan');
    });

    // 📋 History Pemeliharaan Routes
    Route::prefix('history-pemeliharaan')->name('history-pemeliharaan.')->group(function () {
        Route::get('/', [HistoryPemeliharaanController::class, 'index'])->name('index');
        Route::get('/create', [HistoryPemeliharaanController::class, 'create'])->name('create');
        Route::post('/', [HistoryPemeliharaanController::class, 'store'])->name('store');
        Route::get('/{id}/edit', [HistoryPemeliharaanController::class, 'edit'])->name('edit');
        Route::put('/{id}', [HistoryPemeliharaanController::class, 'update'])->name('update');
        Route::delete('/{id}', [HistoryPemeliharaanController::class, 'destroy'])->name('destroy');
        Route::post('/import', [HistoryPemeliharaanController::class, 'importExcel'])->name('import');


        // Export & Import
        Route::post('/export-pdf-filtered', [HistoryPemeliharaanController::class, 'exportPdfFiltered'])->name('exportPdfFiltered');
        Route::post('/import', [HistoryPemeliharaanController::class, 'importExcel'])->name('import');

        // Utilities
        Route::get('/clean-orphan', [HistoryPemeliharaanController::class, 'cleanOrphanData'])->name('clean.orphan');
    });

    // 📊 Riwayat Straight Line Routes
    Route::prefix('riwayat/straight-line')->name('riwayat.straight-line.')->group(function () {
        Route::get('/', [RiwayatStraightLineController::class, 'index'])->name('index');
        Route::post('/simpan', [RiwayatStraightLineController::class, 'simpan'])->name('simpan');
        Route::get('/detail/{kode}', [RiwayatStraightLineController::class, 'detail'])->name('detail');
        Route::delete('/destroy/{kode}', [RiwayatStraightLineController::class, 'destroy'])->name('destroy');
    });

    // 📊 Riwayat SAW Routes
    Route::prefix('riwayat-saw')->name('riwayat-saw.')->group(function () {
        Route::get('/', [RiwayatSawController::class, 'index'])->name('index');
        Route::post('/simpan', [RiwayatSawController::class, 'store'])->name('store');
        Route::get('/detail/{kode}', [RiwayatSawController::class, 'show'])->name('show');
        Route::delete('/{kode}', [RiwayatSawController::class, 'destroy'])->name('destroy');
    });

    // 📑 Laporan Routes
    Route::prefix('laporan')->name('laporan.')->group(function () {
        Route::get('/', [LaporanController::class, 'index'])->name('index');
        Route::get('/generate', [LaporanController::class, 'generate'])->name('generate');
    });

    // 👤 Profil Routes
    Route::prefix('profile')->name('profile.')->group(function () {
        Route::get('/', [ProfileController::class, 'edit'])->name('edit');
        Route::patch('/', [ProfileController::class, 'update'])->name('update');
        Route::delete('/', [ProfileController::class, 'destroy'])->name('destroy');
    });

    // 👥 Manajemen User Routes (hanya untuk regu mekanik)
    Route::middleware('role:regu_mekanik')
        ->prefix('users')
        ->name('users.')
        ->group(function () {
            Route::get('/', [UserController::class, 'index'])->name('index');
            Route::get('/create', [UserController::class, 'create'])->name('create');
            Route::post('/', [UserController::class, 'store'])->name('store');
            Route::get('/{user}/edit', [UserController::class, 'edit'])->name('edit');
            Route::patch('/{user}', [UserController::class, 'update'])->name('update');
            Route::delete('/{user}', [UserController::class, 'destroy'])->name('destroy');
        });
});

// 🔂 Auth tambahan
require __DIR__ . '/auth.php';
