@extends('layouts.app')

@section('title', 'Mutasi Transaksi - S&D Finance')

@section('content')
<div class="max-w-7xl mx-auto space-y-6 text-xs">
    
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
        <label class="block font-semibold text-slate-600 mb-2">FILTER REKENING / BANK</label>
        <select class="w-full md:w-1/3 px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:border-[#D8A749]">
            <option value="">-- Semua Rekening / Bank --</option>
        </select>
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
                        <th class="px-6 py-4 text-right">Debet</th>
                        <th class="px-6 py-4 text-right">Kredit</th>
                        <th class="px-6 py-4 text-center">Bukti</th>
                        <th class="px-6 py-4 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50 text-xs">
                    @forelse($mutasis as $item)
                        <tr class="hover:bg-slate-50 transition">
                            <td class="px-6 py-4 font-bold text-slate-800 whitespace-nowrap">
                                {{ \Carbon\Carbon::parse($item->tanggal)->format('d-M-Y') }}
                            </td>
                            <td class="px-6 py-4 font-mono font-bold text-slate-600">
                                {{ $item->no_jurnal ?? ('JRN-' . str_pad($item->id, 4, '0', STR_PAD_LEFT)) }}
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
                            <td class="px-6 py-4 font-black text-emerald-600 text-right whitespace-nowrap">
                                Rp {{ number_format($item->nominal ?? 0, 0, ',', '.') }}
                            </td>
                            <td class="px-6 py-4 text-right text-slate-400 whitespace-nowrap">
                                -
                            </td>
                            <td class="px-6 py-4 text-center whitespace-nowrap">
                                @if($item->bukti_foto)
                                    <a href="{{ asset('storage/' . $item->bukti_foto) }}" target="_blank" class="px-2.5 py-1 bg-amber-50 text-amber-600 font-bold rounded-lg hover:bg-amber-100 transition">
                                        Lihat
                                    </a>
                                @else
                                    <span class="text-slate-300">-</span>
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
                            <td colspan="11" class="px-6 py-8 text-center text-slate-400">Belum ada data mutasi.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>
@endsection