<?php

namespace App\Http\Controllers;

use App\Models\Mutasi;
use App\Models\Rekening;
use App\Models\Kategori;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;

class MutasiController extends Controller
{
    public function index(Request $request)
{
    $query = Mutasi::with(['rekening', 'kategori']);

    // Filter Berdasarkan Rekening / Bank
    if ($request->filled('rekening_id')) {
        $query->where('rekening_id', $request->rekening_id);
    }

    // Filter Berdasarkan Bulan (Format: YYYY-MM)
    if ($request->filled('bulan')) {
        $query->whereRaw("DATE_FORMAT(tanggal, '%Y-%m') = ?", [$request->bulan]);
    }

    $mutasis = $query->latest()->get();
    $rekenings = Rekening::all();

    return view('mutasi.index', compact('mutasis', 'rekenings'));
}

public function store(Request $request)
{
    $request->validate([
        'no_jurnal'   => 'required|string|max:255',
        'tanggal'     => 'required|date',
        'rekening_id' => 'required|exists:rekenings,id',
        'kategori_id' => 'required|exists:kategoris,id',
        'nominal'     => 'required|numeric',
        'keterangan'  => 'nullable|string',
        'bukti_foto'  => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
    ]);

    $buktiPath = null;
    if ($request->hasFile('bukti_foto')) {
        $file = $request->file('bukti_foto');
        $filename = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
        // Simpan langsung ke public/bukti_mutasi biar aman di Railway
        $file->move(public_path('bukti_mutasi'), $filename);
        $buktiPath = 'bukti_mutasi/' . $filename;
    }

    Mutasi::create([
        'no_jurnal'   => $request->no_jurnal,
        'tanggal'     => $request->tanggal,
        'rekening_id' => $request->rekening_id,
        'kategori_id' => $request->kategori_id,
        'nominal'     => $request->nominal,
        'keterangan'  => $request->keterangan,
        'bukti_foto'  => $buktiPath,
    ]);

    return redirect()->route('mutasi.index')->with('success', 'Data Mutasi berhasil ditambahkan!');
}
}