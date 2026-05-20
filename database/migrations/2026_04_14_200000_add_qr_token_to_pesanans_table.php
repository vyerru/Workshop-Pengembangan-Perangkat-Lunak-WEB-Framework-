<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::table('pesanans', function (Blueprint $table) {
            $table->string('qr_token', 40)->after('kode_pesanan')->unique()->nullable();
        });
    }

    public function down(): void {
        Schema::table('pesanans', function (Blueprint $table) {
            $table->dropColumn('qr_token');
        });
    }
};
