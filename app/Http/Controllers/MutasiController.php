<?php

namespace App\Http\Controllers;

use App\Models\Mutasi;
use App\Models\Kategori;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class MutasiController extends Controller
{
    public function index()
    {
        $mutasi = Mutasi::with('kategori')->latest('tanggal')->get();
        return view('mutasi.index', compact('mutasi'));
    }

    public function create()
    {
        $kategoris = Kategori::all(); // Untuk dropdown No COA & Nama COA
        return view('mutasi.create', compact('kategoris'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'tanggal'     => 'required|date',
            'no_jurnal'   => 'nullable|string',
            'nama'        => 'nullable|string',
            'sumber'      => 'nullable|string',
            'keterangan'  => 'nullable|string',
            'kategori_id' => 'required|exists:kategoris,id',
            'debet'       => 'nullable|numeric|min:0',
            'kredit'      => 'nullable|numeric|min:0',
            'bukti_foto'  => 'nullable|file|mimes:jpg,jpeg,png,webp,pdf|max:2048',
        ]);

        $path = null;
        if ($request->hasFile('bukti_foto')) {
            $path = $request->file('bukti_foto')->store('bukti_mutasi', 'public');
        }

        Mutasi::create([
            'tanggal'     => $request->tanggal,
            'no_jurnal'   => $request->no_jurnal,
            'nama'        => $request->nama,
            'sumber'      => $request->sumber,
            'keterangan'  => $request->keterangan,
            'kategori_id' => $request->kategori_id,
            'debet'       => $request->debet ?? 0,
            'kredit'      => $request->kredit ?? 0,
            'bukti_foto'  => $path,
        ]);

        return redirect()->route('mutasi.index')->with('success', 'Data mutasi berhasil disimpan!');
    }

    public function destroy($id)
    {
        $mutasi = Mutasi::findOrFail($id);

        if ($mutasi->bukti_foto && Storage::disk('public')->exists($mutasi->bukti_foto)) {
            Storage::disk('public')->delete($mutasi->bukti_foto);
        }

        $mutasi->delete();

        return redirect()->route('mutasi.index')->with('success', 'Data mutasi berhasil dihapus!');
    }
}