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
        Schema::create('agendas', function (Blueprint $table) {
            $table->id();
            $table->time('start_time'); // Jam Mulai
            $table->time('end_time');   // Jam Selesai
            $table->string('title');    // Nama Agenda
            $table->text('description')->nullable(); // Deskripsi (bisa dikosongkan)
            $table->string('location'); // Lokasi
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('agendas');
    }
};
