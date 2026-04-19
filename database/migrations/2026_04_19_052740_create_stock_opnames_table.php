<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create("stock_opname", function (Blueprint $table) {
            $table->id();

            $table
                ->foreignId("barang_id")
                ->constrained("barang")
                ->cascadeOnDelete();

            $table->integer("stok_fisik")->unsigned();
            $table->integer("selisih");
            $table->date("tanggal");

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists("stock_opname");
    }
};
