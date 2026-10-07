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
        // Ambil input dari form
        $noAkun = $request->input('no_akun') ?? $request->input('kode') ?? '000';
        $namaAkun = $request->input('nama_akun') ?? $request->input('nama_kategori') ?? 'Tanpa Nama';

        // Biar ada aja, walaupun input kosong, tetap simpan ke database
        Kategori::create([
            'kode'          => $noAkun,
            'no_akun'       => $noAkun,
            'nama_kategori' => $namaAkun,
            'nama_akun'     => $namaAkun,
        ]);

        return redirect()->route('kategori.index')->with('success', 'Kategori COA berhasil ditambahkan!');
    }

    public function update(Request $request, $id)
    {
        $noAkun = $request->input('no_akun') ?? $request->input('kode') ?? '000';
        $namaAkun = $request->input('nama_akun') ?? $request->input('nama_kategori') ?? 'Tanpa Nama';

        $kat = Kategori::findOrFail($id);
        $kat->update([
            'kode'          => $noAkun,
            'no_akun'       => $noAkun,
            'nama_kategori' => $namaAkun,
            'nama_akun'     => $namaAkun,
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