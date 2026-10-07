<?php

namespace App\Http\Controllers;

use App\Models\Kategori;
use Illuminate\Http\Request;

class KategoriController extends Controller
{
    public function index()
    {
        $kategori = Kategori::all();
        return view('kategori.index', compact('kategori'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'no_akun'   => 'required|string|max:100',
            'nama_akun' => 'required|string|max:255',
        ]);

        Kategori::create([
            'no_akun'   => $request->no_akun,
            'nama_akun' => $request->nama_akun,
        ]);

        return redirect()->route('kategori.index')->with('success', 'Kategori COA berhasil ditambahkan!');
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'no_akun'   => 'required|string|max:100',
            'nama_akun' => 'required|string|max:255',
        ]);

        $kat = Kategori::findOrFail($id);
        $kat->update([
            'no_akun'   => $request->no_akun,
            'nama_akun' => $request->nama_akun,
        ]);

        return redirect()->route('kategori.index')->with('success', 'Kategori COA berhasil diperbarui!');
    }

    public function destroy($id)
    {
        $kat = Kategori::findOrFail($id);
        $kat->delete();

        return redirect()->route('kategori.index')->with('success', 'Kategori COA berhasil dihapus!');
    }
}