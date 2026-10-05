<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('rekenings', function (Blueprint $table) {
            $table->id();
            $table->string('nama_rekening'); // contoh: Bank BCA, Kas Tunai
            $table->string('nomor_rekening')->nullable(); // contoh: 123456789
            $table->string('atas_nama')->nullable(); // contoh: PT S&D Finance
            $table->decimal('saldo', 15, 2)->default(0); // saldo saat ini
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('rekenings');
    }
};