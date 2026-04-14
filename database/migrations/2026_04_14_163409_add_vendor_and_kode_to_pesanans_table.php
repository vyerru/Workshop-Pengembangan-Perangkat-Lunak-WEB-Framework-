<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::table('pesanans', function (Blueprint $table) {
            // nullable() ditambahkan agar migrasi tidak error jika ada data lama
            $table->foreignId('vendor_id')->after('id')->nullable()->constrained('vendors')->cascadeOnDelete();
            $table->string('kode_pesanan')->after('vendor_id')->unique()->nullable();
        });
    }

    public function down(): void {
        Schema::table('pesanans', function (Blueprint $table) {
            $table->dropForeign(['vendor_id']);
            $table->dropColumn(['vendor_id', 'kode_pesanan']);
        });
    }
};