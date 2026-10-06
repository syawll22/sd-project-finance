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

    // Filter per Rekening / Bank jika dipilih
    if ($request->filled('rekening_id')) {
        $query->where('rekening_id', $request->rekening_id);
    }

    // Filter per Jenis (masuk/keluar/transfer)
    if ($request->filled('jenis')) {
        $query->where('jenis', $request->jenis);
    }

    $mutasi = $query->latest()->get();
    $rekenings = Rekening::all(); // Mengirimkan data rekening ke view untuk dropdown filter

    return view('mutasi.index', compact('mutasi', 'rekenings'));
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
        'tanggal' => 'required|date',
        'rekening_id' => 'required|exists:rekenings,id',
        'kategori_id' => 'nullable|exists:kategoris,id',
        'jenis' => 'required|in:masuk,keluar,pindah',
        'nominal' => 'required|numeric|min:0',
        'keterangan' => 'nullable|string',
        'bukti_foto' => 'nullable|file|max:10240',
    ]);

    // Ambil semua data KECUALI bukti_foto dulu
    $data = $request->except('bukti_foto');

    // Cek dan simpan file jika user mengunggah sesuatu
    if ($request->hasFile('bukti_foto')) {
        $data['bukti_foto'] = $request->file('bukti_foto')->store('bukti_mutasi', 'public');
    }

    Mutasi::create($data);

    return redirect()->route('mutasi.index')->with('success', 'Mutasi berhasil ditambahkan!');
}

    public function edit(Mutasi $mutasi)
    {
        $rekenings = Rekening::all();
        $kategoris = Kategori::all();
        return view('mutasi.edit', compact('mutasi', 'rekenings', 'kategoris'));
    }

   public function update(Request $request, Mutasi $mutasi)
{
    $request->validate([
        'tanggal' => 'required|date',
        'rekening_id' => 'required|exists:rekenings,id',
        'kategori_id' => 'nullable|exists:kategoris,id',
        'jenis' => 'required|in:masuk,keluar,pindah',
        'nominal' => 'required|numeric|min:0',
        'keterangan' => 'nullable|string',
        'bukti_foto' => 'nullable|file|max:10240',
    ]);

    $data = $request->except('bukti_foto');

    if ($request->hasFile('bukti_foto')) {
        // Hapus file lama jika ada
        if ($mutasi->bukti_foto && Storage::disk('public')->exists($mutasi->bukti_foto)) {
            Storage::disk('public')->delete($mutasi->bukti_foto);
        }
        $data['bukti_foto'] = $request->file('bukti_foto')->store('bukti_mutasi', 'public');
    }

    $mutasi->update($data);

    return redirect()->route('mutasi.index')->with('success', 'Mutasi berhasil diperbarui!');
}

    public function destroy(Mutasi $mutasi)
    {
        if ($mutasi->bukti_foto && Storage::disk('public')->exists($mutasi->bukti_foto)) {
            Storage::disk('public')->delete($mutasi->bukti_foto);
        }
        
        $mutasi->delete();

        return redirect()->route('mutasi.index')->with('success', 'Mutasi berhasil dihapus!');
    }
}