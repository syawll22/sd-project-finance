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
        $table->string('nama_rekening');
        $table->decimal('saldo', 15, 2)->default(0)->nullable(); // Saldo awal opsional (default 0)
        $table->timestamps();
    });
    }

    public function down(): void
    {
        Schema::dropIfExists('rekenings');
    }
};