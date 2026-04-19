<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LogAktivitas extends Model
{
    protected $table = "log_aktivitas";

    public $timestamps = false;

    protected $fillable = ["user_id", "aktivitas", "waktu"];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
