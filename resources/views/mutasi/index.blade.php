@extends('layouts.app')

@section('title', 'Mutasi Transaksi - S&D Finance')

@section('content')
<div x-data="{ showModal: false, imgUrl: '', titleModal: '' }" class="space-y-4 sm:space-y-6">

    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 sm:gap-4 bg-white p-4 sm:p-6 rounded-2xl sm:rounded-[28px] shadow-sm border border-gray-100/80">
        <div>
            <h1 class="text-xl sm:text-3xl font-extrabold text-[#1C2A24] tracking-tight">Mutasi Transaksi</h1>
            <p class="text-xs font-medium text-gray-500 mt-0.5">Riwayat lengkap pencatatan jurnal umum sesuai laporan Excel.</p>
        </div>

        <a href="{{ route('mutasi.create') }}" 
           class="bg-[#D8A749] hover:bg-[#c4953c] text-white font-bold text-xs sm:text-sm py-2.5 px-5 sm:py-3 sm:px-6 rounded-xl sm:rounded-2xl shadow-md transition duration-200 flex items-center justify-center gap-2 w-full sm:w-auto shrink-0">
            <svg class="w-4 h-4 stroke-[2.5]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
            </svg>
            <span>Tambah Mutasi</span>
        </a>
    </div>

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
            <div class="flex items-end">
                @if(request('rekening_id'))
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

    <!-- Table Card Container Presisi Excel -->
    <div class="bg-white rounded-2xl sm:rounded-[28px] p-3 sm:p-6 shadow-sm border border-gray-100/50 flex-1">
        <div class="overflow-x-auto w-full">
            <table class="w-full text-left min-w-[1000px]">
                <thead>
                    <tr class="text-[11px] font-bold text-gray-400 tracking-wider uppercase border-b border-gray-100">
                        <th class="pb-4 px-3">TANGGAL</th>
                        <th class="pb-4 px-3">NO. JURNAL</th>
                        <th class="pb-4 px-3">NAMA</th>
                        <th class="pb-4 px-3">SUMBER</th>
                        <th class="pb-4 px-3">KETERANGAN</th>
                        <th class="pb-4 px-3">NO. COA</th>
                        <th class="pb-4 px-3">NAMA COA</th>
                        <th class="pb-4 px-3 text-end">DEBET</th>
                        <th class="pb-4 px-3 text-end">KREDIT</th>
                        <th class="pb-4 px-3 text-center">AKSI</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50 text-xs sm:text-sm">
                    @forelse($mutasi as $item)
                        <tr class="hover:bg-gray-50/50 transition">
                            <td class="py-4 px-3 font-semibold text-gray-600 whitespace-nowrap">
                                {{ \Carbon\Carbon::parse($item->tanggal)->format('d-M-Y') }}
                            </td>
                            <td class="py-4 px-3 font-medium text-gray-700 whitespace-nowrap">
                                {{ $item->no_jurnal ?? '-' }}
                            </td>
                            <td class="py-4 px-3 font-medium text-gray-700 whitespace-nowrap">
                                {{ $item->nama ?? '-' }}
                            </td>
                            <td class="py-4 px-3 text-gray-600 whitespace-nowrap">
                                {{ $item->sumber ?? '-' }}
                            </td>
                            <td class="py-4 px-3 text-gray-500 max-w-xs truncate">
                                {{ $item->keterangan ?? '-' }}
                            </td>
                            <td class="py-4 px-3 font-bold text-[#1C2A24] whitespace-nowrap">
                                {{ $item->kategori->no_akun ?? $item->kategori->kode ?? '-' }}
                            </td>
                            <td class="py-4 px-3 font-medium text-gray-700 whitespace-nowrap">
                                {{ $item->kategori->nama_akun ?? $item->kategori->nama_kategori ?? '-' }}
                            </td>
                            <td class="py-4 px-3 text-end font-extrabold text-emerald-600 whitespace-nowrap">
                                {{ $item->debet > 0 ? 'Rp ' . number_format($item->debet, 0, ',', '.') : '-' }}
                            </td>
                            <td class="py-4 px-3 text-end font-extrabold text-rose-600 whitespace-nowrap">
                                {{ $item->kredit > 0 ? 'Rp ' . number_format($item->kredit, 0, ',', '.') : '-' }}
                            </td>
                            <td class="py-4 px-3 text-center whitespace-nowrap">
                                <div class="flex items-center justify-center gap-1">
                                    <a href="{{ route('mutasi.edit', $item->id) }}" class="p-1.5 text-gray-400 hover:text-[#D8A749] hover:bg-amber-50 rounded-lg transition" title="Edit">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                    </a>
                                    <form action="{{ route('mutasi.destroy', $item->id) }}" method="POST" onsubmit="return confirm('Hapus mutasi ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="p-1.5 text-gray-400 hover:text-rose-600 hover:bg-rose-50 rounded-lg transition" title="Hapus">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="10" class="text-center py-12 text-gray-400 font-medium">Belum ada data mutasi transaksi.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection