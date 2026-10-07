<?php

namespace App\Http\Controllers;

use App\Models\Kategori;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;

class KategoriController extends Controller
{
    public function store(Request $request)
    {
        // AUTO-FIX DATABASE RAILWAY (Jalan otomatis kalau kolom belum ada)
        if (!Schema::hasColumn('kategoris', 'no_akun')) {
            Schema::table('kategoris', function (Blueprint $table) {
                if (Schema::hasColumn('kategoris', 'kode_kategori')) {
                    $table->renameColumn('kode_kategori', 'no_akun');
                } elseif (Schema::hasColumn('kategoris', 'kode')) {
                    $table->renameColumn('kode', 'no_akun');
                } else {
                    $table->string('no_akun')->nullable()->after('id');
                }
            });
        }

        if (!Schema::hasColumn('kategoris', 'nama_akun')) {
            Schema::table('kategoris', function (Blueprint $table) {
                if (Schema::hasColumn('kategoris', 'nama_kategori')) {
                    $table->renameColumn('nama_kategori', 'nama_akun');
                } else {
                    $table->string('nama_akun')->after('no_akun');
                }
            });
        }

        // KODE UTAMA DARI CONTROLLER KAMU
        $request->validate([
            'no_akun'   => 'required',
            'nama_akun' => 'required',
        ]);

        Kategori::create([
            'no_akun'   => $request->no_akun,
            'nama_akun' => $request->nama_akun,
        ]);

        return redirect()->back()->with('success', 'Kategori berhasil ditambahkan!');
    }
}