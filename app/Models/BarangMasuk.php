<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BarangMasuk extends Model
{
    use HasFactory;

    protected $table = 'barang_masuk';

    protected $fillable = [
        'barang_id',
        'user_id',
        'tanggal',
        'jumlah',
        'sisa_jumlah',
        'sumber',
    ];

    protected $casts = [
        'jumlah'      => 'integer',
        'sisa_jumlah' => 'integer',
        'tanggal'     => 'date',
    ];

    public function barang()
    {
        return $this->belongsTo(Barang::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function keluarDetails()
    {
        return $this->hasMany(BarangKeluarDetail::class, 'barang_masuk_id');
    }
}
