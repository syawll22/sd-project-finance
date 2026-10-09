@extends('layouts.app')

@section('title', 'Mutasi Transaksi - S&D Finance')

@section('content')
<div class="max-w-7xl mx-auto space-y-6 text-xs" x-data="{ modalOpen: false, activeFile: '', activeJurnal: '' }">
    
    <!-- Header Page -->
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-black text-slate-900 tracking-tight">Mutasi Transaksi</h1>
            <p class="text-xs text-slate-500 mt-1">Riwayat lengkap pencatatan jurnal umum sesuai laporan Excel.</p>
        </div>
        <a href="{{ route('mutasi.create') }}" class="px-4 py-2.5 bg-[#D8A749] hover:bg-[#c4953f] text-white font-bold rounded-xl transition shadow-sm flex items-center gap-2">
            <span>+ Tambah Mutasi</span>
        </a>
    </div>

    <!-- Filter Card -->
    <div class="bg-white rounded-[2rem] p-6 shadow-sm border border-slate-100">
        <form method="GET" action="{{ route('mutasi.index') }}" class="grid grid-cols-1 md:grid-cols-3 gap-4 items-end">
            <div>
                <label class="block font-semibold text-slate-600 mb-2">FILTER REKENING / BANK</label>
                <select name="rekening_id" class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:border-[#D8A749]">
                    <option value="">-- Semua Rekening / Bank --</option>
                    @foreach($rekenings as $rek)
                        <option value="{{ $rek->id }}" {{ request('rekening_id') == $rek->id ? 'selected' : '' }}>
                            {{ $rek->nama_bank ?? $rek->nama_rekening }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block font-semibold text-slate-600 mb-2">FILTER BULAN</label>
                <input type="month" name="bulan" value="{{ request('bulan') }}" class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:border-[#D8A749]">
            </div>
            <div class="flex gap-2">
                <button type="submit" class="px-5 py-2.5 bg-[#1C2A24] text-white font-bold rounded-xl transition hover:bg-slate-800">
                    Filter Data
                </button>
                <a href="{{ route('mutasi.index') }}" class="px-4 py-2.5 bg-slate-100 text-slate-600 font-bold rounded-xl transition hover:bg-slate-200">
                    Reset
                </a>
            </div>
        </form>
    </div>

    <!-- Table Card -->
    <div class="bg-white rounded-[2rem] shadow-sm border border-slate-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="border-b border-slate-100 text-[11px] font-bold text-slate-400 uppercase tracking-wider bg-slate-50/50">
                        <th class="px-6 py-4">Tanggal</th>
                        <th class="px-6 py-4">No. Jurnal</th>
                        <th class="px-6 py-4">Nama</th>
                        <th class="px-6 py-4">Sumber</th>
                        <th class="px-6 py-4">Keterangan</th>
                        <th class="px-6 py-4">No. COA</th>
                        <th class="px-6 py-4">Nama COA</th>
                        <th class="px-6 py-4 text-right">Debet (Masuk)</th>
                        <th class="px-6 py-4 text-right">Kredit (Keluar)</th>
                        <th class="px-6 py-4 text-center">Bukti</th>
                        <th class="px-6 py-4 text-center">Aksi</th>
                    </tr>
                </thead>
               <!-- Di bagian Tbody Tabel -->
            <tbody class="divide-y divide-slate-50 text-xs">
                @forelse($mutasis as $item)
                    <tr class="hover:bg-slate-50 transition">
                        <td class="px-6 py-4 font-bold text-slate-800 whitespace-nowrap">
                            {{ \Carbon\Carbon::parse($item->tanggal)->format('d-M-Y') }}
                        </td>
                        <td class="px-6 py-4 font-mono font-bold text-slate-600">
                            {{ $item->no_jurnal }}
                        </td>
                        <td class="px-6 py-4 font-semibold text-slate-700">
                            {{ $item->rekening->nama_rekening ?? '-' }}
                        </td>
                        <td class="px-6 py-4 text-slate-500">
                            {{ $item->rekening->nama_bank ?? 'Bank/Kas' }}
                        </td>
                        <td class="px-6 py-4 text-slate-600 max-w-xs truncate">
                            {{ $item->keterangan ?? '-' }}
                        </td>
                        <td class="px-6 py-4 font-bold text-slate-800">
                            {{ $item->kategori->no_akun ?? '-' }}
                        </td>
                        <td class="px-6 py-4 font-semibold text-slate-700">
                            {{ $item->kategori->nama_akun ?? '-' }}
                        </td>
                        
                       <!-- KOLOM DEBET (UANG MASUK) -->
                        <td class="px-6 py-4 font-black text-emerald-600 text-right whitespace-nowrap">
                            @if($item->jenis === 'masuk')
                                Rp {{ number_format($item->nominal ?? 0, 0, ',', '.') }}
                            @else
                                -
                            @endif
                        </td>

                        <!-- KOLOM KREDIT (UANG KELUAR / PINDAH) -->
                        <td class="px-6 py-4 font-black text-rose-600 text-right whitespace-nowrap">
                            @if($item->jenis === 'keluar' || $item->jenis === 'pindah')
                                Rp {{ number_format($item->nominal ?? 0, 0, ',', '.') }}
                                @if($item->jenis === 'pindah')
                                    <span class="text-[9px] text-slate-400 block font-normal">(Pindah)</span>
                                @endif
                            @else
                                -
                            @endif
                        </td>

<!-- TOMBOL LIHAT FOTO -->
<td class="px-6 py-4 text-center whitespace-nowrap">
    @if($item->bukti_foto)
        @php
            $cleanPath = str_replace(['public/', 'storage/'], '', $item->bukti_foto);
            $fileUrl = asset('storage/' . $cleanPath);
        @endphp
        <button type="button" @click="activeFile = '{{ $fileUrl }}'; activeJurnal = '{{ $item->no_jurnal }}'; modalOpen = true" class="px-2.5 py-1 bg-amber-50 text-amber-600 font-bold rounded-lg hover:bg-amber-100 transition">
            Lihat Foto
        </button>
    @else
        <span class="text-slate-300 italic">No File</span>
    @endif
</td>
                        
                        <td class="px-6 py-4 text-center whitespace-nowrap">
                            <div class="flex items-center justify-center gap-2">
                                <a href="{{ route('mutasi.edit', $item->id) }}" class="text-slate-400 hover:text-amber-600">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                </a>
                                <form action="{{ route('mutasi.destroy', $item->id) }}" method="POST" onsubmit="return confirm('Hapus data mutasi ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-slate-400 hover:text-rose-600">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="11" class="px-6 py-8 text-center text-slate-400">Belum ada data mutasi yang cocok dengan filter.</td>
                    </tr>
                @endforelse
            </tbody>
            </table>
        </div>
    </div>

    <!-- MODAL POP UP GAMBAR (YANG BENER & TIDAK BROKEN) -->
    <div x-show="modalOpen" class="fixed inset-0 z-50 flex items-center justify-center bg-black/75 p-4 backdrop-blur-sm" style="display: none;">
        <div @click.away="modalOpen = false" class="bg-white rounded-3xl max-w-3xl w-full p-6 relative shadow-2xl flex flex-col max-h-[90vh]">
            <div class="flex justify-between items-center mb-4 border-b pb-3">
                <h3 class="font-bold text-slate-800 text-base">Bukti Foto Transaksi - No Jurnal: <span x-text="activeJurnal" class="text-amber-600"></span></h3>
                <button type="button" @click="modalOpen = false" class="text-slate-400 hover:text-slate-700 font-bold text-2xl px-2 leading-none">&times;</button>
            </div>
            
            <div class="bg-slate-900 rounded-2xl p-3 flex items-center justify-center flex-1 overflow-hidden min-h-[400px]">
                <img :src="activeFile" class="max-h-[70vh] max-w-full object-contain rounded-xl mx-auto" alt="Bukti Transaksi">
            </div>
        </div>
    </div>

</div>
@endsection