<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StockOpname extends Model
{
    protected $table = "stock_opname";

    protected $fillable = ["barang_id", "stok_fisik", "selisih", "tanggal"];

    public function barang()
    {
        return $this->belongsTo(Barang::class);
    }
}
