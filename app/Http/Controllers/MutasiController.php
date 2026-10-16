<?php

namespace App\Http\Controllers;

use App\Models\Mutasi;
use App\Models\Rekening;
use App\Models\Kategori;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;

class MutasiController extends Controller
{
    public function index(Request $request)
    {
        $query = Mutasi::with(['rekening', 'kategori']);

        if ($request->filled('rekening_id')) {
            $query->where('rekening_id', $request->rekening_id);
        }

        if ($request->filled('bulan')) {
            try {
                $bulan = Carbon::createFromFormat('Y-m', $request->bulan);
                $query->whereBetween('tanggal', [
                    $bulan->copy()->startOfMonth()->toDateString(),
                    $bulan->copy()->endOfMonth()->toDateString(),
                ]);
            } catch (\Throwable $e) {
                // Abaikan filter bulan yang formatnya tidak valid.
            }
        }

        $mutasis = $query->orderByDesc('tanggal')->orderByDesc('id')->get();
        $rekenings = Rekening::orderBy('nama_rekening')->get();

        return view('mutasi.index', compact('mutasis', 'rekenings'));
    }

    public function create()
    {
        $rekenings = Rekening::orderBy('nama_rekening')->get();
        $kategoris = Kategori::orderBy('no_akun')->get();

        return view('mutasi.create', compact('rekenings', 'kategoris'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'tanggal' => ['required', 'date'],
            'rekening_id' => ['required', 'exists:rekenings,id'],
            'kategori_id' => ['required', 'exists:kategoris,id'],
            'jenis' => ['required', 'in:masuk,keluar,pindah'],
            'nominal' => ['required', 'numeric', 'min:0.01'],
            'keterangan' => ['nullable', 'string', 'max:5000'],
            'bukti_foto' => ['nullable', 'file', 'mimes:jpg,jpeg,png,webp,heic,heif', 'max:10240'],
        ], [
            'bukti_foto.mimes' => 'Bukti harus berupa JPG, JPEG, PNG, WEBP, HEIC, atau HEIF.',
            'bukti_foto.max' => 'Ukuran bukti maksimal 10 MB.',
        ]);

        $rekening = Rekening::findOrFail($validated['rekening_id']);
        $noJurnal = $this->generateNoJurnal($rekening, $validated['jenis'], $validated['tanggal']);
        $pathFoto = null;

        if ($request->hasFile('bukti_foto')) {
            $file = $request->file('bukti_foto');
            $extension = strtolower($file->getClientOriginalExtension() ?: $file->extension() ?: 'bin');
            $filename = now()->format('YmdHis') . '_' . bin2hex(random_bytes(6)) . '.' . $extension;
            $pathFoto = $file->storeAs('bukti_mutasi', $filename, 'public');
        }

        Mutasi::create([
            'tanggal' => $validated['tanggal'],
            'no_jurnal' => $noJurnal,
            'rekening_id' => $validated['rekening_id'],
            'kategori_id' => $validated['kategori_id'],
            'jenis' => $validated['jenis'],
            'nominal' => $validated['nominal'],
            'keterangan' => $validated['keterangan'] ?? null,
            'bukti_foto' => $pathFoto,
        ]);

        return redirect()->route('mutasi.index')->with('success', 'Mutasi berhasil ditambahkan dengan No Jurnal: ' . $noJurnal);
    }

    public function edit($id)
    {
        $mutasi = Mutasi::with(['rekening', 'kategori'])->findOrFail($id);
        $rekenings = Rekening::orderBy('nama_rekening')->get();
        $kategoris = Kategori::orderBy('no_akun')->get();

        return view('mutasi.edit', compact('mutasi', 'rekenings', 'kategoris'));
    }
}