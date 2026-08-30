<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Tabel penghubung FIFO (Lot Fulfillment / Traceability):
     * Mencatat setiap lot barang_masuk yang dikonsumsi oleh transaksi barang_keluar
     */
    public function up(): void
    {
        Schema::create('barang_keluar_detail', function (Blueprint $table) {
            $table->id();

            $table->foreignId('barang_keluar_id')
                  ->constrained('barang_keluar')
                  ->cascadeOnDelete();

            $table->foreignId('barang_masuk_id')
                  ->constrained('barang_masuk')
                  ->cascadeOnDelete();

            $table->unsignedInteger('jumlah_diambil');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('barang_keluar_detail');
    }
};
