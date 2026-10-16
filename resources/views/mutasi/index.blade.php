@extends('layouts.app')

@section('title', 'Mutasi Transaksi - S&D Finance')

@section('content')
<div
    class="max-w-7xl mx-auto space-y-6 pb-10"
    x-data="{
        modalOpen: false,
        activeFile: '',
        activeDownload: '',
        activeJurnal: '',
        imageError: false,
        openModal(file, download, jurnal) {
            this.activeFile = file;
            this.activeDownload = download;
            this.activeJurnal = jurnal;
            this.imageError = false;
            this.modalOpen = true;
        },
        closeModal() {
            this.modalOpen = false;
            this.activeFile = '';
            this.activeDownload = '';
            this.activeJurnal = '';
            this.imageError = false;
        }
    }"
    @keydown.escape.window="closeModal()"
>
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <p class="text-xs font-bold uppercase tracking-[0.2em] text-amber-600 mb-2">S&D Finance</p>
            <h1 class="text-2xl sm:text-3xl font-black text-slate-900 tracking-tight">Mutasi Transaksi</h1>
            <p class="text-sm text-slate-500 mt-2">Lihat dan kelola transaksi berdasarkan rekening.</p>
        </div>
        <a href="{{ route('mutasi.create') }}" class="inline-flex items-center justify-center px-5 py-3 bg-[#D8A749] hover:bg-[#c4953f] text-white text-sm font-bold rounded-xl transition shadow-sm whitespace-nowrap">+ Tambah Mutasi</a>
    </div>

    @if (session('success'))
        <div class="rounded-2xl border border-emerald-200 bg-emerald-50 px-5 py-4 text-sm font-medium text-emerald-700">{{ session('success') }}</div>
    @endif
    @if (session('error'))
        <div class="rounded-2xl border border-red-200 bg-red-50 px-5 py-4 text-sm font-medium text-red-700">{{ session('error') }}</div>
    @endif

    {{-- PILIH REKENING --}}
    <section class="bg-white rounded-2xl sm:rounded-3xl p-5 sm:p-6 shadow-sm border border-slate-200">
        <div class="flex flex-col sm:flex-row sm:items-end sm:justify-between gap-2 mb-4">
            <div>
                <h2 class="text-base font-bold text-slate-900">Pilih Rekening</h2>
                <p class="text-sm text-slate-500 mt-1">Tampilkan mutasi dari rekening tertentu atau gabungkan semuanya.</p>
            </div>
            <p class="text-xs text-slate-500">{{ $mutasis->count() }} transaksi ditampilkan</p>
        </div>

        <div class="flex flex-wrap gap-2">
            <a href="{{ route('mutasi.index', request()->except('rekening_id')) }}" class="inline-flex items-center px-4 py-2.5 rounded-xl border text-sm font-semibold transition {{ request()->filled('rekening_id') ? 'border-slate-200 bg-slate-50 text-slate-600 hover:bg-slate-100' : 'border-amber-300 bg-amber-50 text-amber-800' }}">Semua Rekening</a>
            @foreach ($rekenings as $rek)
                @php
                    $labelBank = $rek->nama_bank ?? 'Cash';
                    $labelRekening = $rek->nama_rekening ?? $labelBank;
                    $active = (string) request('rekening_id') === (string) $rek->id;
                @endphp
                <a href="{{ route('mutasi.index', array_merge(request()->except('rekening_id'), ['rekening_id' => $rek->id])) }}" class="inline-flex flex-col px-4 py-2.5 rounded-xl border text-left transition {{ $active ? 'border-amber-300 bg-amber-50 text-amber-800' : 'border-slate-200 bg-white text-slate-700 hover:border-amber-300 hover:bg-amber-50/50' }}">
                    <span class="text-sm font-semibold">{{ $labelRekening }}</span>                </a>
            @endforeach
        </div>
    </section>

    {{-- TABEL MUTASI --}}
    <section class="bg-white rounded-2xl sm:rounded-3xl shadow-sm border border-slate-200 overflow-hidden">
        <div class="flex items-center justify-between gap-3 px-5 sm:px-6 py-5 border-b border-slate-100">
            <div>
                <h2 class="text-base font-bold text-slate-900">Daftar Mutasi</h2>
                <p class="text-xs text-slate-500 mt-1">Bukti transaksi dapat dibuka atau diunduh dari kolom Bukti.</p>
            </div>
            <span class="text-xs font-semibold text-slate-500 bg-slate-100 rounded-lg px-3 py-2">{{ $mutasis->count() }} data</span>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse min-w-[1100px]">
                <thead>
                    <tr class="border-b border-slate-100 text-[11px] font-bold text-slate-500 uppercase tracking-wider bg-slate-50">
                        <th class="px-5 py-4">Tanggal</th>
                        <th class="px-5 py-4">No. Jurnal</th>
                        <th class="px-5 py-4">Rekening</th>
                        <th class="px-5 py-4">Bank / Sumber</th>
                        <th class="px-5 py-4">Keterangan</th>
                        <th class="px-5 py-4">No. COA</th>
                        <th class="px-5 py-4">Nama COA</th>
                        <th class="px-5 py-4 text-right">Debet (Masuk)</th>
                        <th class="px-5 py-4 text-right">Kredit (Keluar)</th>
                        <th class="px-5 py-4 text-center">Bukti</th>
                        <th class="px-5 py-4 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-sm">
                    @forelse ($mutasis as $item)
                        <tr class="hover:bg-amber-50/30 transition">
                            <td class="px-5 py-4 font-semibold text-slate-800 whitespace-nowrap">{{ $item->tanggal ? \Carbon\Carbon::parse($item->tanggal)->format('d M Y') : '-' }}</td>
                            <td class="px-5 py-4 font-mono text-xs font-bold text-slate-700 whitespace-nowrap">{{ $item->no_jurnal ?? '-' }}</td>
                            <td class="px-5 py-4 font-semibold text-slate-700">{{ $item->rekening->nama_rekening ?? '-' }}</td>
                            <td class="px-5 py-4 text-slate-500">{{ $item->rekening->nama_bank ?? 'Cash' }}</td>
                            <td class="px-5 py-4 text-slate-600 max-w-xs"><span class="block truncate" title="{{ $item->keterangan }}">{{ $item->keterangan ?: '-' }}</span></td>
                            <td class="px-5 py-4 font-bold text-slate-800 whitespace-nowrap">{{ $item->kategori->no_akun ?? '-' }}</td>
                            <td class="px-5 py-4 font-medium text-slate-700">{{ $item->kategori->nama_akun ?? '-' }}</td>
                            <td class="px-5 py-4 font-bold text-emerald-600 text-right whitespace-nowrap">@if ($item->jenis === 'masuk') Rp {{ number_format($item->nominal ?? 0, 0, ',', '.') }} @else <span class="text-slate-300">-</span> @endif</td>
                            <td class="px-5 py-4 font-bold text-rose-600 text-right whitespace-nowrap">@if (in_array($item->jenis, ['keluar', 'pindah'])) Rp {{ number_format($item->nominal ?? 0, 0, ',', '.') }} @if ($item->jenis === 'pindah')<span class="text-[10px] text-slate-400 block font-normal">Pindah buku</span>@endif @else <span class="text-slate-300">-</span> @endif</td>
                            <td class="px-5 py-4 text-center whitespace-nowrap">
                                @if ($item->bukti_foto)
                                    @php
                                        $fileUrl = route('mutasi.bukti', $item->id);
                                        $downloadUrl = route('mutasi.bukti', ['id' => $item->id, 'download' => 1]);
                                    @endphp
                                    <div class="flex items-center justify-center gap-2">
                                        <button type="button" x-on:click='openModal(@json($fileUrl), @json($downloadUrl), @json($item->no_jurnal ?? ""))' class="px-3 py-2 bg-amber-50 text-amber-800 font-semibold text-xs rounded-lg hover:bg-amber-100 transition">Lihat</button>
                                        <a href="{{ $downloadUrl }}" class="px-3 py-2 bg-slate-100 text-slate-700 font-semibold text-xs rounded-lg hover:bg-slate-200 transition">Unduh</a>
                                    </div>
                                @else
                                    <span class="text-xs text-slate-400 italic">Belum ada</span>
                                @endif
                            </td>
                            <td class="px-5 py-4 text-center whitespace-nowrap">
                                <div class="flex items-center justify-center gap-3">
                                    <a href="{{ route('mutasi.edit', $item->id) }}" class="text-slate-400 hover:text-amber-600" title="Edit mutasi" aria-label="Edit mutasi">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                    </a>
                                    <form action="{{ route('mutasi.destroy', $item->id) }}" method="POST" onsubmit="return confirm('Hapus data mutasi ini?')">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="text-slate-400 hover:text-rose-600" title="Hapus mutasi" aria-label="Hapus mutasi">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="11" class="px-6 py-12 text-center"><p class="text-sm font-semibold text-slate-700">Belum ada transaksi</p><p class="text-xs text-slate-500 mt-1">Coba ubah filter atau tambahkan mutasi baru.</p></td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>


    {{-- MODAL PREVIEW FOTO --}}
    <div
        x-show="modalOpen"
        x-cloak
        x-transition.opacity
        x-on:click.self="closeModal()"
        x-on:keydown.escape.window="closeModal()"
        class="fixed inset-0 z-[100] flex items-center justify-center bg-black/90 p-2 sm:p-4"
        style="display: none;"
        role="dialog"
        aria-modal="true"
        aria-label="Preview bukti transaksi"
    >
        <div class="relative w-full h-full max-w-7xl max-h-[95vh] flex flex-col">

            {{-- HEADER --}}
            <div class="flex items-center justify-between gap-4 px-4 py-3 text-white">
                <div class="min-w-0">
                    <h3 class="font-bold text-base sm:text-lg">
                        Bukti Transaksi
                    </h3>
                    <p
                        class="text-xs sm:text-sm text-slate-300 mt-1 truncate"
                        x-text="activeJurnal"
                    ></p>
                </div>

                <button
                    type="button"
                    x-on:click="closeModal()"
                    class="shrink-0 w-10 h-10 rounded-full bg-white/10 hover:bg-white/20 text-white text-2xl transition"
                    aria-label="Tutup preview"
                >
                    &times;
                </button>
            </div>

            {{-- AREA FOTO --}}
            <div class="flex-1 min-h-0 flex items-center justify-center overflow-hidden">
                <template x-if="activeFile && !imageError">
                    <img
                        :src="activeFile"
                        alt="Bukti transaksi"
                        class="max-w-full max-h-full object-contain rounded-lg"
                        x-on:error="imageError = true"
                    >
                </template>

                {{-- PESAN JIKA FORMAT TIDAK DIDUKUNG --}}
                <div
                    x-show="imageError"
                    class="max-w-md text-center px-6 py-8 text-white"
                >
                    <p class="text-lg font-bold">
                        Foto tidak bisa ditampilkan
                    </p>
                    <p class="text-sm text-slate-300 mt-2">
                        Format file mungkin tidak didukung browser.
                        File HEIC/HEIF, misalnya, tidak bisa ditampilkan langsung
                        di beberapa browser.
                    </p>
                </div>
            </div>

        </div>
    </div>

@endsection