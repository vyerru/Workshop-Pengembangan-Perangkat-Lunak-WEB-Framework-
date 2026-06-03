<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pesanans', function (Blueprint $table) {
            $table->integer('nomor_antrian')->nullable()->after('transaction_id');
            $table->string('status_antrian')->default('pending')->after('nomor_antrian');
            $table->index(['vendor_id', 'nomor_antrian']);
        });
    }

    public function down(): void
    {
        Schema::table('pesanans', function (Blueprint $table) {
            $table->dropIndex(['vendor_id', 'nomor_antrian']);
            $table->dropColumn('status_antrian');
            $table->dropColumn('nomor_antrian');
        });
    }
};
