<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('kategoris', function (Blueprint $table) {
            // Rename kolom jika masih pakai nama lama
            if (Schema::hasColumn('kategoris', 'kode_kategori') && !Schema::hasColumn('kategoris', 'no_akun')) {
                $table->renameColumn('kode_kategori', 'no_akun');
            } elseif (Schema::hasColumn('kategoris', 'kode') && !Schema::hasColumn('kategoris', 'no_akun')) {
                $table->renameColumn('kode', 'no_akun');
            } elseif (!Schema::hasColumn('kategoris', 'no_akun')) {
                $table->string('no_akun')->nullable()->after('id');
            }

            if (Schema::hasColumn('kategoris', 'nama_kategori') && !Schema::hasColumn('kategoris', 'nama_akun')) {
                $table->renameColumn('nama_kategori', 'nama_akun');
            } elseif (!Schema::hasColumn('kategoris', 'nama_akun')) {
                $table->string('nama_akun')->after('no_akun');
            }
        });
    }

    public function down(): void
    {
        //
    }
};