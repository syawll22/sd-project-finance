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
        $rekenings = \App\Models\Rekening::all();
        $kategoris = \App\Models\Kategori::all();
        
        return view('mutasi.create', compact('rekenings', 'kategoris'));
    }

    public function store(Request $request)
    {
        // Validasi input
        $request->validate([
            'tanggal' => 'required|date',
            'rekening_id' => 'required|exists:rekenings,id',
            'jenis' => 'required|in:masuk,keluar,pindah',
            'nominal' => 'required|numeric',
            'bukti_foto' => 'nullable|image|max:2048',
        ]);

        $rekening = \App\Models\Rekening::findOrFail($request->rekening_id);
        $namaBank = $rekening->nama_bank ?? $rekening->nama_rekening ?? 'Cash';
        
        // Tentukan KK atau KM
        $kodeJenis = ($request->jenis === 'masuk') ? 'KM' : 'KK';
        
        // Format YYMM dari tanggal transaksi
        $carbonDate = \Carbon\Carbon::parse($request->tanggal);
        $tahunBulan = $carbonDate->format('ym'); // Contoh: 2610
        
        // Prefix pencarian nomor urut
        $prefix = "{$namaBank}/{$kodeJenis}-{$tahunBulan}";
        
        // Cari nomor urut terakhir di bulan & tahun tersebut untuk bank ini
        $lastMutasi = \App\Models\Mutasi::where('no_jurnal', 'like', "{$prefix}%")
                        ->orderBy('no_jurnal', 'desc')
                        ->first();
                        
        $urutan = 1;
        if ($lastMutasi) {
            $lastNum = (int) substr($lastMutasi->no_jurnal, -3);
            $urutan = $lastNum + 1;
        }
        
        $noJurnalOtomatis = $prefix . str_pad($urutan, 3, '0', STR_PAD_LEFT);

        // Handle Upload Foto Bukti
        $pathFoto = null;
        if ($request->hasFile('bukti_foto')) {
            $file = $request->file('bukti_foto');
            $filename = time() . '_' . preg_replace('/\s+/', '_', $file->getClientOriginalName());
            // Simpan di storage/app/public/bukti_mutasi
            $pathFoto = $file->storeAs('bukti_mutasi', $filename, 'public');
        }

        // Simpan ke database
        \App\Models\Mutasi::create([
            'tanggal' => $request->tanggal,
            'no_jurnal' => $noJurnalOtomatis,
            'rekening_id' => $request->rekening_id,
            'kategori_id' => $request->kategori_id,
            'jenis' => $request->jenis,
            'nominal' => $request->nominal,
            'keterangan' => $request->keterangan,
            'bukti_foto' => $pathFoto,
        ]);

        return redirect()->route('mutasi.index')->with('success', 'Mutasi berhasil ditambahkan dengan No Jurnal: ' . $noJurnalOtomatis);
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
        'bukti_foto'  => 'nullable|file|max:10240',
    ]);

    $buktiPath = $mutasi->bukti_foto;

    if ($request->hasFile('bukti_foto')) {
        if ($mutasi->bukti_foto) {
            Storage::disk('public')->delete($mutasi->bukti_foto);
        }

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
            $oldPath = str_replace(['public/', 'storage/'], '', $mutasi->bukti_foto);
            Storage::disk('public')->delete($oldPath);
        }

        $mutasi->delete();

        return redirect()->route('mutasi.index')->with('success', 'Data Mutasi berhasil dihapus!');
    }
}