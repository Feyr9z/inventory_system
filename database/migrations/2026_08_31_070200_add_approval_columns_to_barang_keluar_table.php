<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Menambahkan kolom status persetujuan (approval workflow) pada tabel barang_keluar:
     * - status            : 'pending', 'disetujui', 'ditolak' (default 'disetujui' untuk kompatibilitas data lama)
     * - approved_by       : user_id Kepala Gudang / Admin yang memverifikasi
     * - catatan_penolakan : alasan penolakan jika transaksi ditolak
     * - approved_at       : waktu persetujuan/penolakan diproses
     */
    public function up(): void
    {
        Schema::table('barang_keluar', function (Blueprint $table) {
            $table->string('status', 20)->default('disetujui')->after('tujuan');
            $table->foreignId('approved_by')
                  ->nullable()
                  ->after('status')
                  ->constrained('users')
                  ->nullOnDelete();
            $table->text('catatan_penolakan')->nullable()->after('approved_by');
            $table->timestamp('approved_at')->nullable()->after('catatan_penolakan');
        });
    }

    public function down(): void
    {
        Schema::table('barang_keluar', function (Blueprint $table) {
            $table->dropForeign(['approved_by']);
            $table->dropColumn(['status', 'approved_by', 'catatan_penolakan', 'approved_at']);
        });
    }
};
