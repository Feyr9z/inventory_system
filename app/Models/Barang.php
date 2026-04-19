<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Barang extends Model
{
    protected $table = "barang";

    protected $fillable = [
        "nama_barang",
        "stok",
        "lokasi",
        "stok_minimum",
        "kategori_id",
    ];

    public function kategori()
    {
        return $this->belongsTo(Kategori::class);
    }

    public function barangMasuk()
    {
        return $this->hasMany(BarangMasuk::class);
    }

    public function barangKeluar()
    {
        return $this->hasMany(BarangKeluar::class);
    }

    public function stockOpname()
    {
        return $this->hasMany(StockOpname::class);
    }
}
