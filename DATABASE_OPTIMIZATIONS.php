<?php

/**
 * ADVANCED MIGRATIONS & DATABASE OPTIMIZATIONS
 * 
 * Reference code untuk fitur-fitur tambahan
 * Copy dan gunakan sesuai kebutuhan
 */

/**
 * ========================================
 * 1. MIGRATION: ADD INDEXES
 * ========================================
 * 
 * Command: php artisan make:migration add_indexes_to_tables
 * 
 * Jalankan: php artisan migrate
 */

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        // Index di barang table
        Schema::table('barang', function (Blueprint $table) {
            $table->index('kategori_id');
            $table->index('created_at');
            $table->index('stok');
        });

        // Index di transaksi tables
        Schema::table('barang_masuk', function (Blueprint $table) {
            $table->index(['barang_id', 'tanggal']);
            $table->index('created_at');
        });

        Schema::table('barang_keluar', function (Blueprint $table) {
            $table->index(['barang_id', 'tanggal']);
            $table->index('created_at');
        });

        Schema::table('stock_opname', function (Blueprint $table) {
            $table->index(['barang_id', 'tanggal']);
        });

        // Index di log_aktivitas
        Schema::table('log_aktivitas', function (Blueprint $table) {
            $table->index('user_id');
            $table->index('waktu');
        });
    }

    public function down(): void {
        Schema::table('barang', function (Blueprint $table) {
            $table->dropIndex(['kategori_id']);
            $table->dropIndex(['created_at']);
            $table->dropIndex(['stok']);
        });

        Schema::table('barang_masuk', function (Blueprint $table) {
            $table->dropIndex(['barang_id', 'tanggal']);
            $table->dropIndex(['created_at']);
        });

        Schema::table('barang_keluar', function (Blueprint $table) {
            $table->dropIndex(['barang_id', 'tanggal']);
            $table->dropIndex(['created_at']);
        });

        Schema::table('stock_opname', function (Blueprint $table) {
            $table->dropIndex(['barang_id', 'tanggal']);
        });

        Schema::table('log_aktivitas', function (Blueprint $table) {
            $table->dropIndex(['user_id']);
            $table->dropIndex(['waktu']);
        });
    }
};


/**
 * ========================================
 * 2. MIGRATION: ADD APPROVAL WORKFLOW
 * ========================================
 * 
 * Command: php artisan make:migration add_approval_to_transaksi
 */

return new class extends Migration {
    public function up(): void {
        // Untuk barang_keluar dengan approval system
        Schema::table('barang_keluar', function (Blueprint $table) {
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending')->after('tujuan');
            $table->unsignedBigInteger('approved_by')->nullable()->after('status');
            $table->timestamp('approved_at')->nullable()->after('approved_by');
            $table->text('approval_notes')->nullable()->after('approved_at');
            
            $table->foreign('approved_by')->references('id')->on('users')->nullOnDelete();
        });
    }

    public function down(): void {
        Schema::table('barang_keluar', function (Blueprint $table) {
            $table->dropForeign(['approved_by']);
            $table->dropColumn(['status', 'approved_by', 'approved_at', 'approval_notes']);
        });
    }
};


/**
 * ========================================
 * 3. MIGRATION: ADD FILE ATTACHMENT
 * ========================================
 */

return new class extends Migration {
    public function up(): void {
        Schema::table('stock_opname', function (Blueprint $table) {
            $table->string('attachment')->nullable()->after('tanggal');
            $table->text('keterangan')->nullable()->after('attachment');
        });
    }

    public function down(): void {
        Schema::table('stock_opname', function (Blueprint $table) {
            $table->dropColumn(['attachment', 'keterangan']);
        });
    }
};


/**
 * ========================================
 * 4. MIGRATION: SOFT DELETE FOR AUDITING
 * ========================================
 */

return new class extends Migration {
    public function up(): void {
        Schema::table('barang', function (Blueprint $table) {
            $table->softDeletes();
        });

        Schema::table('barang_masuk', function (Blueprint $table) {
            $table->softDeletes();
        });

        Schema::table('barang_keluar', function (Blueprint $table) {
            $table->softDeletes();
        });
    }

    public function down(): void {
        Schema::table('barang', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });

        Schema::table('barang_masuk', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });

        Schema::table('barang_keluar', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });
    }
};


/**
 * ========================================
 * 5. MIGRATION: AUDIT LOG TABLE
 * ========================================
 * 
 * Untuk tracking semua perubahan data
 */

return new class extends Migration {
    public function up(): void {
        Schema::create('audit_logs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->string('model_type');
            $table->unsignedBigInteger('model_id');
            $table->string('action'); // created, updated, deleted
            $table->json('old_values')->nullable();
            $table->json('new_values')->nullable();
            $table->text('description')->nullable();
            $table->string('ip_address')->nullable();
            $table->string('user_agent')->nullable();
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->nullOnDelete();
            $table->index(['model_type', 'model_id']);
            $table->index('created_at');
        });
    }

    public function down(): void {
        Schema::dropIfExists('audit_logs');
    }
};


/**
 * ========================================
 * 6. MIGRATION: NOTIFICATIONS TABLE
 * ========================================
 */

return new class extends Migration {
    public function up(): void {
        Schema::create('notifications', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->string('type'); // 'low_stock', 'approval_pending', dll
            $table->string('title');
            $table->text('message');
            $table->json('data')->nullable();
            $table->timestamp('read_at')->nullable();
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->cascadeOnDelete();
            $table->index('user_id');
            $table->index('read_at');
        });
    }

    public function down(): void {
        Schema::dropIfExists('notifications');
    }
};


/**
 * ========================================
 * 7. DATABASE VIEWS (SQL)
 * ========================================
 * 
 * Untuk report dan analytics
 * 
 * Jalankan di psql:
 */

-- View untuk stok summary per kategori
CREATE VIEW v_stok_per_kategori AS
SELECT 
    k.id,
    k.nama_kategori,
    COUNT(b.id) as jumlah_barang,
    SUM(b.stok) as total_stok,
    AVG(b.stok) as rata_rata_stok,
    COUNT(CASE WHEN b.stok < b.stok_minimum THEN 1 END) as barang_kurang_stok
FROM kategori k
LEFT JOIN barang b ON k.id = b.kategori_id
GROUP BY k.id, k.nama_kategori;

-- View untuk transaksi harian
CREATE VIEW v_transaksi_harian AS
SELECT 
    DATE(tanggal) as tanggal,
    'masuk' as tipe,
    COUNT(*) as jumlah_transaksi,
    SUM(jumlah) as total_qty
FROM barang_masuk
GROUP BY DATE(tanggal)
UNION ALL
SELECT 
    DATE(tanggal) as tanggal,
    'keluar' as tipe,
    COUNT(*) as jumlah_transaksi,
    SUM(jumlah) as total_qty
FROM barang_keluar
GROUP BY DATE(tanggal);

-- View untuk barang dengan performa turnover
CREATE VIEW v_barang_turnover AS
SELECT 
    b.id,
    b.nama_barang,
    b.stok,
    COALESCE(COUNT(bk.id), 0) as jumlah_keluar,
    COALESCE(SUM(bk.jumlah), 0) as total_qty_keluar,
    CASE 
        WHEN b.stok > 0 THEN ROUND(COALESCE(SUM(bk.jumlah), 0)::numeric / b.stok, 2)
        ELSE 0
    END as turnover_ratio
FROM barang b
LEFT JOIN barang_keluar bk ON b.id = bk.barang_id 
    AND bk.tanggal >= CURRENT_DATE - INTERVAL '30 days'
GROUP BY b.id, b.nama_barang, b.stok;


/**
 * ========================================
 * 8. QUERY OPTIMIZATION EXAMPLES
 * ========================================
 */

-- Buruk: N+1 query problem
$barang = Barang::all();
foreach ($barang as $item) {
    echo $item->kategori->nama_kategori;
}

-- Baik: Eager loading
$barang = Barang::with('kategori')->get();
foreach ($barang as $item) {
    echo $item->kategori->nama_kategori;
}

-- Lebih baik: Select only needed columns
$barang = Barang::select('id', 'nama_barang', 'kategori_id', 'stok')
    ->with('kategori:id,nama_kategori')
    ->get();


/**
 * ========================================
 * NOTES
 * ========================================
 * 
 * - Selalu backup database sebelum migrate
 * - Test migrations di local terlebih dahulu
 * - Gunakan transactions untuk critical data
 * - Monitor performance dengan EXPLAIN
 * - Archive old logs untuk storage efficiency
 * - Implement proper backup strategy
 * - Use database constraints untuk data integrity
 */
