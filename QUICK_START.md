# 🚀 Quick Start Guide

## System Overview

Sistem inventaris berbasis Laravel 11 + PostgreSQL dengan fitur:
- Dashboard dengan statistik real-time
- CRUD Barang dengan kategori
- Transaksi Barang Masuk/Keluar/Opname
- Real-time stok management

**Status**: ✅ PRODUCTION READY

---

## ⚡ Quick Setup (5 menit)

```bash
cd C:\myProject\inventory

# 1. Install dependencies (jika belum)
composer install

# 2. Setup environment
cp .env.example .env
php artisan key:generate

# 3. Configure database di .env
# DB_CONNECTION=pgsql
# DB_HOST=127.0.0.1
# DB_DATABASE=inventory_db
# DB_USERNAME=rafly
# DB_PASSWORD=rafly

# 4. Run migrations & seeders
php artisan migrate --seed

# 5. Start server
php artisan serve

# Access: http://localhost:8000/inventory
```

---

## 👤 Test Users

```
Admin:       admin@inventory.test / password
Staff:       staff@inventory.test / password
Management:  management@inventory.test / password
```

---

## 📍 Main Routes

| Route | Method | Description |
|-------|--------|-------------|
| `/inventory` | GET | Dashboard |
| `/inventory/barang` | GET | List Barang |
| `/inventory/barang/create` | GET | Tambah Barang Form |
| `/inventory/barang` | POST | Store Barang |
| `/inventory/barang/{id}/edit` | GET | Edit Form |
| `/inventory/barang/{id}` | PUT | Update Barang |
| `/inventory/barang/{id}` | DELETE | Delete Barang |
| `/inventory/transaksi/masuk` | GET | Barang Masuk Form |
| `/inventory/transaksi/masuk` | POST | Store Masuk |
| `/inventory/transaksi/keluar` | GET | Barang Keluar Form |
| `/inventory/transaksi/keluar` | POST | Store Keluar |
| `/inventory/transaksi/opname` | GET | Opname Form |
| `/inventory/transaksi/opname` | POST | Store Opname |

---

## 📊 Dashboard

- Total Barang Count
- Total Stok Sum
- Quick action buttons

---

## 📦 Barang Management

### List View
- Tabel dengan nama, kategori, stok, lokasi
- Stok minimum indicator
- Edit & Delete buttons
- ⚠️ Visual warning jika stok < minimum

### Create/Edit
- Form validation
- Required fields: nama, kategori, stok_minimum
- Optional: lokasi
- Error messages jelas

---

## 💼 Transaksi

### Barang Masuk
- Select barang dari dropdown
- Input jumlah & sumber
- Stok auto-increment
- Success message

### Barang Keluar
- Select barang
- Input jumlah & tujuan
- **Validasi**: Error jika stok insufficient
- Stok auto-decrement

### Stock Opname
- Select barang
- Input stok fisik & tanggal
- Real-time selisih calculation
- Stok update ke nilai fisik

---

## 🎯 Business Logic

```
Barang Masuk:
  stok += jumlah

Barang Keluar:
  if jumlah > stok:
    ERROR: Stok tidak cukup
  else:
    stok -= jumlah

Stock Opname:
  selisih = stok_fisik - stok_sistem
  stok = stok_fisik
```

---

## 📚 Database Tables

| Table | Purpose |
|-------|---------|
| `users` | User accounts dengan roles |
| `kategori` | Product categories |
| `barang` | Master data barang |
| `barang_masuk` | Incoming transactions |
| `barang_keluar` | Outgoing transactions |
| `stock_opname` | Physical verification |
| `log_aktivitas` | Activity audit trail |

---

## 🛠️ Common Commands

```bash
# Start development server
php artisan serve

# Run migrations
php artisan migrate

# Run seeders
php artisan db:seed

# Fresh database
php artisan migrate:refresh --seed

# Clear caches
php artisan cache:clear
php artisan config:clear
php artisan view:clear

# Check routes
php artisan route:list

# Run tests
php artisan test
```

---

## 🔍 Troubleshooting

### Server tidak jalan
```bash
php artisan serve --port=8000
```

### Database error
```bash
# Check .env configuration
# Make sure PostgreSQL is running
php artisan migrate:refresh --seed
```

### Cache issues
```bash
php artisan cache:clear
php artisan config:clear
```

### Page not found
```bash
php artisan route:clear
```

---

## 📖 Documentation Files

- **DOCUMENTATION.md** - Complete guide with recommendations
- **IMPLEMENTATION_GUIDE.php** - Code examples for features
- **DATABASE_OPTIMIZATIONS.php** - Advanced queries & migrations
- **COMPLETION_SUMMARY.md** - Project status & checklist

---

## 🚀 Next Steps

1. **Test the system** dengan sample data
2. **Review recommendations** di DOCUMENTATION.md
3. **Implement priority features** dari guide
4. **Add role-based access** jika needed
5. **Deploy ke production** dengan security setup

---

## 📋 File Structure

```
app/Http/Controllers/ → Business logic
app/Models/          → Database models
resources/views/     → Blade templates
routes/web.php       → URL routing
database/            → Migrations & seeders
```

---

## ✨ Key Features

✅ Real-time stok management
✅ Transaction logging
✅ Stock validation
✅ User roles support
✅ Responsive UI
✅ Error handling
✅ Data persistence

---

## 🎓 Learning Path

1. **Explore** dashboard dan barang list
2. **Test** transaksi masuk (stok naik)
3. **Test** transaksi keluar (stok turun)
4. **Verify** stock opname calculation
5. **Review** code di controllers
6. **Implement** recommendations dari docs

---

## 💡 Tips

- Database seeder sudah otomatis run
- Test users tersedia untuk login
- Sample barang sudah ada
- Validation error messages jelas
- Input old() preserved on error
- Flash messages show success/error
- Navigation menu di header

---

## 📞 Support

- Check DOCUMENTATION.md untuk details
- Review code comments
- Check DATABASE_OPTIMIZATIONS untuk advanced
- See IMPLEMENTATION_GUIDE untuk code examples

---

## ✅ Checklist

- [x] All endpoints working (tested HTTP 200)
- [x] Database migrations complete
- [x] Seeders populated
- [x] UI responsive
- [x] Validation working
- [x] Error handling implemented
- [x] Business logic correct

**Status: Ready to Use** ✅

Selamat menggunakan Inventory Management System!
