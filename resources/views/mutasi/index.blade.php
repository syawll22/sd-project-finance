@extends('layouts.app')

@section('title', 'Mutasi Transaksi - S&D Finance')

@section('content')
<!-- State Alpine.js untuk Modal Preview Bukti -->
<div x-data="{ showModal: false, imgUrl: '', titleModal: '' }" class="space-y-4 sm:space-y-6">

    <!-- Header Bar -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 sm:gap-4 bg-white p-4 sm:p-6 rounded-2xl sm:rounded-[28px] shadow-sm border border-gray-100/80">
        <div>
            <h1 class="text-xl sm:text-3xl font-extrabold text-[#1C2A24] tracking-tight">Mutasi Transaksi</h1>
            <p class="text-xs font-medium text-gray-500 mt-0.5">Riwayat lengkap pencatatan arus kas masuk, keluar, dan transfer.</p>
        </div>

        <a href="{{ route('mutasi.create') }}" 
           class="bg-[#D8A749] hover:bg-[#c4953c] text-white font-bold text-xs sm:text-sm py-2.5 px-5 sm:py-3 sm:px-6 rounded-xl sm:rounded-2xl shadow-md transition duration-200 flex items-center justify-center gap-2 w-full sm:w-auto shrink-0">
            <svg class="w-4 h-4 stroke-[2.5]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
            </svg>
            <span>Tambah Mutasi</span>
        </a>
    </div>

    <!-- Flash Alert -->
    @if(session('success'))
        <div class="p-4 bg-emerald-100 border border-emerald-300 text-emerald-800 text-xs sm:text-sm rounded-2xl flex items-center gap-2">
            <svg class="w-5 h-5 shrink-0 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            <span>{{ session('success') }}</span>
        </div>
    @endif

    <!-- FILTER BAR -->
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
                    <a href="{{ route('mutasi.index') }}" class="w-full text-center bg-gray-100 hover:bg-gray-200 text-gray-600 font-bold text-xs py-2.5 px-4 rounded-xl transition flex items-center justify-center gap-1.5">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                        </svg>
                        <span>Reset Filter</span>
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
                            <!-- Foto / File Bukti -->
                            <td class="py-3 px-4 text-center">
                                @if($item->bukti_foto)
                                    @php
                                        $extension = pathinfo($item->bukti_foto, PATHINFO_EXTENSION);
                                        $isImage = in_array(strtolower($extension), ['jpg', 'jpeg', 'png', 'webp', 'gif', 'svg']);
                                    @endphp

                                    @if($isImage)
                                        <button @click="showModal = true; imgUrl = '{{ asset('storage/' . $item->bukti_foto) }}'; titleModal = 'Bukti Transaksi - {{ $item->keterangan ?? 'Detail' }}'" 
                                                type="button" 
                                                title="Klik untuk Preview"
                                                class="focus:outline-none group relative inline-block">
                                            <img src="{{ asset('storage/' . $item->bukti_foto) }}" 
                                                 alt="Bukti" 
                                                 class="w-10 h-10 object-cover rounded-xl border border-gray-200 shadow-sm group-hover:scale-105 transition duration-200">
                                        </button>
                                    @else
                                        <a href="{{ asset('storage/' . $item->bukti_foto) }}" target="_blank" download title="Download File ({{ strtoupper($extension) }})"
                                           class="w-10 h-10 rounded-xl bg-amber-50 border border-amber-200/80 flex flex-col items-center justify-center text-[#D8A749] hover:bg-[#D8A749] hover:text-white transition duration-200 shadow-sm mx-auto group">
                                            <svg class="w-4 h-4 text-[#D8A749] group-hover:text-white transition" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                                            </svg>
                                            <span class="text-[7px] font-extrabold uppercase mt-0.5">{{ $extension }}</span>
                                        </a>
                                    @endif
                                @else
                                    <div class="w-10 h-10 rounded-xl bg-gray-50 border border-gray-200 flex flex-col items-center justify-center text-gray-300 text-[9px] font-bold mx-auto">
                                        <svg class="w-4 h-4 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                        </svg>
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
                                    <span class="bg-emerald-100 text-emerald-700 text-[10px] font-extrabold px-3 py-1 rounded-full uppercase tracking-wider inline-flex items-center gap-1">
                                        Pemasukan
                                    </span>
                                @elseif($item->jenis == 'keluar')
                                    <span class="bg-rose-100 text-rose-700 text-[10px] font-extrabold px-3 py-1 rounded-full uppercase tracking-wider inline-flex items-center gap-1">
                                        Pengeluaran
                                    </span>
                                @else
                                    <span class="bg-sky-100 text-sky-700 text-[10px] font-extrabold px-3 py-1 rounded-full uppercase tracking-wider inline-flex items-center gap-1">
                                        Transfer
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
                                <div class="flex items-center justify-center gap-1">
                                    <a href="{{ route('mutasi.edit', $item->id) }}" 
                                       class="p-2 text-gray-400 hover:text-[#D8A749] hover:bg-amber-50 rounded-lg transition" 
                                       title="Edit Data">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                        </svg>
                                    </a>
                                    <form action="{{ route('mutasi.destroy', $item->id) }}" method="POST" onsubmit="return confirm('Yakin mau hapus mutasi ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" 
                                                class="p-2 text-gray-400 hover:text-rose-600 hover:bg-rose-50 rounded-lg transition" 
                                                title="Hapus Data">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                            </svg>
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