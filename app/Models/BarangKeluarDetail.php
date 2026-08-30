<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BarangKeluarDetail extends Model
{
    protected $table = 'barang_keluar_detail';

    protected $fillable = [
        'barang_keluar_id',
        'barang_masuk_id',
        'jumlah_diambil',
    ];

    protected $casts = [
        'jumlah_diambil' => 'integer',
    ];

    public function barangKeluar()
    {
        return $this->belongsTo(BarangKeluar::class, 'barang_keluar_id');
    }

    public function barangMasuk()
    {
        return $this->belongsTo(BarangMasuk::class, 'barang_masuk_id');
    }
}
