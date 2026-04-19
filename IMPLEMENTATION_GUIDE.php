<?php

/**
 * QUICK IMPLEMENTATION GUIDE
 * 
 * Contoh implementasi fitur-fitur yang direkomendasikan
 * untuk mempercepat development di masa depan.
 */

/**
 * ========================================
 * 1. ACTIVITY LOGGING TRAIT
 * ========================================
 * 
 * Path: app/Traits/LogActivity.php
 * 
 * Gunakan trait ini pada model yang ingin di-track:
 *   class Barang extends Model {
 *       use LogActivity;
 *   }
 */

namespace App\Traits;

use App\Models\LogAktivitas;

trait LogActivity {
    public static function booted() {
        static::created(function ($model) {
            LogAktivitas::create([
                'user_id' => auth()?->id(),
                'aktivitas' => "Membuat " . class_basename($model),
                'waktu' => now(),
            ]);
        });

        static::updated(function ($model) {
            LogAktivitas::create([
                'user_id' => auth()?->id(),
                'aktivitas' => "Mengubah " . class_basename($model),
                'waktu' => now(),
            ]);
        });

        static::deleted(function ($model) {
            LogAktivitas::create([
                'user_id' => auth()?->id(),
                'aktivitas' => "Menghapus " . class_basename($model),
                'waktu' => now(),
            ]);
        });
    }
}


/**
 * ========================================
 * 2. ADMIN MIDDLEWARE
 * ========================================
 * 
 * Path: app/Http/Middleware/AdminMiddleware.php
 * 
 * Daftar di app/Http/Kernel.php (bagian protected $routeMiddleware)
 */

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class AdminMiddleware {
    public function handle(Request $request, Closure $next) {
        if (!auth()->check()) {
            return redirect('/login');
        }

        if (auth()->user()->role !== 'admin') {
            return redirect('/inventory')
                ->with('error', 'Anda tidak memiliki akses ke halaman ini');
        }

        return $next($request);
    }
}

/**
 * USAGE di routes:
 * Route::delete('/inventory/barang/{id}', [BarangController::class, 'destroy'])
 *     ->middleware('admin');
 */


/**
 * ========================================
 * 3. LOW STOCK OBSERVER
 * ========================================
 * 
 * Path: app/Observers/BarangObserver.php
 * 
 * Daftar di AppServiceProvider:
 *   use App\Models\Barang;
 *   use App\Observers\BarangObserver;
 *   
 *   Barang::observe(BarangObserver::class);
 */

namespace App\Observers;

use App\Models\Barang;

class BarangObserver {
    public function updated(Barang $barang) {
        // Check jika stok di bawah minimum
        if ($barang->stok <= $barang->stok_minimum && 
            $barang->stok_minimum > 0) {
            
            // Log warning
            \Log::warning("Stok barang {$barang->nama_barang} di bawah minimum: {$barang->stok}/{$barang->stok_minimum}");
            
            // Bisa diperluas dengan notification, email, dll
        }
    }
}


/**
 * ========================================
 * 4. BARANG EXPORT CLASS
 * ========================================
 * 
 * Install terlebih dahulu:
 *   composer require maatwebsite/excel
 * 
 * Path: app/Exports/BarangExport.php
 */

namespace App\Exports;

use App\Models\Barang;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class BarangExport implements FromCollection, WithHeadings {
    public function collection() {
        return Barang::with('kategori')
            ->get()
            ->map(function ($barang) {
                return [
                    $barang->id,
                    $barang->nama_barang,
                    $barang->kategori->nama_kategori,
                    $barang->stok,
                    $barang->stok_minimum,
                    $barang->lokasi,
                    $barang->created_at->format('Y-m-d'),
                ];
            });
    }

    public function headings(): array {
        return ['ID', 'Nama Barang', 'Kategori', 'Stok', 'Min Stok', 'Lokasi', 'Tanggal'];
    }
}

/**
 * USAGE di controller:
 * use App\Exports\BarangExport;
 * use Maatwebsite\Excel\Facades\Excel;
 * 
 * public function export() {
 *     return Excel::download(
 *         new BarangExport,
 *         'barang-' . date('Y-m-d') . '.xlsx'
 *     );
 * }
 */


/**
 * ========================================
 * 5. TRANSAKSI MODEL EVENT
 * ========================================
 * 
 * Tambahkan ke app/Models/BarangMasuk.php
 * untuk otomatis log ke log_aktivitas
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BarangMasuk extends Model {
    protected $table = "barang_masuk";
    protected $fillable = ["barang_id", "tanggal", "jumlah", "sumber"];

    public function barang() {
        return $this->belongsTo(Barang::class);
    }

    protected static function booted() {
        static::created(function ($model) {
            $barang = $model->barang;
            LogAktivitas::create([
                'user_id' => auth()?->id(),
                'aktivitas' => "Barang masuk: {$barang->nama_barang} (qty: {$model->jumlah}) dari {$model->sumber}",
                'waktu' => now(),
            ]);
        });
    }
}


/**
 * ========================================
 * 6. PAGINATION DI VIEWS
 * ========================================
 * 
 * Update di BarangController:
 */

public function index() {
    $barang = Barang::with('kategori')->paginate(15);
    return view('barang.index', compact('barang'));
}

/**
 * Di view (barang/index.blade.php):
 * 
 * @if ($barang->count() > 0)
 *     <table>
 *         ...data...
 *     </table>
 *     {{ $barang->links() }}
 * @endif
 */


/**
 * ========================================
 * 7. API RESOURCE RESPONSE
 * ========================================
 * 
 * Path: app/Http/Resources/BarangResource.php
 */

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class BarangResource extends JsonResource {
    public function toArray($request) {
        return [
            'id' => $this->id,
            'nama' => $this->nama_barang,
            'stok' => $this->stok,
            'kategori' => $this->kategori->nama_kategori,
            'stok_minimum' => $this->stok_minimum,
            'lokasi' => $this->lokasi,
            'created_at' => $this->created_at->format('Y-m-d H:i:s'),
        ];
    }
}

/**
 * USAGE:
 * return BarangResource::collection($barang);
 */


/**
 * ========================================
 * 8. CACHING TIPS
 * ========================================
 */

// Di controller
public function index() {
    $total = Cache::remember('total_barang', 3600, function () {
        return Barang::count();
    });
    
    $stok = Cache::remember('total_stok', 3600, function () {
        return Barang::sum('stok');
    });
    
    return view('dashboard', compact('total', 'stok'));
}

// Clear cache saat ada perubahan
public function store(Request $request) {
    // ... store logic ...
    
    Cache::forget('total_barang');
    Cache::forget('total_stok');
    
    return redirect()->back();
}


/**
 * ========================================
 * 9. VALIDATION CUSTOM RULES
 * ========================================
 * 
 * Path: app/Rules/ValidStok.php
 */

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class ValidStok implements ValidationRule {
    private $barangId;

    public function __construct($barangId) {
        $this->barangId = $barangId;
    }

    public function validate(string $attribute, mixed $value, Closure $fail): void {
        $barang = \App\Models\Barang::find($this->barangId);
        
        if ($barang && $value > $barang->stok) {
            $fail('Stok tidak cukup. Tersedia: ' . $barang->stok);
        }
    }
}

/**
 * USAGE:
 * $request->validate([
 *     'jumlah' => ['required', new ValidStok($request->barang_id)]
 * ]);
 */


/**
 * ========================================
 * 10. FEATURE TEST EXAMPLE
 * ========================================
 * 
 * Path: tests/Feature/BarangControllerTest.php
 * 
 * Jalankan: php artisan test
 */

namespace Tests\Feature;

use App\Models\Barang;
use App\Models\Kategori;
use Tests\TestCase;

class BarangControllerTest extends TestCase {
    
    public function test_can_view_barang_list() {
        $response = $this->get('/inventory/barang');
        $response->assertStatus(200);
        $response->assertViewIs('barang.index');
    }
    
    public function test_can_create_barang() {
        $kategori = Kategori::factory()->create();
        
        $response = $this->post('/inventory/barang', [
            'nama_barang' => 'Test Item',
            'kategori_id' => $kategori->id,
            'stok_minimum' => 5,
            'lokasi' => 'Test Location',
        ]);
        
        $response->assertRedirect('/inventory/barang');
        $this->assertDatabaseHas('barang', ['nama_barang' => 'Test Item']);
    }
    
    public function test_cannot_keluar_barang_if_insufficient_stock() {
        $barang = Barang::factory()->create(['stok' => 5]);
        
        $response = $this->post('/inventory/transaksi/keluar', [
            'barang_id' => $barang->id,
            'jumlah' => 10,
            'tanggal' => date('Y-m-d'),
            'tujuan' => 'Test',
        ]);
        
        $response->assertSessionHasErrors('jumlah');
    }
}


/**
 * ========================================
 * NOTES
 * ========================================
 * 
 * - Selalu gunakan Eloquent untuk query
 * - Implementasi eager loading untuk relationship
 * - Cache data yang sering diakses
 * - Log setiap transaksi penting
 * - Validasi di backend, jangan andalkan frontend
 * - Gunakan middleware untuk authorization
 * - Test kode sebelum push ke production
 * - Follow Laravel conventions dan best practices
 */
