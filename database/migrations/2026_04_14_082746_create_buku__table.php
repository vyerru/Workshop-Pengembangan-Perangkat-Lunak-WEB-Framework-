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
        Schema::create('buku', function (Blueprint $table) {
            // Menggunakan tipe data INT sebagai Primary Key
            $table->integer('idbuku')->autoIncrement();
            $table->string('kode', 20);
            $table->string('judul', 500);
            $table->string('pengarang', 200);
            
            // Kolom untuk Foreign Key (harus bertipe sama dengan Primary Key di tabel referensi, yaitu INT)
            $table->integer('idkategori');
            
            // Definisi relasi Foreign Key ke tabel kategori
            $table->foreign('idkategori')
                  ->references('idkategori')
                  ->on('kategori')
                  ->onDelete('cascade') // Opsional: Hapus buku jika kategori dihapus
                  ->onUpdate('cascade');
                  
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('buku');
    }
};