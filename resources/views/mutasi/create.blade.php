@extends('layouts.app')

@section('title', 'Data Mutasi - S&D Finance')

@section('content')
<div class="space-y-6">
    
    <!-- Header Page -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-black text-slate-900 tracking-tight">Data Mutasi</h1>
            <p class="text-xs text-slate-500 mt-1">Daftar riwayat transaksi mutasi kas & bank.</p>
        </div>
        <a href="{{ route('mutasi.create') }}" class="bg-[#D8A749] hover:bg-amber-600 text-white font-bold text-xs px-5 py-3 rounded-2xl shadow-lg transition flex items-center justify-center gap-2 w-full sm:w-auto">
            <svg class="w-4 h-4 stroke-[2.5]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
            </svg>
            <span>Tambah Mutasi</span>
        </a>
    </div>

    <!-- Alert Success -->
    @if(session('success'))
        <div class="p-4 bg-emerald-100 border border-emerald-300 text-emerald-800 text-xs rounded-2xl font-bold flex items-center gap-2">
            <svg class="w-5 h-5 shrink-0 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            <span>{{ session('success') }}</span>
        </div>
    @endif

    <!-- Tabel Mutasi -->
    <div class="bg-white rounded-[2rem] shadow-sm border border-slate-100 overflow-hidden">
        <div class="overflow-x-auto w-full">
            <table class="w-full text-left text-xs min-w-[700px]">
                <thead class="bg-slate-50 text-slate-400 font-bold uppercase text-[10px] tracking-wider border-b border-slate-100">
                    <tr>
                        <th class="px-6 py-4">TANGGAL</th>
                        <th class="px-6 py-4">REKENING</th>
                        <th class="px-6 py-4">KATEGORI (COA)</th>
                        <th class="px-6 py-4 text-right">NOMINAL</th>
                        <th class="px-6 py-4">KETERANGAN</th>
                        <th class="px-6 py-4 text-center">BUKTI</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
                    <!-- FIX: Pake $mutasis (jamak) -->
                    @forelse($mutasis as $item)
                        <tr class="hover:bg-slate-50 transition">
                            <td class="px-6 py-4 font-bold text-slate-800 whitespace-nowrap">
                                {{ \Carbon\Carbon::parse($item->tanggal)->format('d M Y') }}
                            </td>
                            <td class="px-6 py-4 font-semibold text-slate-700 whitespace-nowrap">
                                {{ $item->rekening->nama_bank ?? $item->rekening->nama_rekening ?? '-' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-2.5 py-1 bg-amber-50 text-[#D8A749] font-bold rounded-lg text-[10px]">
                                    {{ $item->kategori->no_akun ?? '' }} {{ $item->kategori->nama_akun ?? '-' }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-right font-black text-slate-900 whitespace-nowrap">
                                Rp {{ number_format($item->nominal ?? 0, 0, ',', '.') }}
                            </td>
                            <td class="px-6 py-4 text-slate-500">
                                {{ $item->keterangan ?? '-' }}
                            </td>
                            <td class="px-6 py-4 text-center whitespace-nowrap">
                                @if($item->bukti_foto)
                                    <a href="{{ asset('storage/' . $item->bukti_foto) }}" target="_blank" class="inline-flex items-center gap-1 text-[#D8A749] hover:underline font-bold text-[11px]">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                        </svg>
                                        Lihat
                                    </a>
                                @else
                                    <span class="text-slate-300">-</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-8 text-center text-slate-400 font-medium">
                                Belum ada data mutasi transaksi.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>
@endsection