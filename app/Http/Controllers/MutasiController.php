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
    public function index()
    {
        $mutasis = Mutasi::with(['rekening', 'kategori'])->latest()->get();

        // Kirim $mutasis dan $mutasi sekaligus biar nggak bikin bentrok cache
        return view('mutasi.index', [
            'mutasis' => $mutasis,
            'mutasi'  => $mutasis
        ]);
    }

    public function create()
    {
        $rekenings = Rekening::all();
        $kategoris = Kategori::all();
        return view('mutasi.create', compact('rekenings', 'kategoris'));
    }

    public function store(Request $request)
    {
        // 1. AUTO-PATCH DATABASE RAILWAY (Jaga-jaga kalau kolom nominal di server belum ada)
        if (!Schema::hasColumn('mutasis', 'nominal')) {
            Schema::table('mutasis', function (Blueprint $table) {
                $table->decimal('nominal', 15, 2)->default(0)->after('kategori_id');
            });
        }

        // 2. VALIDASI INPUT
        $request->validate([
            'tanggal'     => 'required|date',
            'rekening_id' => 'required|exists:rekenings,id',
            'kategori_id' => 'required|exists:kategoris,id',
            'nominal'     => 'required|numeric',
            'keterangan'  => 'nullable|string',
            'bukti_foto'  => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
        ]);

        // 3. HANDLE UPLOAD FOTO
        $buktiPath = null;
        if ($request->hasFile('bukti_foto')) {
            $buktiPath = $request->file('bukti_foto')->store('bukti_mutasi', 'public');
        }

        // 4. SIMPAN DATA MUTASI (Lengkap dengan nominal)
        Mutasi::create([
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