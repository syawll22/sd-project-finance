<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('kategoris', function (Blueprint $table) {
            // Ubah nama kolom lama jika masih bernama 'kode' atau 'kode_kategori'
            if (Schema::hasColumn('kategoris', 'kode_kategori')) {
                $table->renameColumn('kode_kategori', 'no_akun');
            } elseif (Schema::hasColumn('kategoris', 'kode')) {
                $table->renameColumn('kode', 'no_akun');
            } elseif (!Schema::hasColumn('kategoris', 'no_akun')) {
                $table->string('no_akun')->nullable()->after('id');
            }

            if (Schema::hasColumn('kategoris', 'nama_kategori')) {
                $table->renameColumn('nama_kategori', 'nama_akun');
            } elseif (!Schema::hasColumn('kategoris', 'nama_akun')) {
                $table->string('nama_akun')->after('no_akun');
            }
        });
    }

    public function down(): void
    {
        Schema::table('kategoris', function (Blueprint $table) {
            //
        });
    }
};