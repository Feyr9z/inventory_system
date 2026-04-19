<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Models\Barang;
use App\Models\BarangMasuk;
use App\Models\BarangKeluar;
use App\Models\StockOpname;
use App\Models\Kategori;
use App\Observers\BarangObserver;
use App\Observers\BarangMasukObserver;
use App\Observers\BarangKeluarObserver;
use App\Observers\StockOpnameObserver;
use App\Observers\KategoriObserver;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Barang::observe(BarangObserver::class);
        BarangMasuk::observe(BarangMasukObserver::class);
        BarangKeluar::observe(BarangKeluarObserver::class);
        StockOpname::observe(StockOpnameObserver::class);
        Kategori::observe(KategoriObserver::class);
    }
}
