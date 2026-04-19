<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create("barang_masuk", function (Blueprint $table) {
            $table->id();

            $table
                ->foreignId("barang_id")
                ->constrained("barang")
                ->cascadeOnDelete();

            $table->date("tanggal");
            $table->integer("jumlah")->unsigned();
            $table->string("sumber")->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists("barang_masuk");
    }
};
