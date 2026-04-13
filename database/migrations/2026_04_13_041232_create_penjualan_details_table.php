<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('penjualan_details', function (Blueprint $table) {
            $table->id('idpenjualan_detail'); // int4 NOT NULL [PK]
            $table->unsignedBigInteger('id_penjualan'); // int4 NOT NULL [FK]
            // Menyesuaikan id_barang dari modul: varchar(8)
            $table->string('id_barang', 8); // varchar(8) NOT NULL [FK]
            $table->smallInteger('jumlah'); // int2 NOT NULL
            $table->integer('subtotal'); // int4 NOT NULL
            $table->timestamps();

            $table->foreign('id_penjualan')->references('id_penjualan')->on('penjualans')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('penjualan_details');
    }
};
