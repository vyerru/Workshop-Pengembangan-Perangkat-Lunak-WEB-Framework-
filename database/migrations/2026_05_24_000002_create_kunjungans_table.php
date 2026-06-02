<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('kunjungans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('toko_id')->constrained('tokos')->cascadeOnDelete();
            $table->foreignId('sales_id')->constrained('users');
            $table->decimal('latitude_sales', 10, 7);
            $table->decimal('longitude_sales', 10, 7);
            $table->float('accuracy_sales');
            $table->decimal('latitude_toko', 10, 7);
            $table->decimal('longitude_toko', 10, 7);
            $table->float('accuracy_toko')->nullable();
            $table->float('jarak_terhitung');
            $table->float('threshold_efektif');
            $table->enum('status', ['diterima', 'ditolak'])->default('ditolak');
            $table->timestamp('waktu_kunjungan')->useCurrent();
            $table->timestamps();
            $table->index(['sales_id', 'waktu_kunjungan']);
            $table->index('toko_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('kunjungans');
    }
};
