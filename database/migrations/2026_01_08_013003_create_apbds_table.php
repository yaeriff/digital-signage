<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('apbds', function (Blueprint $table) {
            $table->id();
            $table->string('perangkat_daerah');
            
            // BigInteger untuk Uang (Triliunan)
            $table->bigInteger('anggaran_pendapatan')->default(0);
            $table->bigInteger('realisasi_pendapatan_rp')->default(0);
            $table->double('realisasi_pendapatan')->default(0);
            $table->bigInteger('anggaran_belanja')->default(0);
            $table->bigInteger('realisasi_belanja_rp')->default(0);
            $table->double('realisasi_belanja')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('apbds');
    }
};