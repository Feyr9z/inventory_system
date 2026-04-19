<?php

namespace App\Http\Controllers;

use App\Models\Barang;

class DashboardController extends Controller
{
    public function index()
    {
        $total = Barang::count();
        $stok = Barang::sum("stok");

        return view("dashboard", compact("total", "stok"));
    }
}
