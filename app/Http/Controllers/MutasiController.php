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

        // Filter Rekening
        if ($request->filled('rekening_id')) {
            $query->where('rekening_id', $request->rekening_id);
        }

        // Filter Jenis
        if ($request->filled('jenis')) {
            $query->where('jenis', $request->jenis);
        }

        $mutasi = $query->get();
        $rekenings = Rekening::all();

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
            'tanggal'     => 'required|date',
            'rekening_id' => 'required|exists:rekenings,id',
            'kategori_id' => 'nullable|exists:kategoris,id',
            'jenis'       => 'required|in:masuk,keluar,pindah,transfer',
            'nominal'     => 'required|numeric|min:1',
            'keterangan'  => 'nullable|string',
            'bukti_foto'  => 'nullable|file|mimes:jpg,jpeg,png,webp,pdf|max:2048',
        ]);

        DB::transaction(function () use ($request) {
            $path = null;
            if ($request->hasFile('bukti_foto')) {
                $path = $request->file('bukti_foto')->store('bukti_mutasi', 'public');
            }

            // Simpan Data Mutasi
            Mutasi::create([
                'tanggal'     => $request->tanggal,
                'rekening_id' => $request->rekening_id,
                'kategori_id' => $request->kategori_id,
                'jenis'       => $request->jenis,
                'nominal'     => $request->nominal,
                'keterangan'  => $request->keterangan,
                'bukti_foto'  => $path,
            ]);

            // Update Saldo Rekening
            $rekening = Rekening::findOrFail($request->rekening_id);
            if ($request->jenis == 'masuk') {
                $rekening->increment('saldo', $request->nominal);
            } elseif (in_array($request->jenis, ['keluar', 'pindah', 'transfer'])) {
                $rekening->decrement('saldo', $request->nominal);
            }
        });

        return redirect()->route('mutasi.index')->with('success', 'Mutasi transaksi berhasil ditambahkan!');
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
            'kategori_id' => 'nullable|exists:kategoris,id',
            'jenis'       => 'required|in:masuk,keluar,pindah,transfer',
            'nominal'     => 'required|numeric|min:1',
            'keterangan'  => 'nullable|string',
            'bukti_foto'  => 'nullable|file|mimes:jpg,jpeg,png,webp,pdf|max:2048',
        ]);

        $mutasi = Mutasi::findOrFail($id);

        DB::transaction(function () use ($request, $mutasi) {
            // Revert Saldo Lama terlebih dahulu
            $rekeningLama = Rekening::findOrFail($mutasi->rekening_id);
            if ($mutasi->jenis == 'masuk') {
                $rekeningLama->decrement('saldo', $mutasi->nominal);
            } elseif (in_array($mutasi->jenis, ['keluar', 'pindah', 'transfer'])) {
                $rekeningLama->increment('saldo', $mutasi->nominal);
            }

            // Upload Foto Baru jika ada
            $path = $mutasi->bukti_foto;
            if ($request->hasFile('bukti_foto')) {
                if ($path && Storage::disk('public')->exists($path)) {
                    Storage::disk('public')->delete($path);
                }
                $path = $request->file('bukti_foto')->store('bukti_mutasi', 'public');
            }

            // Update Mutasi
            $mutasi->update([
                'tanggal'     => $request->tanggal,
                'rekening_id' => $request->rekening_id,
                'kategori_id' => $request->kategori_id,
                'jenis'       => $request->jenis,
                'nominal'     => $request->nominal,
                'keterangan'  => $request->keterangan,
                'bukti_foto'  => $path,
            ]);

            // Terapkan Saldo Baru
            $rekeningBaru = Rekening::findOrFail($request->rekening_id);
            if ($request->jenis == 'masuk') {
                $rekeningBaru->increment('saldo', $request->nominal);
            } elseif (in_array($request->jenis, ['keluar', 'pindah', 'transfer'])) {
                $rekeningBaru->decrement('saldo', $request->nominal);
            }
        });

        return redirect()->route('mutasi.index')->with('success', 'Mutasi transaksi berhasil diperbarui!');
    }

    public function destroy($id)
    {
        $mutasi = Mutasi::findOrFail($id);

        DB::transaction(function () use ($mutasi) {
            // Kembalikan Saldo Rekening
            $rekening = Rekening::findOrFail($mutasi->rekening_id);
            if ($mutasi->jenis == 'masuk') {
                $rekening->decrement('saldo', $mutasi->nominal);
            } elseif (in_array($mutasi->jenis, ['keluar', 'pindah', 'transfer'])) {
                $rekening->increment('saldo', $mutasi->nominal);
            }

            // Hapus file foto dari storage jika ada
            if ($mutasi->bukti_foto && Storage::disk('public')->exists($mutasi->bukti_foto)) {
                Storage::disk('public')->delete($mutasi->bukti_foto);
            }

            $mutasi->delete();
        });

        return redirect()->route('mutasi.index')->with('success', 'Mutasi berhasil dihapus!');
    }
}