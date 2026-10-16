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

    public function update(Request $request, $id)
    {
        $mutasi = Mutasi::with('rekening')->findOrFail($id);

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

        $rekeningBaru = Rekening::findOrFail($validated['rekening_id']);
        $rekeningLama = $mutasi->rekening;
        $bankLama = $rekeningLama->nama_bank ?? $rekeningLama->nama_rekening ?? 'Cash';
        $bankBaru = $rekeningBaru->nama_bank ?? $rekeningBaru->nama_rekening ?? 'Cash';
        $jurnalPerluDibuatUlang = $bankLama !== $bankBaru
            || Carbon::parse($mutasi->tanggal)->format('ym') !== Carbon::parse($validated['tanggal'])->format('ym')
            || $mutasi->jenis !== $validated['jenis'];

        $noJurnal = $jurnalPerluDibuatUlang
            ? $this->generateNoJurnal($rekeningBaru, $validated['jenis'], $validated['tanggal'], $mutasi->id)
            : $mutasi->no_jurnal;

        $buktiPath = $mutasi->bukti_foto;
        $oldPathToDelete = null;

        if ($request->hasFile('bukti_foto')) {
            $file = $request->file('bukti_foto');
            $extension = strtolower($file->getClientOriginalExtension() ?: $file->extension() ?: 'bin');
            $filename = now()->format('YmdHis') . '_' . bin2hex(random_bytes(6)) . '.' . $extension;
            $newPath = $file->storeAs('bukti_mutasi', $filename, 'public');

            if (!$newPath) {
                throw ValidationException::withMessages(['bukti_foto' => 'Bukti foto gagal disimpan. Coba unggah ulang.']);
            }

            $oldPathToDelete = $this->normalizeBuktiPath($mutasi->bukti_foto);
            $buktiPath = $newPath;
        }

        $mutasi->update([
            'no_jurnal' => $noJurnal,
            'tanggal' => $validated['tanggal'],
            'rekening_id' => $validated['rekening_id'],
            'kategori_id' => $validated['kategori_id'],
            'jenis' => $validated['jenis'],
            'nominal' => $validated['nominal'],
            'keterangan' => $validated['keterangan'] ?? null,
            'bukti_foto' => $buktiPath,
        ]);

        if ($oldPathToDelete) {
            Storage::disk('public')->delete($oldPathToDelete);
        }

        return redirect()->route('mutasi.index')->with('success', 'Data mutasi berhasil diperbarui.');
    }

    /** Menampilkan atau mengunduh file bukti melalui storage Laravel, tanpa bergantung pada symlink /storage. */
    public function bukti(Request $request, $id)
    {
        $mutasi = Mutasi::findOrFail($id);
        $path = $this->normalizeBuktiPath($mutasi->bukti_foto);

        if (!$path || !Storage::disk('public')->exists($path)) {
            abort(404, 'File bukti transaksi tidak ditemukan di penyimpanan.');
        }

        $filename = basename($path);
        $disposition = $request->boolean('download') ? 'attachment' : 'inline';

        return Storage::disk('public')->response($path, $filename, [
            'X-Content-Type-Options' => 'nosniff',
        ], $disposition);
    }

    public function destroy($id)
    {
        $mutasi = Mutasi::findOrFail($id);
        $path = $this->normalizeBuktiPath($mutasi->bukti_foto);

        $mutasi->delete();

        if ($path) {
            Storage::disk('public')->delete($path);
        }

        return redirect()->route('mutasi.index')->with('success', 'Data mutasi berhasil dihapus.');
    }

    /** Nomor urut dipakai bersama oleh KM dan KK untuk bank serta bulan yang sama. */
    private function generateNoJurnal(Rekening $rekening, string $jenis, string $tanggal, ?int $ignoreId = null): string
    {
        $namaBank = trim($rekening->nama_bank ?? $rekening->nama_rekening ?? 'Cash');
        $kodeJenis = $jenis === 'masuk' ? 'KM' : 'KK';
        $tahunBulan = Carbon::parse($tanggal)->format('ym');

        // Ambil seluruh nomor untuk bank/bulan ini, bukan hanya KM atau KK.
        $numbers = Mutasi::query()
            ->where('no_jurnal', 'like', $namaBank . '/%-' . $tahunBulan . '%')
            ->when($ignoreId, fn ($query) => $query->where('id', '!=', $ignoreId))
            ->lockForUpdate()
            ->pluck('no_jurnal');

        $lastNumber = 0;
        foreach ($numbers as $number) {
            if (preg_match('/-' . preg_quote($tahunBulan, '/') . '(\d+)$/', $number, $matches)) {
                $lastNumber = max($lastNumber, (int) $matches[1]);
            }
        }

        $prefix = $namaBank . '/' . $kodeJenis . '-' . $tahunBulan;

        return $prefix . str_pad((string) ($lastNumber + 1), 3, '0', STR_PAD_LEFT);
    }

    /** Mendukung path baru dan beberapa format path lama yang mungkin sudah tersimpan di database. */
    private function normalizeBuktiPath(?string $path): ?string
    {
        if (!$path) {
            return null;
        }

        $path = str_replace('\\', '/', trim($path));
        $path = ltrim($path, '/');
        $path = preg_replace('#^(storage/app/public/|storage/|public/)#', '', $path);

        if (!str_contains($path, '/')) {
            $path = 'bukti_mutasi/' . $path;
        }

        return $path;
    }
}
