# API Usage Examples

## Overview

Endpoint reference untuk menggunakan Inventory Management System

---

## Dashboard

### Get Dashboard Stats
```
GET /inventory
Response:
- total: int (jumlah barang)
- stok: int (total stok semua barang)
```

**Example:**
```bash
curl http://localhost:8000/inventory
```

---

## Barang Management

### List All Barang
```
GET /inventory/barang
Response:
- barang: Collection of Barang
- barang[].id, nama_barang, kategori.nama_kategori, stok, stok_minimum, lokasi
```

**Example:**
```bash
curl http://localhost:8000/inventory/barang
```

### Show Create Form
```
GET /inventory/barang/create
Response: HTML Form dengan kategori options
```

### Create Barang
```
POST /inventory/barang
Content-Type: application/x-www-form-urlencoded

Parameters:
- nama_barang (required): string, max 255
- kategori_id (required): exists in kategori table
- stok_minimum (required): integer, >= 0
- lokasi (optional): string, max 255

Response: Redirect to /inventory/barang with success message
```

**Example:**
```bash
curl -X POST http://localhost:8000/inventory/barang \
  -d "nama_barang=Laptop&kategori_id=1&stok_minimum=2&lokasi=Gudang%20A"
```

### Show Edit Form
```
GET /inventory/barang/{id}
Response: HTML Form pre-filled dengan barang data
```

### Update Barang
```
PUT /inventory/barang/{id}
Content-Type: application/x-www-form-urlencoded
Method: POST with _method=PUT

Same parameters sebagai Create
Response: Redirect with success message
```

### Delete Barang
```
DELETE /inventory/barang/{id}
Method: POST with _method=DELETE

Response: Redirect with success message
```

---

## Barang Masuk (Incoming)

### Show Form
```
GET /inventory/transaksi/masuk
Response: HTML Form dengan barang dropdown
```

### Create Masuk Transaction
```
POST /inventory/transaksi/masuk
Content-Type: application/x-www-form-urlencoded

Parameters:
- barang_id (required): exists in barang table
- jumlah (required): integer, > 0
- tanggal (required): date (YYYY-MM-DD)
- sumber (required): string, max 255 (misal: Supplier A)

Side Effects:
- barang.stok += jumlah
- Create entry di barang_masuk table

Response: Redirect with success message
```

**Example:**
```bash
curl -X POST http://localhost:8000/inventory/transaksi/masuk \
  -d "barang_id=1&jumlah=10&tanggal=2026-04-19&sumber=Supplier%20A"
```

**Response Message:**
```
Barang masuk berhasil dicatat
```

---

## Barang Keluar (Outgoing)

### Show Form
```
GET /inventory/transaksi/keluar
Response: HTML Form dengan barang dropdown
```

### Create Keluar Transaction
```
POST /inventory/transaksi/keluar
Content-Type: application/x-www-form-urlencoded

Parameters:
- barang_id (required): exists in barang table
- jumlah (required): integer, > 0, <= barang.stok
- tanggal (required): date (YYYY-MM-DD)
- tujuan (required): string, max 255 (misal: Dept A)

Validation:
- if jumlah > barang.stok:
    ERROR: "Stok tidak cukup. Stok tersedia: X"

Side Effects (if valid):
- barang.stok -= jumlah
- Create entry di barang_keluar table

Response: Redirect with success/error message
```

**Example (Success):**
```bash
curl -X POST http://localhost:8000/inventory/transaksi/keluar \
  -d "barang_id=1&jumlah=3&tanggal=2026-04-19&tujuan=Dept%20A"
```

**Response Message:**
```
Barang keluar berhasil dicatat
```

**Example (Insufficient Stock):**
```bash
curl -X POST http://localhost:8000/inventory/transaksi/keluar \
  -d "barang_id=1&jumlah=999&tanggal=2026-04-19&tujuan=Dept%20A"
```

**Error Response:**
```
Stok tidak cukup. Stok tersedia: X
```

---

## Stock Opname (Verification)

### Show Form
```
GET /inventory/transaksi/opname
Response: HTML Form dengan:
- barang dropdown
- stok_sistem display (read-only)
- stok_fisik input
- selisih display (auto-calculated)
- tanggal input
```

### Create Opname
```
POST /inventory/transaksi/opname
Content-Type: application/x-www-form-urlencoded

Parameters:
- barang_id (required): exists in barang table
- stok_fisik (required): integer, >= 0
- tanggal (required): date (YYYY-MM-DD)

Calculation:
- selisih = stok_fisik - barang.stok

Side Effects:
- barang.stok = stok_fisik (UPDATE to physical count)
- Create entry di stock_opname table dengan selisih

Response: Redirect with success message
```

**Example:**
```bash
curl -X POST http://localhost:8000/inventory/transaksi/opname \
  -d "barang_id=1&stok_fisik=15&tanggal=2026-04-19"
```

**Scenario:**
```
Before Opname:
- barang.stok = 20 (sistem)

POST:
- stok_fisik = 15 (physical count)

Calculation:
- selisih = 15 - 20 = -5

After Opname:
- barang.stok = 15 (updated)
- stock_opname entry created dengan selisih = -5
```

---

## Error Responses

### Validation Error (422)
```json
{
  "message": "Validation failed",
  "errors": {
    "field_name": ["Error message"]
  }
}
```

Example:
```
nama_barang: ["The field is required"]
jumlah: ["The field must be at least 1"]
```

### Not Found (404)
```
Barang atau resource tidak ditemukan
Redirect to previous page
```

### Insufficient Stock
```
Stok tidak cukup. Stok tersedia: X
Redirect with error message
```

---

## Success Responses

### Redirect with Message
```
Location: /inventory/...
Session Flash:
- success: "Data berhasil disimpan"
- error: "Stok tidak cukup"
```

---

## Common Patterns

### Create Masuk & Verify Stok
```bash
# 1. Create masuk
curl -X POST http://localhost:8000/inventory/transaksi/masuk \
  -d "barang_id=1&jumlah=5&tanggal=2026-04-19&sumber=Supplier"

# 2. Check list
curl http://localhost:8000/inventory/barang | grep stok
# Expected: stok increased by 5
```

### Create Keluar & Handle Error
```bash
# 1. Try keluar dengan stok > available
curl -X POST http://localhost:8000/inventory/transaksi/keluar \
  -d "barang_id=1&jumlah=999&tanggal=2026-04-19&tujuan=Dept"

# Response: Error message dengan available stok
# Status: Redirect dengan validation error
```

### Opname Verification
```bash
# 1. Physical count shows mismatch
curl -X POST http://localhost:8000/inventory/transaksi/opname \
  -d "barang_id=1&stok_fisik=12&tanggal=2026-04-19"

# Result: stok updated to 12, selisih recorded
```

---

## CSRF Protection

All POST, PUT, DELETE requests require CSRF token:

```html
<form method="POST" action="/inventory/transaksi/masuk">
  @csrf
  <!-- fields -->
</form>
```

Or in headers:
```
X-CSRF-TOKEN: <token>
```

---

## Status Codes

| Code | Meaning |
|------|---------|
| 200 | GET successful |
| 302 | Redirect (POST/PUT/DELETE) |
| 422 | Validation error |
| 404 | Not found |

---

## Data Types

| Field | Type | Example |
|-------|------|---------|
| id | int | 1 |
| nama | string | "Laptop Dell" |
| stok | int | 5 |
| tanggal | date | "2026-04-19" |
| jumlah | int | 10 |
| selisih | int | -5 |

---

## Rate Limiting

No rate limiting implemented. For production, add:
```php
Route::middleware('throttle:60,1')->group(function () {
    // routes
});
```

---

## Pagination

Future enhancement:
```
GET /inventory/barang?page=1&per_page=15
```

Currently: No pagination

---

## Testing with cURL

```bash
# Dashboard
curl http://localhost:8000/inventory

# List barang
curl http://localhost:8000/inventory/barang

# Create barang (POST)
curl -X POST http://localhost:8000/inventory/barang \
  -d "nama_barang=Test&kategori_id=1&stok_minimum=0"

# Barang masuk
curl -X POST http://localhost:8000/inventory/transaksi/masuk \
  -d "barang_id=1&jumlah=5&tanggal=$(date +%Y-%m-%d)&sumber=Test"

# Barang keluar
curl -X POST http://localhost:8000/inventory/transaksi/keluar \
  -d "barang_id=1&jumlah=2&tanggal=$(date +%Y-%m-%d)&tujuan=Test"

# Opname
curl -X POST http://localhost:8000/inventory/transaksi/opname \
  -d "barang_id=1&stok_fisik=8&tanggal=$(date +%Y-%m-%d)"
```

---

## Mobile/API Development

For building mobile apps using this system:

1. Wrap endpoints dengan API responses
2. Return JSON instead of HTML
3. Implement token-based authentication
4. Add API rate limiting
5. Version your API (/api/v1/...)

See DOCUMENTATION.md untuk lebih detail

---

**Last Updated**: 2026-04-19
**Version**: 1.0
