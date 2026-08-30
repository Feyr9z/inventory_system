<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Menambahkan kolom untuk mendukung FIFO dan traceability transaksi:
     *
     * barang_masuk:
     *   - sisa_jumlah : sisa stok dari lot ini yang belum dikeluarkan (FIFO lot tracking)
     *   - user_id     : siapa yang menginput (untuk monitoring Kepala Gudang)
     *
     * barang_keluar:
     *   - user_id     : siapa yang menginput (untuk monitoring Kepala Gudang)
     */
    public function up(): void
    {
        Schema::table('barang_masuk', function (Blueprint $table) {
            // Kolom FIFO: sisa lot yang belum dikeluarkan
            $table->unsignedInteger('sisa_jumlah')->default(0)->after('jumlah');

            // Traceability: user yang menginput transaksi
            $table->foreignId('user_id')
                  ->nullable()
                  ->after('barang_id')
                  ->constrained('users')
                  ->nullOnDelete();
        });

        Schema::table('barang_keluar', function (Blueprint $table) {
            // Traceability: user yang menginput transaksi
            $table->foreignId('user_id')
                  ->nullable()
                  ->after('barang_id')
                  ->constrained('users')
                  ->nullOnDelete();
        });

        // Backfill: set sisa_jumlah = jumlah untuk semua lot masuk yang ada
        // (data lama dianggap masih full karena belum ada FIFO)
        \DB::table('barang_masuk')->update(['sisa_jumlah' => \DB::raw('jumlah')]);
    }

    public function down(): void
    {
        Schema::table('barang_masuk', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            $table->dropColumn(['sisa_jumlah', 'user_id']);
        });

        Schema::table('barang_keluar', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            $table->dropColumn('user_id');
        });
    }
};
