@extends('layouts.app')

@section('title', 'Mutasi Transaksi - S&D Finance')

@section('content')
<!-- State Alpine.js untuk Modal Preview Bukti -->
<div x-data="{ showModal: false, imgUrl: '', titleModal: '' }" class="space-y-4 sm:space-y-6">

    <!-- Header Bar (Responsif Stack di HP) -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 sm:gap-4 bg-white p-4 sm:p-6 rounded-2xl sm:rounded-[28px] shadow-sm border border-gray-100/80">
        <div>
            <h1 class="text-xl sm:text-3xl font-extrabold text-[#1C2A24] tracking-tight">Mutasi Transaksi</h1>
            <p class="text-xs font-medium text-gray-500 mt-0.5">Riwayat lengkap pencatatan arus kas masuk, keluar, dan transfer.</p>
        </div>

        <a href="{{ route('mutasi.create') }}" 
           class="bg-[#D8A749] hover:bg-[#c4953c] text-white font-bold text-xs sm:text-sm py-2.5 px-5 sm:py-3 sm:px-6 rounded-xl sm:rounded-2xl shadow-md transition duration-200 flex items-center justify-center gap-1.5 w-full sm:w-auto shrink-0">
            <span>+ Tambah Mutasi</span>
        </a>
    </div>

    <!-- Flash Alert -->
    @if(session('success'))
        <div class="p-4 bg-emerald-100 border border-emerald-300 text-emerald-800 text-xs sm:text-sm rounded-2xl">
            {{ session('success') }}
        </div>
    @endif

    <!-- FILTER BAR (Filter per Rekening/Bank & Jenis) -->
    <div class="bg-white p-4 rounded-2xl border border-gray-100/80 shadow-sm">
        <form method="GET" action="{{ route('mutasi.index') }}" class="grid grid-cols-1 sm:grid-cols-3 gap-3">
            <!-- Filter Bank / Rekening -->
            <div>
                <label class="block text-[11px] font-bold text-gray-400 uppercase tracking-wider mb-1">Filter Rekening / Bank</label>
                <select name="rekening_id" onchange="this.form.submit()" class="w-full text-xs font-semibold text-gray-700 bg-gray-50 border border-gray-200 rounded-xl p-2.5 focus:outline-none focus:border-[#D8A749]">
                    <option value="">-- Semua Rekening / Bank --</option>
                    @foreach($rekenings ?? [] as $rek)
                        <option value="{{ $rek->id }}" {{ request('rekening_id') == $rek->id ? 'selected' : '' }}>
                            {{ $rek->nama_rekening ?? $rek->nama }}
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Filter Jenis Mutasi -->
            <div>
                <label class="block text-[11px] font-bold text-gray-400 uppercase tracking-wider mb-1">Filter Jenis</label>
                <select name="jenis" onchange="this.form.submit()" class="w-full text-xs font-semibold text-gray-700 bg-gray-50 border border-gray-200 rounded-xl p-2.5 focus:outline-none focus:border-[#D8A749]">
                    <option value="">-- Semua Jenis --</option>
                    <option value="masuk" {{ request('jenis') == 'masuk' ? 'selected' : '' }}>Pemasukan</option>
                    <option value="keluar" {{ request('jenis') == 'keluar' ? 'selected' : '' }}>Pengeluaran</option>
                    <option value="transfer" {{ request('jenis') == 'transfer' ? 'selected' : '' }}>Transfer</option>
                </select>
            </div>

            <!-- Reset Filter Button -->
            <div class="flex items-end">
                @if(request('rekening_id') || request('jenis'))
                    <a href="{{ route('mutasi.index') }}" class="w-full text-center bg-gray-100 hover:bg-gray-200 text-gray-600 font-bold text-xs py-2.5 px-4 rounded-xl transition">
                        Reset Filter
                    </a>
                @endif
            </div>
        </form>
    </div>

    <!-- Table Card Container -->
    <div class="bg-white rounded-2xl sm:rounded-[28px] p-3 sm:p-6 shadow-sm border border-gray-100/50 flex-1">
        <div class="overflow-x-auto w-full">
            <table class="w-full text-left min-w-[850px]">
                <thead>
                    <tr class="text-[11px] font-bold text-gray-400 tracking-wider uppercase border-b border-gray-100">
                        <th class="pb-4 px-4 text-center">BUKTI</th>
                        <th class="pb-4 px-4">TANGGAL</th>
                        <th class="pb-4 px-4">REKENING</th>
                        <th class="pb-4 px-4">KATEGORI</th>
                        <th class="pb-4 px-4">JENIS</th>
                        <th class="pb-4 px-4">NOMINAL</th>
                        <th class="pb-4 px-4">KETERANGAN</th>
                        <th class="pb-4 px-4 text-center">AKSI</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50 text-xs sm:text-sm">
                    @forelse($mutasi as $item)
                        <tr class="hover:bg-gray-50/50 transition">
                            <!-- Foto / File Bukti dengan Modal Pop-up -->
                            <td class="py-3 px-4 text-center">
                                @if($item->bukti_foto)
                                    @php
                                        $extension = pathinfo($item->bukti_foto, PATHINFO_EXTENSION);
                                        $isImage = in_array(strtolower($extension), ['jpg', 'jpeg', 'png', 'webp', 'gif', 'svg']);
                                    @endphp

                                    @if($isImage)
                                        <!-- Buka Modal Pop-up kalo Gambar -->
                                        <button @click="showModal = true; imgUrl = '{{ asset('storage/' . $item->bukti_foto) }}'; titleModal = 'Bukti Transaksi - {{ $item->keterangan ?? 'Detail' }}'" 
                                                type="button" 
                                                title="Klik untuk Preview"
                                                class="focus:outline-none group">
                                            <img src="{{ asset('storage/' . $item->bukti_foto) }}" 
                                                 alt="Bukti" 
                                                 class="w-10 h-10 object-cover rounded-xl border border-gray-200 shadow-sm group-hover:scale-105 transition duration-200">
                                        </button>
                                    @else
                                        <!-- Download kalo PDF / Dokumen -->
                                        <a href="{{ asset('storage/' . $item->bukti_foto) }}" target="_blank" download title="Download File ({{ strtoupper($extension) }})"
                                           class="w-10 h-10 rounded-xl bg-amber-50 border border-amber-200 flex flex-col items-center justify-center text-[#D8A749] hover:bg-[#D8A749] hover:text-white transition duration-200 shadow-sm">
                                            <span class="text-[12px]">📄</span>
                                            <span class="text-[7px] font-extrabold uppercase">{{ $extension }}</span>
                                        </a>
                                    @endif
                                @else
                                    <div class="w-10 h-10 rounded-xl bg-gray-100 border border-gray-200 flex items-center justify-center text-gray-300 text-[9px] font-bold mx-auto">
                                        NO FILE
                                    </div>
                                @endif
                            </td>

                            <td class="py-4 px-4 font-semibold text-gray-600 whitespace-nowrap">
                                {{ \Carbon\Carbon::parse($item->tanggal ?? $item->created_at)->format('d/m/Y') }}
                            </td>
                            <td class="py-4 px-4 font-bold text-[#1C2A24] whitespace-nowrap">
                                {{ $item->rekening->nama_rekening ?? $item->rekening->nama ?? '-' }}
                            </td>
                            <td class="py-4 px-4 text-gray-600 font-medium whitespace-nowrap">
                                {{ $item->kategori->nama_kategori ?? $item->kategori->nama ?? '-' }}
                            </td>
                            <td class="py-4 px-4 whitespace-nowrap">
                                @if($item->jenis == 'masuk')
                                    <span class="bg-emerald-100 text-emerald-700 text-[10px] font-extrabold px-3 py-1 rounded-full uppercase tracking-wider">
                                        PEMASUKAN
                                    </span>
                                @elseif($item->jenis == 'keluar')
                                    <span class="bg-rose-100 text-rose-700 text-[10px] font-extrabold px-3 py-1 rounded-full uppercase tracking-wider">
                                        PENGELUARAN
                                    </span>
                                @else
                                    <span class="bg-sky-100 text-sky-700 text-[10px] font-extrabold px-3 py-1 rounded-full uppercase tracking-wider">
                                        TRANSFER
                                    </span>
                                @endif
                            </td>
                            <td class="py-4 px-4 font-extrabold whitespace-nowrap {{ $item->jenis == 'masuk' ? 'text-emerald-600' : ($item->jenis == 'keluar' ? 'text-rose-600' : 'text-sky-600') }}">
                                {{ $item->jenis == 'keluar' ? '-' : '+' }} Rp {{ number_format($item->nominal, 0, ',', '.') }}
                            </td>
                            <td class="py-4 px-4 text-gray-500 text-xs font-medium max-w-xs truncate">
                                {{ $item->keterangan ?? '-' }}
                            </td>
                            <!-- Aksi Edit & Delete -->
                            <td class="py-4 px-4 text-center whitespace-nowrap">
                                <div class="flex items-center justify-center gap-2">
                                    <a href="{{ route('mutasi.edit', $item->id) }}" class="p-1.5 text-gray-400 hover:text-[#D8A749] transition" title="Edit">
                                        ✏️
                                    </a>
                                    <form action="{{ route('mutasi.destroy', $item->id) }}" method="POST" onsubmit="return confirm('Yakin mau hapus mutasi ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="p-1.5 text-gray-400 hover:text-rose-600 transition" title="Hapus">
                                            🗑️
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center py-12 text-gray-400 font-medium">
                                Belum ada data mutasi transaksi.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- MODAL POP-UP PREVIEW BUKTI -->
    <div x-show="showModal" 
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/70 backdrop-blur-sm"
         style="display: none;">
        
        <div @click.away="showModal = false" 
             class="bg-white w-full max-w-lg rounded-2xl sm:rounded-3xl shadow-2xl overflow-hidden border border-slate-100 flex flex-col max-h-[90vh]">
            
            <!-- Modal Header -->
            <div class="flex items-center justify-between p-4 sm:p-5 border-b border-slate-100">
                <h3 class="font-bold text-sm sm:text-base text-[#1C2A24] truncate" x-text="titleModal">Preview Bukti</h3>
                <button @click="showModal = false" class="text-slate-400 hover:text-slate-600 p-1 rounded-lg hover:bg-slate-100 transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>

            <!-- Modal Body (Preview Gambar) -->
            <div class="p-4 sm:p-6 overflow-y-auto flex items-center justify-center bg-slate-50 min-h-[250px]">
                <img :src="imgUrl" alt="Bukti Transaksi" class="max-w-full max-h-[60vh] object-contain rounded-xl shadow-md border border-slate-200">
            </div>

            <!-- Modal Footer -->
            <div class="p-4 bg-white border-t border-slate-100 flex justify-end">
                <button @click="showModal = false" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs rounded-xl transition">
                    Tutup
                </button>
            </div>
        </div>
    </div>

</div>
@endsection