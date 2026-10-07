<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
       Schema::create('mutasis', function (Blueprint $table) {
    $table->id();
    $table->foreignId('rekening_id')->constrained()->onDelete('cascade');
    $table->date('tanggal');
    $table->string('no_jurnal')->nullable();
    $table->string('nama')->nullable();
    $table->string('sumber')->nullable();
    $table->text('keterangan')->nullable();
    $table->string('no_coa')->nullable();
    $table->string('nama_coa')->nullable();
    $table->decimal('debet', 15, 2)->default(0);
    $table->decimal('kredit', 15, 2)->default(0);
    $table->timestamps();
});
    }

    public function down(): void
    {
        Schema::dropIfExists('mutasis');
    }
};