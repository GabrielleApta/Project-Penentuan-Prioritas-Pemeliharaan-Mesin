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
    UserController
};

// 🔒 Redirect root ke login
Route::get('/', fn() => redirect()->route('login'));

// 🔐 Autentikasi
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

// 🔐 Protected routes
Route::middleware(['auth', 'verified'])->group(function () {

    // 🏠 Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::middleware('role:admin')->get('/admin/dashboard', [DashboardController::class, 'index'])->name('dashboard.index');
    Route::middleware('role:user')->get('/user/dashboard', [DashboardController::class, 'userDashboard'])->name('user.dashboard');

    // 🔧 Mesin
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

    // 📉 Depresiasi
    Route::prefix('depresiasi')->name('depresiasi.')->group(function () {
        Route::get('/', [DepresiasiController::class, 'index'])->name('index');
        Route::post('/hitung', [DepresiasiController::class, 'hitung'])->name('hitung');
        Route::get('/reset', [DepresiasiController::class, 'reset'])->name('reset');
        Route::get('/grafik', [DepresiasiController::class, 'grafik'])->name('grafik');
        Route::get('/{mesin_id}', [DepresiasiController::class, 'show'])->name('show');
        Route::get('/export-excel', [DepresiasiController::class, 'exportExcel'])->name('exportExcel');
        Route::get('/export-pdf', [DepresiasiController::class, 'exportPdf'])->name('exportPdf');
    });

    // 🔥 Prioritas SAW
    Route::prefix('prioritas')->name('prioritas.')->group(function () {
        Route::get('/', [PrioritasController::class, 'index'])->name('index');
        Route::post('/proses', [PrioritasController::class, 'proses'])->name('proses');
        Route::get('/hitung', [PrioritasController::class, 'hitungSAW'])->name('hitung');
        Route::get('/{mesin_id}/detail', [PrioritasController::class, 'detailSAW'])->name('detail');
        Route::get('/export', [PrioritasController::class, 'exportExcel'])->name('export');
        Route::get('/{mesin_id}/export', [PrioritasController::class, 'exportDetailExcel'])->name('exportDetail');
        Route::get('/print-pdf', [PrioritasController::class, 'printPDF'])->name('printPDF');
        Route::get('/{mesin_id}/detail/pdf', [PrioritasController::class, 'detailPDF'])->name('detailPDF');
    });

    // 📊 Kriteria
    Route::resource('kriteria', KriteriaController::class)->except(['show']);

    // 📝 Penilaian Mesin
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

    // 📅 Kerusakan Tahunan
    Route::resource('kerusakan-tahunan', KerusakanTahunanController::class)->except(['show']);
    Route::get('/kerusakan-tahunan/rata-rata', [KerusakanTahunanController::class, 'rataRataSkor'])->name('kerusakan-tahunan.rata-rata');
    Route::get('/kerusakan/import', [KerusakanTahunanController::class, 'showImportForm'])->name('kerusakan.import.form');
    Route::post('/kerusakan/import', [KerusakanTahunanController::class, 'import'])->name('kerusakan.import');

    // 📑 Laporan
    Route::prefix('laporan')->name('laporan.')->group(function () {
        Route::get('/', [LaporanController::class, 'index'])->name('index');
        Route::get('/generate', [LaporanController::class, 'generate'])->name('generate');
    });

    // 👤 Profil
    Route::prefix('profile')->name('profile.')->group(function () {
        Route::get('/', [ProfileController::class, 'edit'])->name('edit');
        Route::patch('/', [ProfileController::class, 'update'])->name('update');
        Route::delete('/', [ProfileController::class, 'destroy'])->name('destroy');
    });

    // 👥 Manajemen User (admin only)
    Route::middleware('role:admin')->prefix('users')->name('users.')->group(function () {
        Route::get('/', [UserController::class, 'index'])->name('index');
        Route::get('/create', [UserController::class, 'create'])->name('create');
        Route::post('/', [UserController::class, 'store'])->name('store');
        Route::get('/{user}/edit', [UserController::class, 'edit'])->name('edit');
        Route::patch('/{user}', [UserController::class, 'update'])->name('update');
        Route::delete('/{user}', [UserController::class, 'destroy'])->name('destroy');
    });
});

// 🔂 Auth tambahan (Laravel Breeze / Fortify)
require __DIR__ . '/auth.php';
