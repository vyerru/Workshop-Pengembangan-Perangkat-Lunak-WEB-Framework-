<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('absensis', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('pertemuan_id')->constrained('pertemuans')->cascadeOnDelete();
            $table->string('nfc_uid');
            $table->timestamp('waktu_hadir')->useCurrent();
            $table->timestamps();

            $table->unique(['user_id', 'pertemuan_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('absensis');
    }
};
