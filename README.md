# PT Atha Anakhatulistiwa - Inventory Management System

Sistem Manajemen Inventoris berbasis web yang dirancang untuk mengelola stok barang, transaksi masuk/keluar, audit stock opname, serta laporan dan log aktivitas pengguna secara efisien dan terstruktur.

---

## Fitur Utama

- **Dashboard Analytic**: Menampilkan statistik ringkas total barang, unit stok, barang kurang stok, serta akses cepat transaksi.
- **Manajemen Master Data**: Pengelolaan data barang dan kategori dengan kontrol hak akses.
- **Transaksi Stok**:
  - **Barang Masuk**: Pencatatan penerimaan barang dari supplier dengan otomatisasi update stok.
  - **Barang Keluar**: Pencatatan pengeluaran barang dengan validasi ketersediaan stok.
  - **Stock Opname**: Audit verifikasi stok fisik dengan kalkulasi selisih otomatis dan riwayat perubahan.
- **Laporan & Eksportir Data**:
  - Laporan Transaksi (filter tanggal & tipe transaksi, ekspor CSV).
  - Laporan Stok (filter kategori & status stok, ekspor CSV).
  - History Opname dengan audit trail selisih stok (Surplus, Defisit, Sesuai).
- **Log Aktivitas**: Audit trail otomatis mencatat seluruh aktivitas pengguna demi keamanan sistem.
- **Role-Based Access Control (RBAC)**: Pembagian hak akses terstruktur (Admin, Staff, Management).

---

## Peran Pengguna & Hak Akses

### Admin
- Akses Penuh: Kelola Barang, Kategori, User, Transaksi, Laporan, dan Log Aktivitas.

### Staff
- Input Transaksi: Barang Masuk dan Barang Keluar.
- Monitoring: Lihat Daftar Stok Barang.

### Management
- Pemantauan & Analisis: Lihat Laporan Transaksi, Laporan Stok, History Opname, dan Log Aktivitas (Read-only).

---

## Persyaratan Sistem (Prerequisites)

- **PHP**: 8.2 atau lebih baru
- **Database**: SQLite atau PostgreSQL 12+
- **Composer**: PHP Dependency Manager
- **Node.js & npm**: Asset Bundler (Vite)
- **Git**: Version Control System

---

## Panduan Instalasi

### 1. Clone Repository
```bash
git clone https://github.com/Feyr9z/inventory_system.git
cd inventory_system
```

### 2. Install Dependency PHP
```bash
composer install
```

### 3. Konfigurasi Environment
Salin file environment template:
```bash
cp .env.example .env
```
Atau di Windows (PowerShell):
```powershell
Copy-Item .env.example -Destination .env
```

Atur variabel environment di file `.env` sesuai konfigurasi sistem Anda.

### 4. Generate Application Key
```bash
php artisan key:generate
```

### 5. Migration & Database Seeding
Jalankan migrasi tabel dan pengisian data sampel awal:
```bash
php artisan migrate --seed
```

### 6. Install Node Dependency & Compile Assets
```bash
npm install
npm run build
```

---

## Menjalankan Aplikasi

### Development Server
Jalankan Laravel development server:
```bash
php artisan serve
```
Buka browser dan akses alamat: `http://127.0.0.1:8000`

---

## Kredensial Pengujian (Seeding Accounts)

Gunakan akun sampel berikut untuk menguji masing-masing peran:

| Role | Email | Password |
|---|---|---|
| **Admin** | `admin@inventory.test` | `password` |
| **Staff** | `staff@inventory.test` | `password` |
| **Management** | `management@inventory.test` | `password` |

---

## Perintah Maintenance & Cache

Jika melakukan pembaruan kode atau meredesign aset:
```bash
# Kompilasi Ulang Aset Frontend
npm run build

# Membersihkan Cache Laravel
php artisan view:clear
php artisan config:clear
php artisan cache:clear
```

---

## Lisensi & Hak Cipta

Sistem Manajemen Inventoris ini dikembangkan untuk PT Atha Anakhatulistiwa. Hak Cipta © 2026.
