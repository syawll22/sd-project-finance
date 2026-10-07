@extends('layouts.app')

@section('title', 'Tambah Mutasi - S&D Finance')

@section('content')
<div class="max-w-2xl mx-auto space-y-6">
    
    <!-- Header Page -->
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-black text-slate-900 tracking-tight">Tambah Mutasi Baru</h1>
            <p class="text-xs text-slate-500 mt-1">Input transaksi penerimaan atau pengeluaran kas/bank.</p>
        </div>
        <a href="{{ route('mutasi.index') }}" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-600 font-bold text-xs rounded-xl transition">
            Kembali
        </a>
    </div>

    <!-- Form Card -->
    <div class="bg-white rounded-[2rem] p-6 shadow-sm border border-slate-100">
        <form action="{{ route('mutasi.store') }}" method="POST" enctype="multipart/form-data" class="space-y-4 text-xs">
            @csrf

            <!-- Tanggal -->
            <div>
                <label class="block font-semibold text-slate-600 mb-1">Tanggal Transaksi</label>
                <input type="date" name="tanggal" value="{{ old('tanggal', date('Y-m-d')) }}" class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:border-[#D8A749]" required>
                @error('tanggal') <span class="text-rose-500 text-[10px]">{{ $message }}</span> @enderror
            </div>

            <!-- Rekening / Bank -->
            <div>
                <label class="block font-semibold text-slate-600 mb-1">Rekening / Kas</label>
                <select name="rekening_id" class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:border-[#D8A749]" required>
                    <option value="">-- Pilih Rekening --</option>
                    @foreach($rekenings as $rek)
                        <option value="{{ $rek->id }}" {{ old('rekening_id') == $rek->id ? 'selected' : '' }}>
                            {{ $rek->nama_bank ?? $rek->nama_rekening ?? 'Rekening #'.$rek->id }}
                        </option>
                    @endforeach
                </select>
                @error('rekening_id') <span class="text-rose-500 text-[10px]">{{ $message }}</span> @enderror
            </div>

            <!-- Kategori COA -->
            <div>
                <label class="block font-semibold text-slate-600 mb-1">Kategori COA</label>
                <select name="kategori_id" class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:border-[#D8A749]" required>
                    <option value="">-- Pilih Kategori --</option>
                    @foreach($kategoris as $kat)
                        <option value="{{ $kat->id }}" {{ old('kategori_id') == $kat->id ? 'selected' : '' }}>
                            {{ $kat->no_akun }} - {{ $kat->nama_akun }}
                        </option>
                    @endforeach
                </select>
                @error('kategori_id') <span class="text-rose-500 text-[10px]">{{ $message }}</span> @enderror
            </div>

            <!-- NOMINAL (PENTING!) -->
            <div>
                <label class="block font-semibold text-slate-600 mb-1">Nominal (Rp)</label>
                <input type="number" step="any" name="nominal" value="{{ old('nominal') }}" placeholder="Contoh: 1500000" class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:border-[#D8A749]" required>
                @error('nominal') <span class="text-rose-500 text-[10px]">{{ $message }}</span> @enderror
            </div>

            <!-- Keterangan -->
            <div>
                <label class="block font-semibold text-slate-600 mb-1">Keterangan / Catatan</label>
                <textarea name="keterangan" rows="3" placeholder="Deskripsi transaksi..." class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:border-[#D8A749]">{{ old('keterangan') }}</textarea>
                @error('keterangan') <span class="text-rose-500 text-[10px]">{{ $message }}</span> @enderror
            </div>

            <!-- Upload Bukti Transaksi -->
            <div>
                <label class="block font-semibold text-slate-600 mb-1">Bukti Transaksi (Foto/Lampiran)</label>
                <input type="file" name="bukti_foto" accept="image/*" class="w-full px-4 py-2 bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:border-[#D8A749]">
                @error('bukti_foto') <span class="text-rose-500 text-[10px]">{{ $message }}</span> @enderror
            </div>

            <!-- Submit Button -->
            <div class="flex justify-end gap-2 pt-4 border-t border-slate-100">
                <button type="submit" class="px-6 py-3 bg-[#1C2A24] hover:bg-[#2c3e36] text-white font-bold rounded-xl transition shadow-lg">
                    Simpan Mutasi
                </button>
            </div>
        </form>
    </div>

</div>
@endsection