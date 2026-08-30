# PT Atha Anakhatulistiwa - Inventory Management System

Sistem Informasi Manajemen Inventoris berbasis web yang dirancang untuk mengelola persediaan barang fisik, alokasi pengeluaran metode **FIFO (First In First Out)**, audit stock opname, dokumen bukti transaksi logistik non-finansial, serta monitoring transaksi terstruktur untuk PT Atha Anakhatulistiwa.

---

## Fitur Utama

- **Metode Persediaan FIFO (First In First Out)**:
  - Setiap barang masuk membentuk lot persediaan mandiri dengan pelacakan `sisa_jumlah`.
  - Pengeluaran barang otomatis mengonsumsi lot tertua (`ORDER BY tanggal ASC, id ASC`).
  - Pencatatan pemakaian lot pada tabel relasi `barang_keluar_detail` untuk audit trail penuh.
  - Penanganan race condition dengan database transaction dan pessimistic row-level locking (`lockForUpdate()`).
- **Dashboard Multirole**:
  - Ringkasan analitik real-time: total barang, total unit, peringatan stok minimum, mutasi masuk/keluar/opname bulanan disesuaikan menurut peran pengguna.
- **Enhanced Filtering, Sorting & Default Terbaru**:
  - Seluruh tabel (Master Barang, Laporan Transaksi, Posisi Stok, History Opname, Log Aktivitas, User) secara default menampilkan data **terbaru di atas**.
  - Form pencarian kata kunci multi-kolom, filter kategori, filter status stok (*Normal/Kurang*), filter status selisih audit (*Surplus/Defisit/Sesuai*), dan sorting multi-kriteria dengan tombol reset filter dinamis.
- **Dokumen Bukti Transaksi & Struk Logistik Non-Finansial (Print-Ready)**:
  - **Surat Bukti Penerimaan Barang** (`IN-YYYYMMDD-XXXX`): Nomor referensi dokumen resmi, petugas penerima, vendor asal, kuantitas unit, dan identitas Lot FIFO.
  - **Surat Jalan / Bukti Pengeluaran Barang** (`OUT-YYYYMMDD-XXXX`): Nomor referensi dokumen, penerima/tujuan, petugas pengeluar, dan **tabel rincian alokasi lot FIFO yang dikonsumsi**.
  - **Berita Acara Stock Opname Fisik** (`OPN-YYYYMMDD-XXXX`): Dokumen rekonsiliasi audit fisik vs sistem, catatan selisih unit, dan penjelasan otomatis tindakan rekonsiliasi lot.
  - Desain modal slip dokumen logistik profesional dengan optimasi `@media print` untuk pencetakan dokumen fisik.
- **Fitur "Transaksi Saya" Khusus Staff**:
  - Menu navigasi khusus staf gudang untuk melihat riwayat aktivitas operasional personal per user (`user_id = Auth::id()`).
  - Dilengkapi statistik personal (Total Transaksi, Total Masuk, Total Keluar), filter interaktif, dan akses cepat cetak bukti transaksi personal.
- **Manajemen Master Data & Kategori**:
  - Pengelolaan data barang, detail lot pool FIFO aktif, riwayat konsumsi, dan kategori dengan pembagian wewenang ketat.
- **Transaksi Stok & Audit**:
  - **Barang Masuk**: Pencatatan unit baru, inisialisasi lot sisa, dan atribusi pengguna.
  - **Barang Keluar**: Alokasi otomatis FIFO dengan validasi ketersediaan lot dan stok.
  - **Stock Opname FIFO**: Rekonsiliasi fisik yang menyelaraskan stok agregat sekaligus sisa lot (surplus membuat lot baru, defisit memotong lot tertua).
- **Laporan & Eksportir Data**:
  - Laporan Transaksi dengan atribusi petugas input, rincian alokasi lot FIFO, dan ekspor CSV.
  - Laporan Stok dengan pemfilteran berbasis SQL query dan ekspor CSV.
  - History Opname dengan audit trail selisih stok (Surplus, Defisit, Sesuai).
- **Log Aktivitas & Traceability**:
  - Pencatatan audit trail otomatis via Eloquent Observer tanpa duplikasi log.
- **Role-Based Access Control (RBAC)**:
  - Pembagian hak akses terpusat berbasis PHP Enum: **Admin**, **Staff Gudang**, **Kepala Gudang**, dan **Manajemen**.

---

## Peran Pengguna & Hak Akses

| Fitur / Modul | Admin | Kepala Gudang | Staff Gudang | Manajemen |
|---|:---:|:---:|:---:|:---:|
| **Dashboard** | Full | Full Pengawasan | Operasional | Monitoring |
| **Daftar & Detail Lot FIFO** | Full | Full | Lihat Saja | Lihat Saja |
| **Tambah / Edit Barang** | Ya | Ya | Tidak | Tidak |
| **Hapus Barang** | Ya | Tidak | Tidak | Tidak |
| **Kelola Kategori** | Ya | Ya | Tidak | Tidak |
| **Kelola Pengguna (User)** | Ya | Tidak | Tidak | Tidak |
| **Input Barang Masuk** | Ya | Tidak | Ya | Tidak |
| **Input Barang Keluar (FIFO)** | Ya | Tidak | Ya | Tidak |
| **Transaksi Saya (Personal)** | Ya | Tidak | Ya | Tidak |
| **Input Stock Opname** | Ya | Ya | Tidak | Tidak |
| **Riwayat & Audit Opname** | Ya | Ya | Tidak | Ya |
| **Laporan Transaksi & Stok** | Ya | Ya | Tidak | Ya |
| **Lihat & Cetak Bukti/Struk** | Ya | Ya | Ya (Personal) | Ya |
| **Ekspor CSV** | Ya | Ya | Tidak | Ya |
| **Log Aktivitas** | Ya | Ya | Tidak | Ya |

---

## Persyaratan Sistem (Prerequisites)

- **PHP**: 8.2 atau lebih baru (dengan ekstensi `pdo_pgsql` / `pdo_sqlite`)
- **Database**: PostgreSQL 12+ (atau SQLite untuk lokal/testing)
- **Composer**: PHP Dependency Manager
- **Node.js & npm**: Asset Bundler (Vite + Tailwind CSS v4)
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
Atur kredensial database PostgreSQL di file `.env`:
```env
DB_CONNECTION=pgsql
DB_HOST=127.0.0.1
DB_PORT=5432
DB_DATABASE=inventory_db
DB_USERNAME=postgres
DB_PASSWORD=your_password
```

### 4. Generate Application Key
```bash
php artisan key:generate
```

### 5. Migration & Database Seeding
Jalankan migrasi tabel dan pengisian data master awal beserta lot FIFO:
```bash
php artisan migrate:fresh --seed
```

### 6. Install Node Dependency & Compile Assets
```bash
npm install
npm run build
```

---

## Menjalankan Aplikasi & Automated Tests

### Development Server
```bash
php artisan serve
```
Akses melalui browser di: `http://127.0.0.1:8000`

### Menjalankan Automated Test Suite
```bash
php artisan test
```
*Status: 30 Tests, 147 Assertions — 100% Passed.*

---

## Kredensial Pengujian (Seeding Accounts)

| Role | Email | Password |
|---|---|---|
| **Admin** | `admin@inventory.test` | `password` |
| **Kepala Gudang** | `kepala@inventory.test` | `password` |
| **Staff** | `staff@inventory.test` | `password` |
| **Management** | `management@inventory.test` | `password` |

---

## Lisensi & Hak Cipta

Sistem Manajemen Inventoris ini dikembangkan untuk PT Atha Anakhatulistiwa. Hak Cipta © 2026.
