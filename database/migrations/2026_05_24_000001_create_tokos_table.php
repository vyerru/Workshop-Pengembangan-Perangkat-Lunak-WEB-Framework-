<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tokos', function (Blueprint $table) {
            $table->id();
            $table->string('nama_toko', 100);
            $table->text('alamat')->nullable();
            $table->decimal('latitude', 10, 7)->nullable();
            $table->decimal('longitude', 10, 7)->nullable();
            $table->float('accuracy')->nullable();
            $table->string('barcode_token', 64)->unique();
            $table->foreignId('created_by')->constrained('users');
            $table->timestamps();
            $table->index('barcode_token');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tokos');
    }
};
