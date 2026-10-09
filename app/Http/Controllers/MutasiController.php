<?php

namespace App\Http\Controllers;

use App\Models\Mutasi;
use App\Models\Rekening;
use App\Models\Kategori;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class MutasiController extends Controller
{
    public function index(Request $request)
    {
        $query = Mutasi::with(['rekening', 'kategori']);

        if ($request->filled('rekening_id')) {
            $query->where('rekening_id', $request->rekening_id);
        }

        if ($request->filled('bulan')) {
            $query->whereRaw("DATE_FORMAT(tanggal, '%Y-%m') = ?", [$request->bulan]);
        }

        $mutasis = $query->latest()->get();
        $rekenings = Rekening::all();

        return view('mutasi.index', compact('mutasis', 'rekenings'));
    }

    public function create()
    {
        $rekenings = Rekening::all();
        $kategoris = Kategori::all();
        return view('mutasi.create', compact('rekenings', 'kategoris'));
    }

    public function store(Request $request)
{
    $request->validate([
        'no_jurnal'   => 'required|string|max:255',
        'tanggal'     => 'required|date',
        'rekening_id' => 'required|exists:rekenings,id',
        'kategori_id' => 'required|exists:kategoris,id',
        'jenis'       => 'required|in:masuk,keluar,pindah', // Sesuaikan dengan kolom enum database
        'nominal'     => 'required|numeric',
        'keterangan'  => 'nullable|string',
        'bukti_foto'  => 'nullable|file|image|max:10240',
    ]);

    $buktiPath = null;
    if ($request->hasFile('bukti_foto')) {
        $file = $request->file('bukti_foto');
        $filename = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
        $path = $file->storeAs('bukti_mutasi', $filename, 'public');
        $buktiPath = $path; // Simpan path relatif bersih tanpa dobel 'storage/'
    }

    Mutasi::create([
        'no_jurnal'   => $request->no_jurnal,
        'tanggal'     => $request->tanggal,
        'rekening_id' => $request->rekening_id,
        'kategori_id' => $request->kategori_id,
        'jenis'       => $request->jenis, // masuk / keluar / pindah
        'nominal'     => $request->nominal,
        'keterangan'  => $request->keterangan,
        'bukti_foto'  => $buktiPath,
    ]);

    return redirect()->route('mutasi.index')->with('success', 'Data Mutasi berhasil ditambahkan!');
}

    public function edit($id)
    {
        $mutasi = Mutasi::findOrFail($id);
        $rekenings = Rekening::all();
        $kategoris = Kategori::all();
        return view('mutasi.edit', compact('mutasi', 'rekenings', 'kategoris'));
    }

    public function update(Request $request, $id)
{
    $mutasi = Mutasi::findOrFail($id);

    $request->validate([
        'no_jurnal'   => 'required|string|max:255',
        'tanggal'     => 'required|date',
        'rekening_id' => 'required|exists:rekenings,id',
        'kategori_id' => 'required|exists:kategoris,id',
        'jenis'       => 'required|in:masuk,keluar,pindah',
        'nominal'     => 'required|numeric',
        'keterangan'  => 'nullable|string',
        'bukti_foto' => 'nullable|file |max:10240',   
    ]);

    $buktiPath = $mutasi->bukti_foto;

    if ($request->hasFile('bukti_foto')) {
        $file = $request->file('bukti_foto');
        $filename = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
        $buktiPath = $file->storeAs('bukti_mutasi', $filename, 'public');
    }

    $mutasi->update([
        'no_jurnal'   => $request->no_jurnal,
        'tanggal'     => $request->tanggal,
        'rekening_id' => $request->rekening_id,
        'kategori_id' => $request->kategori_id,
        'jenis'       => $request->jenis,
        'nominal'     => $request->nominal,
        'keterangan'  => $request->keterangan,
        'bukti_foto'  => $buktiPath,
    ]);

    return redirect()->route('mutasi.index')->with('success', 'Data Mutasi berhasil diperbarui!');
}

    public function destroy($id)
    {
        $mutasi = Mutasi::findOrFail($id);

        if ($mutasi->bukti_foto) {
            $oldPath = str_replace('storage/', '', $mutasi->bukti_foto);
            Storage::disk('public')->delete($oldPath);
        }

        $mutasi->delete();

        return redirect()->route('mutasi.index')->with('success', 'Data Mutasi berhasil dihapus!');
    }
}