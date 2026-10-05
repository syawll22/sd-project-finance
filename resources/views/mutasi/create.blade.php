@extends('layouts.app')

@section('title', 'Tambah Mutasi - S&D Finance')

@section('content')
<div class="flex justify-between items-center mb-6">
    <div>
        <h1 class="text-3xl font-extrabold text-[#1C2A24] tracking-tight">Tambah Mutasi</h1>
        <p class="text-xs font-medium text-gray-500 mt-1">Input data transaksi arus kas baru ke dalam sistem.</p>
    </div>
    <a href="{{ route('mutasi.index') }}" class="text-xs text-gray-500 hover:text-gray-800 font-bold bg-white px-4 py-2.5 rounded-xl border border-gray-200 transition">
        ← Kembali
    </a>
</div>

<div class="bg-white rounded-[28px] p-6 shadow-sm border border-gray-100/50 max-w-2xl">
    <!-- PENTING: wajib enctype="multipart/form-data" -->
    <form action="{{ route('mutasi.store') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
        @csrf
        
        <div>
            <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Tanggal</label>
            <input type="date" name="tanggal" value="{{ date('Y-m-d') }}" class="w-full bg-gray-50 border border-gray-200 rounded-xl p-3 text-sm focus:outline-none focus:border-[#D8A749]" required>
        </div>

        <div>
            <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Rekening / Kas</label>
            <select name="rekening_id" class="w-full bg-gray-50 border border-gray-200 rounded-xl p-3 text-sm focus:outline-none focus:border-[#D8A749]" required>
                <option value="">-- Pilih Rekening --</option>
                @foreach($rekenings as $rek)
                    <option value="{{ $rek->id }}">{{ $rek->nama_rekening ?? $rek->nama }}</option>
                @endforeach
            </select>
        </div>

        <div>
            <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Kategori COA</label>
            <select name="kategori_id" class="w-full bg-gray-50 border border-gray-200 rounded-xl p-3 text-sm focus:outline-none focus:border-[#D8A749]">
                <option value="">-- Tanpa Kategori / Umum --</option>
                @foreach($kategoris as $kat)
                    <option value="{{ $kat->id }}">{{ $kat->nama_kategori }}</option>
                @endforeach
            </select>
        </div>

        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Jenis Transaksi</label>
                <select name="jenis" class="w-full bg-gray-50 border border-gray-200 rounded-xl p-3 text-sm focus:outline-none focus:border-[#D8A749]" required>
                    <option value="masuk">Pemasukan (+)</option>
                    <option value="keluar">Pengeluaran (-)</option>
                    <option value="pindah">Transfer / Pindah</option>
                </select>
            </div>
            <div>
                <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Nominal (Rp)</label>
                <input type="number" name="nominal" placeholder="0" class="w-full bg-gray-50 border border-gray-200 rounded-xl p-3 text-sm focus:outline-none focus:border-[#D8A749]" required>
            </div>
        </div>

        <div>
            <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Keterangan</label>
            <textarea name="keterangan" rows="3" placeholder="Catatan tambahan..." class="w-full bg-gray-50 border border-gray-200 rounded-xl p-3 text-sm focus:outline-none focus:border-[#D8A749]"></textarea>
        </div>

        <!-- Input File Upload Foto Bukti -->
        <div>
            <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Bukti Struk / Foto (Opsional)</label>
            <input type="file" name="bukti_foto" class="w-full bg-gray-50 border border-gray-200 rounded-xl p-2.5 text-sm focus:outline-none focus:border-[#D8A749]">        </div>

        <button type="submit" class="w-full bg-[#D8A749] hover:bg-[#c4953c] text-white font-bold py-3.5 rounded-xl shadow-md transition duration-200 mt-2">
            Simpan Mutasi
        </button>
    </form>
</div>
@endsection