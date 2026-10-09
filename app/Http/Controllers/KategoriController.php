<?php

namespace App\Http\Controllers;

use App\Models\Kategori;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;

class KategoriController extends Controller
{
    public function index()
    {
        $kategoris = Kategori::all();
        return view('kategori.index', compact('kategoris'));
    }

    public function store(Request $request)
    {
        // AUTO-FIX DATABASE RAILWAY (Otomatis nambah/rename kolom kalau belum ada)
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

        // VALIDASI
        $request->validate([
            'no_akun'   => 'required',
            'nama_akun' => 'required',
        ]);

        // TAMBAHKAN TRY-CATCH DI SINI
        try {
            Kategori::create([
                'no_akun'   => $request->no_akun,
                'nama_akun' => $request->nama_akun,
            ]);

            return redirect()->back()->with('success', 'Kategori berhasil ditambahkan!');

        } catch (\Exception $e) {
            // Kalau database nolak karena no_akun double / duplikat
            return redirect()->back()
                ->withInput()
                ->with('error', 'No Akun tersebut sudah terdaftar, silakan gunakan yang lain!');
        }
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'no_akun'   => 'required',
            'nama_akun' => 'required',
        ]);

        $kategori = Kategori::findOrFail($id);

        // TAMBAHKAN TRY-CATCH JUGA DI UPDATE (OPSIONAL TAPI AMAN)
        try {
            $kategori->update([
                'no_akun'   => $request->no_akun,
                'nama_akun' => $request->nama_akun,
            ]);

            return redirect()->back()->with('success', 'Kategori berhasil diperbarui!');

        } catch (\Exception $e) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Anjir, gagal update karena No Akun kembar dengan data lain!');
        }
    }
    

    public function destroy($id)
    {
        $kategori = Kategori::findOrFail($id);
        $kategori->delete();

        return redirect()->back()->with('success', 'Kategori berhasil dihapus!');
    }
}