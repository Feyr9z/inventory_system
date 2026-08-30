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
use App\Http\Controllers\StaffPersonalTransaksiController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// Root Route
Route::redirect('/', '/login');

// Authentication Routes
Route::get('/login',  [LoginController::class, 'showLoginForm'])->name('login')->middleware('guest');
Route::post('/login', [LoginController::class, 'login'])->name('login.post')->middleware('guest');
Route::post('/logout',[LoginController::class, 'logout'])->name('logout')->middleware('auth');

// Inventory Routes (Protected by auth)
Route::middleware('auth')->prefix('inventory')->name('inventory.')->group(function () {

    // ======================
    // DASHBOARD — semua role
    // ======================
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

    // ======================
    // BARANG
    // Admin + Kepala Gudang: create / edit / update
    // Admin only           : destroy
    // Semua role           : index
    // ======================
    Route::middleware('role:admin,kepala_gudang')->group(function () {
        Route::get   ('barang/create',       [BarangController::class, 'create'])->name('barang.create');
        Route::post  ('barang',              [BarangController::class, 'store']) ->name('barang.store');
        Route::get   ('barang/{barang}/edit',[BarangController::class, 'edit'])  ->name('barang.edit');
        Route::put   ('barang/{barang}',     [BarangController::class, 'update'])->name('barang.update');
    });

    Route::middleware('role:admin')->group(function () {
        Route::delete('barang/{barang}',     [BarangController::class, 'destroy'])->name('barang.destroy');
    });

    Route::middleware('role:admin,staff,kepala_gudang,management')->group(function () {
        Route::get('barang',          [BarangController::class, 'index'])->name('barang.index');
        Route::get('barang/{barang}', [BarangController::class, 'show'])->name('barang.show');
    });

    // ======================
    // KATEGORI — Admin + Kepala Gudang
    // ======================
    Route::middleware('role:admin,kepala_gudang')->group(function () {
        Route::resource('kategori', KategoriController::class);
    });

    // ======================
    // USER MANAGEMENT — Admin Only
    // ======================
    Route::middleware('role:admin')->group(function () {
        Route::resource('user', UserController::class)->except(['show']);
    });

    // ======================
    // TRANSAKSI
    // ======================
    Route::prefix('transaksi')->name('transaksi.')->group(function () {

        // Barang Masuk — Admin + Staff (Kepala Gudang memantau, tidak input)
        Route::middleware('role:admin,staff')->group(function () {
            Route::get ('masuk', [BarangMasukController::class, 'create'])->name('masuk.create');
            Route::post('masuk', [BarangMasukController::class, 'store']) ->name('masuk.store');
        });

        // Barang Keluar — Admin + Staff
        Route::middleware('role:admin,staff')->group(function () {
            Route::get ('keluar', [BarangKeluarController::class, 'create'])->name('keluar.create');
            Route::post('keluar', [BarangKeluarController::class, 'store']) ->name('keluar.store');
        });

        // Stock Opname (input) — Admin + Kepala Gudang
        Route::middleware('role:admin,kepala_gudang')->group(function () {
            Route::get ('opname', [StockOpnameController::class, 'create'])->name('opname.create');
            Route::post('opname', [StockOpnameController::class, 'store']) ->name('opname.store');
        });

        // History Opname — Admin + Kepala Gudang + Management
        Route::middleware('role:admin,kepala_gudang,management')->group(function () {
            Route::get('opname-history', [StockOpnameController::class, 'history'])->name('opname.history');
        });

        // Transaksi Saya (Personal) — Admin + Staff
        Route::middleware('role:admin,staff')->group(function () {
            Route::get('saya', [StaffPersonalTransaksiController::class, 'index'])->name('saya');
        });
    });

    // ======================
    // LAPORAN — Admin + Kepala Gudang + Management
    // ======================
    Route::middleware('role:admin,kepala_gudang,management')->group(function () {
        Route::get('laporan/transaksi',          [LaporanController::class, 'transaksi'])       ->name('laporan.transaksi');
        Route::get('laporan/stok',               [LaporanController::class, 'stok'])            ->name('laporan.stok');
        Route::get('laporan/transaksi/export/csv',[LaporanController::class, 'exportTransaksiCsv'])->name('laporan.transaksi.export');
        Route::get('laporan/stok/export/csv',    [LaporanController::class, 'exportStokCsv'])   ->name('laporan.stok.export');
    });

    // ======================
    // LOG AKTIVITAS — Admin + Kepala Gudang + Management
    // ======================
    Route::middleware('role:admin,kepala_gudang,management')->group(function () {
        Route::get('log-aktivitas', [LogAktivitasController::class, 'index'])->name('log-aktivitas');
    });

    // ======================
    // CETAK DOKUMEN LOGISTIK & STRUK RESMI — Semua Role Terotentikasi
    // ======================
    Route::prefix('receipt')->name('receipt.')->group(function () {
        Route::get('masuk/{id}',  [LaporanController::class, 'printMasukReceipt'])->name('masuk');
        Route::get('keluar/{id}', [LaporanController::class, 'printKeluarReceipt'])->name('keluar');
        Route::get('opname/{id}', [StockOpnameController::class, 'printOpnameReceipt'])->name('opname');
    });
});
