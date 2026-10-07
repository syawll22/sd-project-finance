@extends('layouts.app')

@section('title', 'Edit Mutasi - S&D Finance')

@section('content')
<div class="flex justify-between items-center mb-6">
    <div>
        <h1 class="text-3xl font-extrabold text-[#1C2A24] tracking-tight">Edit Mutasi</h1>
        <p class="text-xs font-medium text-gray-500 mt-1">Perbarui data transaksi mutasi ini.</p>
    </div>
    <a href="{{ route('mutasi.index') }}" class="text-xs text-gray-600 hover:text-gray-900 font-bold bg-white px-4 py-2.5 rounded-xl border border-gray-200 transition flex items-center gap-1.5 shadow-sm">
        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18" />
        </svg>
        <span>Kembali</span>
    </a>
</div>

<div class="bg-white rounded-[28px] p-6 shadow-sm border border-gray-100/50 max-w-2xl">
    <form action="{{ route('mutasi.update', $mutasi->id) }}" method="POST" enctype="multipart/form-data" class="space-y-4">
        @csrf
        @method('PUT')
        
        <div>
            <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Tanggal</label>
            <input type="date" name="tanggal" value="{{ old('tanggal', $mutasi->tanggal) }}" class="w-full bg-gray-50 border border-gray-200 rounded-xl p-3 text-sm focus:outline-none focus:border-[#D8A749]" required>
        </div>

        <div>
            <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Rekening / Kas</label>
            <select name="rekening_id" class="w-full bg-gray-50 border border-gray-200 rounded-xl p-3 text-sm focus:outline-none focus:border-[#D8A749]" required>
                @foreach($rekenings as $rek)
                    <option value="{{ $rek->id }}" {{ $mutasi->rekening_id == $rek->id ? 'selected' : '' }}>
                        {{ $rek->nama_rekening ?? $rek->nama }}
                    </option>
                @endforeach
            </select>
        </div>

        <div>
            <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Kategori COA</label>
            <select name="kategori_id" class="w-full bg-gray-50 border border-gray-200 rounded-xl p-3 text-sm focus:outline-none focus:border-[#D8A749]">
                <option value="">-- Tanpa Kategori / Umum --</option>
                @foreach($kategoris as $kat)
                    <option value="{{ $kat->id }}" {{ $mutasi->kategori_id == $kat->id ? 'selected' : '' }}>
                        {{ $kat->nama_kategori }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Jenis Transaksi</label>
                <select name="jenis" class="w-full bg-gray-50 border border-gray-200 rounded-xl p-3 text-sm focus:outline-none focus:border-[#D8A749]" required>
                    <option value="masuk" {{ $mutasi->jenis == 'masuk' ? 'selected' : '' }}>Pemasukan (+)</option>
                    <option value="keluar" {{ $mutasi->jenis == 'keluar' ? 'selected' : '' }}>Pengeluaran (-)</option>
                    <option value="pindah" {{ $mutasi->jenis == 'pindah' ? 'selected' : '' }}>Transfer / Pindah</option>
                </select>
            </div>
            <div>
                <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Nominal (Rp)</label>
                <input type="number" name="nominal" value="{{ old('nominal', $mutasi->nominal) }}" class="w-full bg-gray-50 border border-gray-200 rounded-xl p-3 text-sm focus:outline-none focus:border-[#D8A749]" required>
            </div>
        </div>

        <div>
            <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Keterangan</label>
            <textarea name="keterangan" rows="3" class="w-full bg-gray-50 border border-gray-200 rounded-xl p-3 text-sm focus:outline-none focus:border-[#D8A749]">{{ old('keterangan', $mutasi->keterangan) }}</textarea>
        </div>

        <!-- Preview Bukti Foto Lama & Input Baru -->
        <div>
            <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Bukti Struk / Foto</label>
            @if($mutasi->bukti_foto)
                <div class="mb-3 flex items-center gap-3 bg-gray-50 p-2.5 rounded-xl border border-gray-200">
                    <img src="{{ asset('storage/' . $mutasi->bukti_foto) }}" class="w-12 h-12 object-cover rounded-lg border border-gray-200 shadow-sm">
                    <span class="text-xs text-gray-500 font-medium">Foto bukti saat ini. Upload file baru di bawah jika ingin mengganti.</span>
                </div>
            @endif

            <input type="file" name="bukti_foto" class="w-full text-xs text-gray-500 bg-gray-50 border border-gray-200 rounded-xl p-2.5 focus:outline-none focus:border-[#D8A749] file:mr-4 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-bold file:bg-gray-200 file:text-gray-700 hover:file:bg-gray-300">
        </div>

        <button type="submit" class="w-full bg-[#D8A749] hover:bg-[#c4953c] text-white font-bold py-3.5 rounded-xl shadow-md transition duration-200 mt-2 flex items-center justify-center gap-2">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/>
            </svg>
            <span>Update Mutasi</span>
        </button>
    </form>
</div>
@endsection