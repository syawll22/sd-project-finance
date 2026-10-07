@extends('layouts.app')

@section('title', 'Tambah Mutasi - S&D Finance')

@section('content')
<div class="flex justify-between items-center mb-6">
    <div>
        <h1 class="text-3xl font-extrabold text-[#1C2A24] tracking-tight">Tambah Mutasi</h1>
        <p class="text-xs font-medium text-gray-500 mt-1">Input data transaksi jurnal baru sesuai standar akuntansi.</p>
    </div>
    <a href="{{ route('mutasi.index') }}" class="text-xs text-gray-600 hover:text-gray-900 font-bold bg-white px-4 py-2.5 rounded-xl border border-gray-200 transition flex items-center gap-1.5 shadow-sm">
        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18" />
        </svg>
        <span>Kembali</span>
    </a>
</div>

<div class="bg-white rounded-[28px] p-6 shadow-sm border border-gray-100/50 max-w-3xl">
    <form action="{{ route('mutasi.store') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
        @csrf
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Tanggal <span class="text-rose-500">*</span></label>
                <input type="date" name="tanggal" value="{{ old('tanggal', date('Y-m-d')) }}" class="w-full bg-gray-50 border border-gray-200 rounded-xl p-3 text-sm focus:outline-none focus:border-[#D8A749]" required>
            </div>
            <div>
                <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Rekening / Kas <span class="text-rose-500">*</span></label>
                <select name="rekening_id" class="w-full bg-gray-50 border border-gray-200 rounded-xl p-3 text-sm focus:outline-none focus:border-[#D8A749]" required>
                    <option value="">-- Pilih Rekening --</option>
                    @foreach($rekenings as $rek)
                        <option value="{{ $rek->id }}" {{ old('rekening_id') == $rek->id ? 'selected' : '' }}>
                            {{ $rek->nama_rekening ?? $rek->nama }}
                        </option>
                    @endforeach
                </select>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
                <label class="block text-xs font-bold text-gray-500 uppercase mb-1">No. Jurnal</label>
                <input type="text" name="no_jurnal" value="{{ old('no_jurnal') }}" placeholder="Contoh: SALDO AWAL / BKM-01" class="w-full bg-gray-50 border border-gray-200 rounded-xl p-3 text-sm focus:outline-none focus:border-[#D8A749]">
            </div>
            <div>
                <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Nama</label>
                <input type="text" name="nama" value="{{ old('nama') }}" placeholder="Penerima / Pembayar" class="w-full bg-gray-50 border border-gray-200 rounded-xl p-3 text-sm focus:outline-none focus:border-[#D8A749]">
            </div>
            <div>
                <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Sumber</label>
                <input type="text" name="sumber" value="{{ old('sumber') }}" placeholder="Asal sumber dana" class="w-full bg-gray-50 border border-gray-200 rounded-xl p-3 text-sm focus:outline-none focus:border-[#D8A749]">
            </div>
        </div>

        <div>
            <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Pilih COA (Akun) <span class="text-rose-500">*</span></label>
            <select name="kategori_id" class="w-full bg-gray-50 border border-gray-200 rounded-xl p-3 text-sm focus:outline-none focus:border-[#D8A749]" required>
                <option value="">-- Pilih COA --</option>
                @foreach($kategoris as $kat)
                    <option value="{{ $kat->id }}" {{ old('kategori_id') == $kat->id ? 'selected' : '' }}>
                        {{ $kat->no_akun ?? $kat->kode }} - {{ $kat->nama_akun ?? $kat->nama_kategori }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="block text-xs font-bold text-emerald-600 uppercase mb-1">Nominal Debet (Masuk)</label>
                <input type="number" step="0.01" name="debet" value="{{ old('debet', 0) }}" placeholder="0" class="w-full bg-gray-50 border border-gray-200 rounded-xl p-3 text-sm focus:outline-none focus:border-[#D8A749]">
            </div>
            <div>
                <label class="block text-xs font-bold text-rose-600 uppercase mb-1">Nominal Kredit (Keluar)</label>
                <input type="number" step="0.01" name="kredit" value="{{ old('kredit', 0) }}" placeholder="0" class="w-full bg-gray-50 border border-gray-200 rounded-xl p-3 text-sm focus:outline-none focus:border-[#D8A749]">
            </div>
        </div>

        <div>
            <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Keterangan</label>
            <textarea name="keterangan" rows="3" placeholder="Catatan transaksi..." class="w-full bg-gray-50 border border-gray-200 rounded-xl p-3 text-sm focus:outline-none focus:border-[#D8A749]">{{ old('keterangan') }}</textarea>
        </div>

        <div>
            <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Bukti Struk / Foto (Opsional)</label>
            <input type="file" name="bukti_foto" class="w-full text-xs text-gray-500 bg-gray-50 border border-gray-200 rounded-xl p-2.5 focus:outline-none focus:border-[#D8A749] file:mr-4 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-bold file:bg-gray-200 file:text-gray-700 hover:file:bg-gray-300">
        </div>

        <button type="submit" class="w-full bg-[#D8A749] hover:bg-[#c4953c] text-white font-bold py-3.5 rounded-xl shadow-md transition duration-200 mt-2 flex items-center justify-center gap-2">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
            </svg>
            <span>Simpan Transaksi</span>
        </button>
    </form>
</div>
@endsection