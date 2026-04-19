# 📦 PT Atha Anakhatulistiwa - Inventory Management System

Sistem manajemen inventori yang dirancang khusus untuk PT Atha Anakhatulistiwa, perusahaan yang bergerak di bidang periklanan dan event management. Aplikasi ini membantu mengelola stok barang-barang reklame seperti billboard, spanduk, baliho, vertical banner, dan peralatan event secara efisien dan terorganisir.

## 🎯 Fitur Utama

- **Dashboard Interaktif** - Tampilan ringkas stok barang, statistik transaksi, dan alert barang kurang stok
- **Manajemen Barang** - CRUD lengkap untuk data barang dan kategori (hanya Admin)
- **Transaksi Stok**
  - Barang Masuk - Pencatatan barang yang diterima dengan otomatis update stok
  - Barang Keluar - Pencatatan barang yang dikeluarkan dengan validasi stok
  - Stock Opname - Verifikasi stok fisik dengan riwayat perubahan
- **Laporan Komprehensif**
  - Laporan Transaksi dengan filter tanggal dan ekspor CSV
  - Laporan Stok dengan filter kategori, status, dan ekspor CSV
  - Laporan Opname dengan detail selisih stok
- **Log Aktivitas** - Pencatatan semua aktivitas pengguna untuk audit trail
- **Role-Based Access Control** - Kontrol akses berdasarkan 3 peran berbeda
- **Responsive UI** - Antarmuka profesional dengan Bootstrap 5, mobile-friendly

## 👥 User Roles & Permissions

### 🔴 Admin
- Kelola Barang (Buat, Lihat, Edit, Hapus)
- Kelola Kategori
- Kelola User
- Input Transaksi (Barang Masuk, Barang Keluar, Stock Opname)
- Akses Laporan & Log Aktivitas
- Dashboard lengkap

### 🟠 Staff
- Lihat Daftar Barang (Read-only)
- Input Barang Masuk
- Input Barang Keluar
- Lihat Stok Barang
- Dashboard terbatas

### 🟡 Management
- Lihat Daftar Barang (Read-only)
- Akses Laporan Transaksi & Stok
- Monitor Log Aktivitas
- Dashboard terbatas

---

## 📋 Prerequisites (Persyaratan)

Sebelum memulai instalasi, pastikan sistem Anda sudah memiliki:

### Software yang Dibutuhkan
- **PHP** 8.2 atau lebih tinggi
- **PostgreSQL** 12 atau lebih tinggi
- **Composer** (PHP Package Manager)
- **Node.js** & **npm** (untuk asset management)
- **Git** (untuk clone repository)

### Cara Cek Versi yang Terinstall

**Windows (PowerShell/Command Prompt):**
```bash
php --version
psql --version
node --version
npm --version
composer --version
git --version
```

### Instalasi Tools (Jika Belum)

**1. PHP (Windows)**
- Download dari: https://windows.php.net/download/
- Atau gunakan: [PHP Manager for IIS](https://www.iis.net/downloads/community/2018/02/php-manager-150-for-iis-1039)

**2. PostgreSQL (Windows)**
- Download dari: https://www.postgresql.org/download/windows/
- Instalasi standar, catat **password superuser (postgres)**

**3. Node.js (Windows)**
- Download dari: https://nodejs.org/ (gunakan LTS version)
- npm akan terinstall otomatis

**4. Composer (Windows)**
- Download dari: https://getcomposer.org/download/

**5. Git (Windows)**
- Download dari: https://git-scm.com/download/win

---

## 🚀 Instalasi Step-by-Step

### **Langkah 1: Clone Repository**

```bash
# Buka Command Prompt/PowerShell di folder dimana Anda ingin project berada
cd C:\Projects

# Clone repository
git clone https://github.com/Feyr9z/inventory_system.git

# Masuk ke folder project
cd inventory_system
```

### **Langkah 2: Install PHP Dependencies**

```bash
# Install semua dependencies PHP via Composer
composer install
```

> ⏳ Proses ini bisa memakan waktu 2-5 menit tergantung koneksi internet

### **Langkah 3: Setup Environment Configuration**

```bash
# Copy file environment template
copy .env.example .env
```

**Atau jika menggunakan PowerShell:**
```powershell
Copy-Item .env.example -Destination .env
```

Buka file `.env` dengan text editor dan atur konfigurasi:

```env
APP_NAME="PT Atha Inventory"
APP_ENV=local
APP_DEBUG=true
APP_URL=http://localhost:8000

# Database Configuration
DB_CONNECTION=pgsql
DB_HOST=127.0.0.1
DB_PORT=5432
DB_DATABASE=inventory_db
DB_USERNAME=postgres
DB_PASSWORD=YOUR_POSTGRES_PASSWORD_HERE
```

**⚠️ Penting:** Ganti `YOUR_POSTGRES_PASSWORD_HERE` dengan password PostgreSQL yang Anda set saat instalasi.

### **Langkah 4: Generate Application Key**

```bash
php artisan key:generate
```

Perintah ini akan menghasilkan APP_KEY di file `.env` yang digunakan untuk enkripsi session dan data sensitif.

### **Langkah 5: Setup Database**

**A. Buat Database di PostgreSQL**

Buka PostgreSQL Command Line atau GUI (pgAdmin):

```sql
CREATE DATABASE inventory_db;
```

**B. Jalankan Migrations**

```bash
# Jalankan semua database migrations
php artisan migrate
```

Output yang sukses akan terlihat seperti:
```
Migration table created successfully.
Migrating: 2024_01_01_000000_create_kategori_table
Migrated:  2024_01_01_000000_create_kategori_table (0.45s)
Migrating: 2024_01_01_000001_create_barang_table
Migrated:  2024_01_01_000001_create_barang_table (0.32s)
[... more migrations ...]
Migrating: 2024_01_01_000010_create_log_aktivitas_table
Migrated:  2024_01_01_000010_create_log_aktivitas_table (0.28s)
```

### **Langkah 6: Seed Database dengan Data Awal**

```bash
# Populate database dengan data dummy untuk testing
php artisan db:seed
```

Seeder akan membuat:
- **8 Kategori** barang (Billboard, Spanduk, Baliho, Vertical Banner, Event Toolbox, Struktur/Frame, Hardware, Supplies)
- **45 Item Barang** dengan stok, lokasi, dan kategori yang beragam
- **3 User** untuk testing: Admin, Staff, Management

### **Langkah 7: Install Node Dependencies**

```bash
# Install JavaScript dependencies untuk Vite/Tailwind
npm install
```

### **Langkah 8: Compile Assets (Optional)**

Jika Anda ingin menggunakan fitur development assets:

```bash
# Development build
npm run dev

# Production build
npm run build
```

---

## 🏃 Menjalankan Aplikasi

### **Opsi 1: Development Server (Rekomendasi untuk Testing)**

Buka **2 terminal** terpisah:

**Terminal 1 - Jalankan Laravel Development Server:**
```bash
php artisan serve
```

Server akan berjalan di `http://localhost:8000`

**Terminal 2 - Jalankan Asset Watcher (Optional):**
```bash
npm run dev
```

### **Opsi 2: Production Server (Menggunakan Web Server)**

Konfigurasi Apache/Nginx untuk serve Laravel application. [Lihat dokumentasi Laravel](https://laravel.com/docs/11/deployment)

---

## 🔐 Login & Testing

Buka browser dan akses: **http://localhost:8000**

Anda akan diarahkan ke halaman login. Gunakan kredensial seeding:

| Role | Email | Password |
|------|-------|----------|
| Admin | admin@example.com | password |
| Staff | staff@example.com | password |
| Management | management@example.com | password |

### **Testing Flow per Role**

**Admin:**
1. Login dengan admin@example.com
2. Lihat Dashboard dengan semua stats
3. Kelola Barang → Buat, Edit, Hapus barang
4. Kelola Kategori → CRUD kategori
5. Kelola User → Lihat & edit users
6. Input Transaksi → Barang Masuk, Keluar, Opname
7. Laporan → Lihat & export transaksi & stok
8. Log Aktivitas → Monitor semua aktivitas sistem

**Staff:**
1. Login dengan staff@example.com
2. Dashboard terbatas
3. Input Barang Masuk
4. Input Barang Keluar
5. Lihat Daftar Barang (Read-only)

**Management:**
1. Login dengan management@example.com
2. Dashboard terbatas
3. Akses Laporan Transaksi & Stok
4. Monitor Log Aktivitas

---

## 📊 Struktur Database

Aplikasi menggunakan 8 tabel utama:

| Tabel | Deskripsi |
|-------|-----------|
| `users` | Data pengguna (admin, staff, management) |
| `kategoris` | Kategori barang |
| `barangs` | Data master barang |
| `barang_masuks` | Transaksi barang masuk |
| `barang_keluars` | Transaksi barang keluar |
| `stock_opnames` | Riwayat stock opname |
| `log_aktivitas` | Audit trail aktivitas pengguna |
| `password_reset_tokens` | Token reset password |

---

## 🛠️ Troubleshooting

### **Error: "Base table or view not found"**

Jalankan migrations:
```bash
php artisan migrate
```

### **Error: "Class not found" atau "Undefined variable"**

Clear cache Laravel:
```bash
php artisan cache:clear
php artisan config:clear
php artisan view:clear
php artisan route:clear
```

### **Error: "SQLSTATE[HY000]: General error"**

Pastikan PostgreSQL sedang berjalan dan konfigurasi `.env` DB benar:
```bash
# Test connection
php artisan tinker
>>> DB::connection()->getPdo()
```

### **Port 8000 sudah digunakan**

Gunakan port lain:
```bash
php artisan serve --port=8001
```

### **npm install gagal**

Hapus cache npm dan coba lagi:
```bash
npm cache clean --force
npm install
```

### **Assets tidak ter-compile**

Pastikan Node.js terinstall:
```bash
node --version
npm --version
```

Jika sudah, coba:
```bash
npm run build
```

---

## 📁 Struktur Folder Project

```
inventory_system/
├── app/
│   ├── Http/
│   │   ├── Controllers/       # Business logic controllers
│   │   ├── Middleware/        # Authentication & authorization
│   │   └── Requests/          # Form validation
│   ├── Models/                # Eloquent models
│   └── Observers/             # Model events (activity logging)
├── database/
│   ├── migrations/            # Database schema
│   ├── seeders/               # Initial data
│   └── factories/             # Test data factories
├── resources/
│   ├── views/                 # Blade templates
│   │   ├── auth/              # Login/logout views
│   │   ├── dashboard.blade.php
│   │   ├── barang/            # Barang CRUD views
│   │   ├── laporan/           # Report views
│   │   ├── transaksi/         # Transaction views
│   │   ├── log-aktivitas/     # Activity log views
│   │   └── layouts/           # Base layouts
│   ├── css/                   # Stylesheets
│   └── js/                    # JavaScript files
├── routes/
│   └── web.php                # All routes (protected by middleware)
├── public/                    # Publicly accessible files (CSS, JS, images)
├── storage/                   # Logs, cache, uploads
├── .env                       # Environment configuration
├── composer.json              # PHP dependencies
├── package.json               # Node dependencies
└── README.md                  # This file
```

---

## 🔧 Maintenance & Updates

### **Backup Database**

```bash
# Backup dengan pg_dump (PostgreSQL)
pg_dump -U postgres -h localhost inventory_db > backup.sql
```

### **Restore Database**

```bash
psql -U postgres -h localhost -d inventory_db < backup.sql
```

### **Reset Database**

Hapus semua data dan jalankan fresh migration + seeding:
```bash
php artisan migrate:fresh --seed
```

⚠️ **HATI-HATI:** Perintah ini akan menghapus semua data!

### **Update Dependencies**

```bash
# Update composer packages
composer update

# Update npm packages
npm update
```

---

## 📞 Support & Troubleshooting

Jika mengalami masalah:

1. **Check Laravel logs:**
   ```bash
   # Windows
   type storage\logs\laravel.log
   
   # Linux/Mac
   tail -f storage/logs/laravel.log
   ```

2. **Enable debug mode di `.env`:**
   ```env
   APP_DEBUG=true
   ```

3. **Jalankan commands untuk clear cache:**
   ```bash
   php artisan cache:clear
   php artisan config:clear
   php artisan view:clear
   ```

4. **Periksa file permissions** (Linux/Mac):
   ```bash
   chmod -R 775 storage bootstrap/cache
   ```

---

## 📝 License

Sistem ini dikembangkan untuk PT Atha Anakhatulistiwa. Hak cipta © 2024.

---

## 📚 Dokumentasi Tambahan

Lihat file dokumentasi lengkap di repository:
- `QUICK_REFERENCE.md` - Referensi cepat fitur
- `TESTING_GUIDE.md` - Panduan testing detail
- `UI_DESIGN_SUMMARY.md` - Penjelasan desain UI

---

**Terakhir diupdate:** April 2024  
**Framework:** Laravel 11  
**Database:** PostgreSQL 12+  
**PHP Version:** 8.2+
