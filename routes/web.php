<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\BarangController;
use App\Http\Controllers\BarangMasukController;
use App\Http\Controllers\BarangKeluarController;
use App\Http\Controllers\StockOpnameController;
use App\Http\Controllers\KategoriController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\LaporanController;
use App\Http\Controllers\LogAktivitasController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// Root Route
Route::redirect('/', '/login');

// Authentication Routes
Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login')->middleware('guest');
Route::post('/login', [LoginController::class, 'login'])->name('login.post')->middleware('guest');
Route::post('/logout', [LoginController::class, 'logout'])->name('logout')->middleware('auth');

// Inventory Routes (Protected by auth)
Route::middleware('auth')->prefix("inventory")->name("inventory.")->group(function () {
    // ======================
    // DASHBOARD
    // ======================
    Route::get("/", [DashboardController::class, "index"])->name("dashboard");

    // ======================
    // BARANG (CRUD) - Admin Only, View for Staff
    // ======================
    // Admin: full CRUD except index (will define separately)
    Route::middleware('role:admin')->group(function () {
        Route::resource("barang", BarangController::class)->except(['index', 'show']);
    });
    
    // Index accessible to Admin & Staff & Management
    Route::middleware('role:admin,staff,management')->group(function () {
        Route::get("barang", [BarangController::class, 'index'])->name("barang.index");
    });

    // ======================
    // KATEGORI - Admin Only
    // ======================
    Route::middleware('role:admin')->group(function () {
        Route::resource("kategori", KategoriController::class);
    });

    // ======================
    // USER MANAGEMENT - Admin Only
    // ======================
    Route::middleware('role:admin')->group(function () {
        Route::resource("user", UserController::class)->except(['show']);
    });

    // ======================
    // TRANSAKSI - Admin & Staff
    // ======================
    Route::middleware('role:admin,staff')->prefix("transaksi")
        ->name("transaksi.")
        ->group(function () {
            // Barang Masuk
            Route::get("masuk", [
                BarangMasukController::class,
                "create",
            ])->name("masuk.create");
            Route::post("masuk", [
                BarangMasukController::class,
                "store",
            ])->name("masuk.store");

            // Barang Keluar
            Route::get("keluar", [
                BarangKeluarController::class,
                "create",
            ])->name("keluar.create");
            Route::post("keluar", [
                BarangKeluarController::class,
                "store",
            ])->name("keluar.store");

            // Stock Opname - Admin Only
            Route::middleware('role:admin')->group(function () {
                Route::get("opname", [
                    StockOpnameController::class,
                    "create",
                ])->name("opname.create");
                Route::post("opname", [
                    StockOpnameController::class,
                    "store",
                ])->name("opname.store");
                Route::get("opname-history", [
                    StockOpnameController::class,
                    "history",
                ])->name("opname.history");
            });
        });

    // ======================
    // LAPORAN - Admin & Management
    // ======================
    Route::middleware('role:admin,management')->group(function () {
        Route::get("laporan/transaksi", [LaporanController::class, 'transaksi'])->name('laporan.transaksi');
        Route::get("laporan/stok", [LaporanController::class, 'stok'])->name('laporan.stok');
        Route::get("laporan/transaksi/export/csv", [LaporanController::class, 'exportTransaksiCsv'])->name('laporan.transaksi.export');
        Route::get("laporan/stok/export/csv", [LaporanController::class, 'exportStokCsv'])->name('laporan.stok.export');
    });

    // ======================
    // LOG AKTIVITAS - Admin & Management
    // ======================
    Route::middleware('role:admin,management')->group(function () {
        Route::get("log-aktivitas", [LogAktivitasController::class, 'index'])->name('log-aktivitas');
    });
});
