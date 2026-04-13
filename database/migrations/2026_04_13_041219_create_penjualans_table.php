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
        Schema::create('penjualans', function (Blueprint $table) {
            $table->id('id_penjualan'); // int4 NOT NULL [PK]
            $table->timestamp('timestamp')->useCurrent(); // timestamp NOT NULL
            $table->integer('total'); // int4 NOT NULL
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *//*  */
    public function down(): void
    {
        Schema::dropIfExists('penjualans');
    }
};
