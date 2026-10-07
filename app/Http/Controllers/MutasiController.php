<?php

namespace App\Http\Controllers;

use App\Models\Mutasi;
use App\Models\Rekening;
use App\Models\Kategori;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;

class MutasiController extends Controller
{
    public function index(Request $request)
    {
        $query = Mutasi::with(['rekening', 'kategori'])->latest('tanggal');

        if ($request->filled('rekening_id')) {
            $query->where('rekening_id', $request->rekening_id);
        }

        $mutasi = $query->get();
        $rekenings = Rekening::all();

        return view('mutasi.index', compact('mutasi', 'rekenings'));
    }

    public function create()
    {
        // WAJIB pass $rekenings dan $kategoris biar view gak error
        $rekenings = Rekening::all();
        $kategoris = Kategori::all();

        return view('mutasi.create', compact('rekenings', 'kategoris'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'tanggal'     => 'required|date',
            'rekening_id' => 'required|exists:rekenings,id',
            'no_jurnal'   => 'nullable|string',
            'nama'        => 'nullable|string',
            'sumber'      => 'nullable|string',
            'keterangan'  => 'nullable|string',
            'kategori_id' => 'required|exists:kategoris,id',
            'debet'       => 'nullable|numeric|min:0',
            'kredit'      => 'nullable|numeric|min:0',
            'bukti_foto'  => 'nullable|file|mimes:jpg,jpeg,png,webp,pdf|max:2048',
        ]);

        DB::transaction(function () use ($request) {
            $path = null;
            if ($request->hasFile('bukti_foto')) {
                $path = $request->file('bukti_foto')->store('bukti_mutasi', 'public');
            }

            $debet = $request->debet ?? 0;
            $kredit = $request->kredit ?? 0;

            Mutasi::create([
                'tanggal'     => $request->tanggal,
                'rekening_id' => $request->rekening_id,
                'no_jurnal'   => $request->no_jurnal,
                'nama'        => $request->nama,
                'sumber'      => $request->sumber,
                'keterangan'  => $request->keterangan,
                'kategori_id' => $request->kategori_id,
                'debet'       => $debet,
                'kredit'      => $kredit,
                'bukti_foto'  => $path,
            ]);

            // Update Saldo Rekening (Debet menambah saldo, Kredit mengurangi saldo)
            $rekening = Rekening::findOrFail($request->rekening_id);
            if ($debet > 0) {
                $rekening->increment('saldo', $debet);
            }
            if ($kredit > 0) {
                $rekening->decrement('saldo', $kredit);
            }
        });

        return redirect()->route('mutasi.index')->with('success', 'Data mutasi jurnal berhasil ditambahkan!');
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
        $request->validate([
            'tanggal'     => 'required|date',
            'rekening_id' => 'required|exists:rekenings,id',
            'no_jurnal'   => 'nullable|string',
            'nama'        => 'nullable|string',
            'sumber'      => 'nullable|string',
            'keterangan'  => 'nullable|string',
            'kategori_id' => 'required|exists:kategoris,id',
            'debet'       => 'nullable|numeric|min:0',
            'kredit'      => 'nullable|numeric|min:0',
            'bukti_foto'  => 'nullable|file|mimes:jpg,jpeg,png,webp,pdf|max:2048',
        ]);

        $mutasi = Mutasi::findOrFail($id);

        DB::transaction(function () use ($request, $mutasi) {
            // Revert saldo lama
            $rekeningLama = Rekening::findOrFail($mutasi->rekening_id);
            if ($mutasi->debet > 0) {
                $rekeningLama->decrement('saldo', $mutasi->debet);
            }
            if ($mutasi->kredit > 0) {
                $rekeningLama->increment('saldo', $mutasi->kredit);
            }

            // Bukti foto
            $path = $mutasi->bukti_foto;
            if ($request->hasFile('bukti_foto')) {
                if ($path && Storage::disk('public')->exists($path)) {
                    Storage::disk('public')->delete($path);
                }
                $path = $request->file('bukti_foto')->store('bukti_mutasi', 'public');
            }

            $debet = $request->debet ?? 0;
            $kredit = $request->kredit ?? 0;

            $mutasi->update([
                'tanggal'     => $request->tanggal,
                'rekening_id' => $request->rekening_id,
                'no_jurnal'   => $request->no_jurnal,
                'nama'        => $request->nama,
                'sumber'      => $request->sumber,
                'keterangan'  => $request->keterangan,
                'kategori_id' => $request->kategori_id,
                'debet'       => $debet,
                'kredit'      => $kredit,
                'bukti_foto'  => $path,
            ]);

            // Apply saldo baru
            $rekeningBaru = Rekening::findOrFail($request->rekening_id);
            if ($debet > 0) {
                $rekeningBaru->increment('saldo', $debet);
            }
            if ($kredit > 0) {
                $rekeningBaru->decrement('saldo', $kredit);
            }
        });

        return redirect()->route('mutasi.index')->with('success', 'Data mutasi berhasil diperbarui!');
    }

    public function destroy($id)
    {
        $mutasi = Mutasi::findOrFail($id);

        DB::transaction(function () use ($mutasi) {
            if ($mutasi->rekening) {
                if ($mutasi->debet > 0) {
                    $mutasi->rekening->decrement('saldo', $mutasi->debet);
                }
                if ($mutasi->kredit > 0) {
                    $mutasi->rekening->increment('saldo', $mutasi->kredit);
                }
            }

            if ($mutasi->bukti_foto && Storage::disk('public')->exists($mutasi->bukti_foto)) {
                Storage::disk('public')->delete($mutasi->bukti_foto);
            }

            $mutasi->delete();
        });

        return redirect()->route('mutasi.index')->with('success', 'Data mutasi berhasil dihapus!');
    }
}