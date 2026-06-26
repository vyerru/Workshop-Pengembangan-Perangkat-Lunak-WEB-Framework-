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
        Schema::table('pesanans', function (Blueprint $table) {
            $table->index(['vendor_id', 'status_bayar', 'status_antrian', 'nomor_antrian'], 'idx_sse_queue');
        });
    }

    public function down(): void
    {
        Schema::table('pesanans', function (Blueprint $table) {
            $table->dropIndex('idx_sse_queue');
        });
    }
};
