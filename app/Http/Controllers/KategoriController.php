<?php

namespace App\Http\Controllers;

use App\Models\Kategori;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;

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
        'no_akun'   => 'nullable|string', // atau required
        'nama_akun' => 'required|string',
    ]);

    Kategori::create([
        'no_akun'   => $request->no_akun,
        'nama_akun' => $request->nama_akun,
    ]);

    return redirect()->back()->with('success', 'Kategori COA berhasil ditambahkan!');
}

    public function update(Request $request, $id)
    {
        $noAkun = $request->input('no_akun') ?? $request->input('kode');
        $namaAkun = $request->input('nama_akun') ?? $request->input('nama_kategori');

        $data = [];

        if (Schema::hasColumn('kategoris', 'no_akun')) {
            $data['no_akun'] = $noAkun;
        }
        if (Schema::hasColumn('kategoris', 'kode')) {
            $data['kode'] = $noAkun;
        }
        if (Schema::hasColumn('kategoris', 'nama_akun')) {
            $data['nama_akun'] = $namaAkun;
        }
        if (Schema::hasColumn('kategoris', 'nama_kategori')) {
            $data['nama_kategori'] = $namaAkun;
        }

        $kat = Kategori::findOrFail($id);
        $kat->update($data);

        return redirect()->route('kategori.index')->with('success', 'Kategori COA berhasil diperbarui!');
    }

    public function destroy($id)
    {
        $kat = Kategori::findOrFail($id);
        $kat->delete();

        return redirect()->route('kategori.index')->with('success', 'Kategori COA berhasil dihapus!');
    }
}