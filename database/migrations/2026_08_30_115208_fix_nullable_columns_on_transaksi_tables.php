<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Sinkronisasi kolom nullable dengan validasi controller.
     * barang_masuk.sumber  : nullable → NOT NULL (controller: required)
     * barang_keluar.tujuan : nullable → NOT NULL (controller: required)
     */
    public function up(): void
    {
        // Isi nilai default untuk data lama yang mungkin null sebelum constraint diterapkan
        \DB::table('barang_masuk')->whereNull('sumber')->update(['sumber' => '-']);
        \DB::table('barang_keluar')->whereNull('tujuan')->update(['tujuan' => '-']);

        Schema::table('barang_masuk', function (Blueprint $table) {
            $table->string('sumber')->nullable(false)->change();
        });

        Schema::table('barang_keluar', function (Blueprint $table) {
            $table->string('tujuan')->nullable(false)->change();
        });
    }

    public function down(): void
    {
        Schema::table('barang_masuk', function (Blueprint $table) {
            $table->string('sumber')->nullable()->change();
        });

        Schema::table('barang_keluar', function (Blueprint $table) {
            $table->string('tujuan')->nullable()->change();
        });
    }
};
