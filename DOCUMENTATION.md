# Inventory Management System - Laravel 11

Sistem manajemen inventaris berbasis Laravel 11 dengan PostgreSQL untuk tracking barang masuk, barang keluar, dan stock opname.

## 📋 Fitur Utama

✅ **Dashboard**: Tampilan ringkas total barang dan total stok
✅ **Manajemen Barang**: CRUD lengkap untuk data barang dengan kategori
✅ **Transaksi**:
   - Barang Masuk: Pencatatan barang yang masuk dari supplier
   - Barang Keluar: Pencatatan barang yang keluar ke departemen/customer
   - Stock Opname: Verifikasi stok fisik dengan stok sistem
✅ **Validasi**: Input validation untuk semua field required
✅ **Error Handling**: Pesan error yang jelas untuk pengguna
✅ **User Seeding**: Data pengguna demo dengan role berbeda

## 🗄️ Struktur Database

### Tabel Utama:
- **users**: User dengan roles (admin, staff, management)
- **kategori**: Kategori barang
- **barang**: Master data barang dengan stok real-time
- **barang_masuk**: History transaksi masuk barang
- **barang_keluar**: History transaksi keluar barang
- **stock_opname**: History verifikasi stock
- **log_aktivitas**: Log semua aktivitas user (untuk audit trail)

## 🚀 Setup & Instalasi

### Prerequisites:
- PHP 8.2+
- PostgreSQL
- Composer

### Installation:

```bash
# Clone atau download project
cd inventory

# Install dependencies
composer install

# Copy env file
cp .env.example .env

# Generate app key
php artisan key:generate

# Konfigurasi database di .env:
DB_CONNECTION=pgsql
DB_HOST=127.0.0.1
DB_PORT=5432
DB_DATABASE=inventory_db
DB_USERNAME=user
DB_PASSWORD=password

# Run migrations dan seeders
php artisan migrate --seed

# Start server
php artisan serve
```

Akses: http://localhost:8000/inventory

## 👤 Test Users (dari seeder):
- **Admin**: admin@inventory.test / password
- **Staff**: staff@inventory.test / password
- **Management**: management@inventory.test / password

## 📍 API Routes

```
GET     /inventory                          → Dashboard
GET     /inventory/barang                   → List Barang
GET     /inventory/barang/create            → Form Tambah Barang
POST    /inventory/barang                   → Store Barang
GET     /inventory/barang/{id}/edit         → Form Edit Barang
PUT     /inventory/barang/{id}              → Update Barang
DELETE  /inventory/barang/{id}              → Hapus Barang

GET     /inventory/transaksi/masuk          → Form Barang Masuk
POST    /inventory/transaksi/masuk          → Store Barang Masuk

GET     /inventory/transaksi/keluar         → Form Barang Keluar
POST    /inventory/transaksi/keluar         → Store Barang Keluar

GET     /inventory/transaksi/opname         → Form Stock Opname
POST    /inventory/transaksi/opname         → Store Stock Opname
```

## 💼 Logika Bisnis

### Barang Masuk:
```
stok_barang += jumlah_masuk
```

### Barang Keluar:
```
if jumlah_keluar > stok_barang:
    reject (Stok tidak cukup)
else:
    stok_barang -= jumlah_keluar
```

### Stock Opname:
```
selisih = stok_fisik - stok_sistem
stok_barang = stok_fisik (update ke nilai fisik)
```

## 📁 File Structure

```
app/
├── Http/Controllers/
│   ├── DashboardController.php
│   ├── BarangController.php
│   ├── BarangMasukController.php
│   ├── BarangKeluarController.php
│   └── StockOpnameController.php
├── Models/
│   ├── User.php
│   ├── Barang.php
│   ├── Kategori.php
│   ├── BarangMasuk.php
│   ├── BarangKeluar.php
│   ├── StockOpname.php
│   └── LogAktivitas.php

database/
├── migrations/
└── seeders/
    ├── DatabaseSeeder.php
    ├── KategoriSeeder.php
    ├── UserSeeder.php
    └── BarangSeeder.php

resources/views/
├── layouts/app.blade.php
├── dashboard.blade.php
├── barang/
│   ├── index.blade.php
│   ├── create.blade.php
│   └── edit.blade.php
└── transaksi/
    ├── masuk.blade.php
    ├── keluar.blade.php
    └── opname.blade.php

routes/web.php
```

## 💡 Rekomendasi Pengembangan

### 1. **Role-Based Access Control (RBAC)** ⭐
   Implementasi authorization untuk membatasi akses berdasarkan role.
   
   ```php
   // app/Http/Middleware/AdminMiddleware.php
   public function handle(Request $request, Closure $next) {
       if (auth()->check() && auth()->user()->role === 'admin') {
           return $next($request);
       }
       return redirect('/inventory')->with('error', 'Unauthorized');
   }
   
   // routes/web.php
   Route::delete('/inventory/barang/{id}', [BarangController::class, 'destroy'])
       ->middleware('admin');
   ```

### 2. **Activity Logging** ⭐
   Audit trail lengkap untuk setiap transaksi dan perubahan data.
   
   ```php
   // app/Traits/LogActivity.php
   trait LogActivity {
       public static function boot() {
           parent::boot();
           
           static::created(function ($model) {
               LogAktivitas::create([
                   'user_id' => auth()->id(),
                   'aktivitas' => "Created {$model->getTable()}: {$model->id}",
               ]);
           });
       }
   }
   
   // Di Model
   class Barang extends Model {
       use LogActivity;
   }
   ```

### 3. **Laporan Export (Excel/PDF)** ⭐
   Fitur untuk mengexport data dalam format Excel atau PDF.
   
   ```bash
   composer require maatwebsite/excel
   
   # Buat report controller
   php artisan make:controller ReportController
   ```
   
   ```php
   public function exportBarang() {
       return Excel::download(
           new BarangExport, 
           'barang-' . date('Y-m-d') . '.xlsx'
       );
   }
   ```

### 4. **Notifikasi Stok Minimum** ⭐
   Alert otomatis ketika stok barang di bawah minimum.
   
   ```php
   // app/Observers/BarangObserver.php
   public function updated(Barang $barang) {
       if ($barang->stok < $barang->stok_minimum) {
           // Send notification to management
           Notification::send(
               User::where('role', 'management')->get(),
               new LowStockNotification($barang)
           );
       }
   }
   ```

### 5. **Dashboard Analytics**
   Visualisasi data dengan grafik dan statistik yang lebih detail.
   
   ```php
   // Tambahkan ke DashboardController
   public function index() {
       return view('dashboard', [
           'total' => Barang::count(),
           'stok' => Barang::sum('stok'),
           'kategoriStats' => Kategori::withCount('barang')->get(),
           'barangTerjual' => BarangKeluar::count(),
           'trendChart' => $this->getTrendData(),
       ]);
   }
   ```

### 6. **RESTful API Development**
   API untuk integrasi dengan aplikasi mobile atau eksternal.
   
   ```php
   // routes/api.php
   Route::apiResource('barang', Api\BarangController::class);
   Route::apiResource('transaksi', Api\TransaksiController::class);
   
   // Response format JSON
   return response()->json([
       'success' => true,
       'data' => $barang,
       'message' => 'Data retrieved successfully'
   ]);
   ```

### 7. **Advanced Approval Workflow**
   Sistem persetujuan untuk transaksi tertentu.
   
   ```php
   // Tambah kolom status di tabel barang_keluar
   $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');
   
   // Buat ApprovalController
   public function approve(BarangKeluar $transaksi) {
       $transaksi->update(['status' => 'approved']);
       // Process transaction
   }
   ```

### 8. **File Upload & Documentation**
   Attachment untuk setiap transaksi sebagai dokumentasi.
   
   ```php
   // Tambahkan kolom
   $table->string('attachment')->nullable();
   
   // Storage
   Storage::disk('local')->put('transaksi', $file);
   ```

### 9. **Database Query Optimization**
   Performance tuning untuk aplikasi yang lebih responsif.
   
   ```php
   // Tambah indexes di migrations
   $table->index('kategori_id');
   $table->index('tanggal');
   $table->index(['barang_id', 'tanggal']);
   
   // Gunakan select() untuk query yang lebih efisien
   $barang = Barang::select('id', 'nama_barang', 'stok')
       ->with('kategori:id,nama_kategori')
       ->paginate(15);
   ```

### 10. **Comprehensive Testing**
    Unit dan feature tests untuk memastikan kualitas kode.
    
    ```bash
    # Feature test
    php artisan make:test BarangControllerTest
    
    # Unit test
    php artisan make:test Models/BarangTest --unit
    ```
    
    ```php
    public function test_can_create_barang() {
        $response = $this->post('/inventory/barang', [
            'nama_barang' => 'Test Item',
            'kategori_id' => 1,
            'stok_minimum' => 5,
        ]);
        
        $response->assertRedirect();
        $this->assertDatabaseHas('barang', ['nama_barang' => 'Test Item']);
    }
    ```

### 11. **Real-time Notifications**
    Notifikasi real-time menggunakan websocket atau polling.
    
    ```bash
    composer require laravel-websockets/laravel-websockets
    ```

### 12. **Multi-language Support**
    Dukungan untuk berbagai bahasa.
    
    ```php
    // resources/lang/id/messages.php
    return [
        'stok_tidak_cukup' => 'Stok tidak cukup',
        'berhasil_disimpan' => 'Data berhasil disimpan',
    ];
    
    // Penggunaan
    __('messages.berhasil_disimpan')
    ```

## ✅ Validation Rules (Implemented)

- `nama_barang`: required, string, max:255
- `kategori_id`: required, exists:kategori,id
- `stok_minimum`: required, integer, min:0
- `jumlah`: required, integer, min:1
- `tanggal`: required, date
- `sumber/tujuan`: required, string, max:255

## ⚡ Performance Tips

1. **Use eager loading:**
   ```php
   $barang = Barang::with('kategori', 'barangMasuk')->get();
   ```

2. **Add indexes:**
   ```php
   Schema::table('barang', function (Blueprint $table) {
       $table->index('kategori_id');
       $table->index('created_at');
   });
   ```

3. **Implement pagination:**
   ```php
   $barang = Barang::with('kategori')->paginate(15);
   ```

4. **Cache frequently accessed data:**
   ```php
   Cache::remember('total_barang', 3600, fn() => Barang::count());
   ```

5. **Use database query optimization:**
   ```php
   // Buruk
   foreach ($barang as $item) {
       $kategori = $item->kategori;
   }
   
   // Baik
   $barang = Barang::with('kategori')->get();
   ```

## 🔒 Security Best Practices

✅ CSRF protection (automatic di form)
✅ SQL injection prevention (Eloquent ORM)
✅ Input validation
✅ Password hashing (bcrypt)
✅ Secure session management

### Tambahan untuk Production:

- Implement authentication middleware
- Rate limiting untuk API:
  ```php
  Route::middleware('throttle:60,1')->group(function () {
      Route::post('/inventory/transaksi', ...);
  });
  ```
- HTTPS enforcement
- Input sanitization
- File upload security
- CORS configuration untuk API

## 🐛 Troubleshooting

### Error: "No such file or directory" saat migrate
```bash
# Pastikan PostgreSQL running
# Check .env configuration
php artisan config:clear
php artisan migrate:refresh --seed
```

### Error: "Stok tidak cukup" saat barang keluar
- Check stok barang terlebih dahulu di list
- Verifikasi data:
  ```sql
  SELECT nama_barang, stok FROM barang WHERE id = X;
  ```

### Server tidak running
```bash
php artisan serve --port=8000
# Access http://127.0.0.1:8000/inventory
```

### Clear cache
```bash
php artisan cache:clear
php artisan config:clear
php artisan view:clear
```

## 📦 Dependencies

```
- laravel/framework: ^11.0
- laravel/tinker
- laravel/sail
- postgresql: ^15
```

## 🎯 Next Steps

1. Implement role-based middleware
2. Add activity logging untuk audit trail
3. Create export functionality
4. Build mobile API
5. Add real-time notifications
6. Create comprehensive test suite
7. Deploy ke production dengan security best practices

## 📝 Notes

- Semua form menggunakan CSRF token
- Stok tidak bisa negatif (validasi di controller)
- Setiap transaksi tercatat dengan datetime
- User seeding otomatis saat migrate --seed
- Database transactions untuk data consistency

## 📄 License

MIT License

## 💬 Support

Untuk questions atau issues, silakan create issue atau hubungi development team.
