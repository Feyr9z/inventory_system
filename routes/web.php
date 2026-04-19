<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\BarangController;
use App\Http\Controllers\BarangMasukController;
use App\Http\Controllers\BarangKeluarController;
use App\Http\Controllers\StockOpnameController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

Route::prefix("inventory")
    ->name("inventory.")
    ->group(function () {
        // ======================
        // DASHBOARD
        // ======================
        Route::get("/", [DashboardController::class, "index"])->name(
            "dashboard",
        );

        // ======================
        // BARANG (CRUD)
        // ======================
        Route::resource("barang", BarangController::class);

        // ======================
        // TRANSAKSI
        // ======================
        Route::prefix("transaksi")
            ->name("transaksi.")
            ->group(function () {
                Route::post("masuk", [
                    BarangMasukController::class,
                    "store",
                ])->name("masuk.store");

                Route::post("keluar", [
                    BarangKeluarController::class,
                    "store",
                ])->name("keluar.store");

                Route::post("opname", [
                    StockOpnameController::class,
                    "store",
                ])->name("opname.store");
            });
    });
