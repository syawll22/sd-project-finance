@extends('layouts.app')

@section('title', 'Mutasi Transaksi - S&D Finance')

@section('content')
<!-- Header Bar -->
<div class="flex justify-between items-start mb-8">
    <div>
        <h1 class="text-3xl font-extrabold text-[#1C2A24] tracking-tight">Mutasi Transaksi</h1>
        <p class="text-xs font-medium text-gray-500 mt-1">Riwayat lengkap pencatatan arus kas masuk, keluar, dan transfer.</p>
    </div>

    <a href="{{ route('mutasi.create') }}" 
       class="bg-[#D8A749] hover:bg-[#c4953c] text-white font-bold text-xs py-3 px-6 rounded-2xl shadow-md transition duration-200 flex items-center gap-1">
        + Tambah Mutasi
    </a>
</div>

<!-- Flash Alert -->
@if(session('success'))
    <div class="mb-6 p-4 bg-emerald-100 border border-emerald-300 text-emerald-800 text-sm rounded-2xl">
        {{ session('success') }}
    </div>
@endif

<!-- Table Card Container -->
<div class="bg-white rounded-[28px] p-6 shadow-sm border border-gray-100/50 flex-1">
    <div class="overflow-x-auto">
        <table class="w-full text-left">
            <thead>
                <tr class="text-[11px] font-bold text-gray-400 tracking-wider uppercase border-b border-gray-100">
                    <th class="pb-4 px-4">BUKTI</th>
                    <th class="pb-4 px-4">TANGGAL</th>
                    <th class="pb-4 px-4">REKENING</th>
                    <th class="pb-4 px-4">KATEGORI</th>
                    <th class="pb-4 px-4">JENIS</th>
                    <th class="pb-4 px-4">NOMINAL</th>
                    <th class="pb-4 px-4">KETERANGAN</th>
                    <th class="pb-4 px-4 text-center">AKSI</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50 text-sm">
                @forelse($mutasi as $item)
                    <tr class="hover:bg-gray-50/50 transition">
                        <!-- Foto Bukti -->
                        <td class="py-3 px-4">
                            @if($item->bukti_foto)
                                @php
                                    $extension = pathinfo($item->bukti_foto, PATHINFO_EXTENSION);
                                    $isImage = in_array(strtolower($extension), ['jpg', 'jpeg', 'png', 'webp', 'gif', 'svg']);
                                @endphp

                                @if($isImage)
                                    <!-- Jika berupa Foto/Gambar -->
                                    <a href="{{ asset('storage/' . $item->bukti_foto) }}" target="_blank" title="Lihat Gambar">
                                        <img src="{{ asset('storage/' . $item->bukti_foto) }}" 
                                            alt="Bukti" 
                                            class="w-10 h-10 object-cover rounded-xl border border-gray-200 shadow-sm hover:scale-110 transition duration-200">
                                    </a>
                                @else
                                    <!-- Jika berupa Dokumen Lain (PDF, DOCX, XLSX, dll) -->
                                    <a href="{{ asset('storage/' . $item->bukti_foto) }}" target="_blank" download title="Download File ({{ strtoupper($extension) }})"
                                    class="w-10 h-10 rounded-xl bg-amber-50 border border-amber-200 flex flex-col items-center justify-center text-[#D8A749] hover:bg-[#D8A749] hover:text-white transition duration-200 shadow-sm">
                                        <span class="text-[14px]">📄</span>
                                        <span class="text-[8px] font-extrabold uppercase">{{ $extension }}</span>
                                    </a>
                                @endif
                            @else
                                <div class="w-10 h-10 rounded-xl bg-gray-100 border border-gray-200 flex items-center justify-center text-gray-300 text-[10px] font-bold">
                                    NO FILE
                                </div>
                            @endif
                        </td>
</td>
                        <td class="py-4 px-4 font-semibold text-gray-600">
                            {{ \Carbon\Carbon::parse($item->tanggal ?? $item->created_at)->format('d/m/Y') }}
                        </td>
                        <td class="py-4 px-4 font-bold text-[#1C2A24]">
                            {{ $item->rekening->nama_rekening ?? $item->rekening->nama ?? '-' }}
                        </td>
                        <td class="py-4 px-4 text-gray-600 font-medium">
                            {{ $item->kategori->nama_kategori ?? '-' }}
                        </td>
                        <td class="py-4 px-4">
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
                        <td class="py-4 px-4 font-extrabold {{ $item->jenis == 'masuk' ? 'text-emerald-600' : ($item->jenis == 'keluar' ? 'text-rose-600' : 'text-sky-600') }}">
                            {{ $item->jenis == 'keluar' ? '-' : '+' }} Rp {{ number_format($item->nominal, 0, ',', '.') }}
                        </td>
                        <td class="py-4 px-4 text-gray-400 text-xs font-medium">
                            {{ $item->keterangan ?? '-' }}
                        </td>
                        <!-- Aksi Edit & Delete -->
                        <td class="py-4 px-4 text-center">
                            <div class="flex items-center justify-center gap-2">
                                <a href="{{ route('mutasi.edit', $item->id) }}" class="p-1.5 text-gray-400 hover:text-[#D8A749] transition">
                                    ✏️
                                </a>
                                <form action="{{ route('mutasi.destroy', $item->id) }}" method="POST" onsubmit="return confirm('Yakin mau hapus mutasi ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="p-1.5 text-gray-400 hover:text-rose-600 transition">
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
@endsection