@extends('layouts.app')

@section('content')
<div class="max-w-2xl mx-auto bg-white p-8 rounded-3xl shadow-sm">
    <h2 class="text-xl font-bold text-slate-800 mb-6">Tambah Mutasi Transaksi</h2>

    <form action="{{ route('mutasi.store') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
        @csrf

        <div>
            <label class="block font-semibold text-slate-600 mb-1 text-sm">No Jurnal</label>
            <input type="text" name="no_jurnal" value="{{ old('no_jurnal') }}" required class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:border-[#D8A749]">
            @error('no_jurnal') <span class="text-rose-500 text-xs">{{ $message }}</span> @enderror
        </div>

        <div>
            <label class="block font-semibold text-slate-600 mb-1 text-sm">Tanggal</label>
            <input type="date" name="tanggal" value="{{ old('tanggal', date('Y-m-d')) }}" required class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:border-[#D8A749]">
            @error('tanggal') <span class="text-rose-500 text-xs">{{ $message }}</span> @enderror
        </div>

        <div>
            <label class="block font-semibold text-slate-600 mb-1 text-sm">Rekening / Bank</label>
            <select name="rekening_id" required class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:border-[#D8A749]">
                <option value="">-- Pilih Rekening --</option>
                @foreach($rekenings as $rek)
                    <option value="{{ $rek->id }}">{{ $rek->nama_rekening ?? $rek->bank ?? $rek->name }}</option>
                @endforeach
            </select>
            @error('rekening_id') <span class="text-rose-500 text-xs">{{ $message }}</span> @enderror
        </div>

        <div>
            <label class="block font-semibold text-slate-600 mb-1 text-sm">Kategori COA</label>
            <select name="kategori_id" required class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:border-[#D8A749]">
                <option value="">-- Pilih Kategori --</option>
                @foreach($kategoris as $kat)
                    <option value="{{ $kat->id }}">{{ $kat->nama_kategori ?? $kat->name }}</option>
                @endforeach
            </select>
            @error('kategori_id') <span class="text-rose-500 text-xs">{{ $message }}</span> @enderror
        </div>

        <div>
            <label class="block font-semibold text-slate-600 mb-1 text-sm">Nominal (Rp)</label>
            <input type="number" name="nominal" value="{{ old('nominal') }}" required class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:border-[#D8A749]">
            @error('nominal') <span class="text-rose-500 text-xs">{{ $message }}</span> @enderror
        </div>

        <div>
            <label class="block font-semibold text-slate-600 mb-1 text-sm">Keterangan</label>
            <textarea name="keterangan" rows="3" class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:border-[#D8A749]">{{ old('keterangan') }}</textarea>
            @error('keterangan') <span class="text-rose-500 text-xs">{{ $message }}</span> @enderror
        </div>

        <div>
            <label class="block font-semibold text-slate-600 mb-1 text-sm">Bukti Transaksi (Bebas: HEIC, JPG, PNG, PDF, dll)</label>
            <input type="file" name="bukti_foto" class="w-full px-4 py-2 bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:border-[#D8A749]">
            @error('bukti_foto') <span class="text-rose-500 text-xs">{{ $message }}</span> @enderror
        </div>

        <div class="flex justify-end gap-3 pt-4">
            <a href="{{ route('mutasi.index') }}" class="px-5 py-2.5 bg-slate-200 font-bold text-slate-700 rounded-xl">Batal</a>
            <button type="submit" class="px-5 py-2.5 bg-[#D8A749] font-bold text-white rounded-xl shadow-sm">Simpan Mutasi</button>
        </div>
    </form>
</div>
@endsection