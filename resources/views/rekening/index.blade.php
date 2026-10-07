@extends('layouts.app')

@section('title', 'Rekening & Kas - S&D Finance')

@section('content')
<div class="space-y-6" x-data="{ openCreateModal: false, openEditModal: false, editData: {} }">
    
    <!-- Header Page -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-black text-slate-900 tracking-tight">Kelola Rekening & Kas</h1>
            <p class="text-xs text-slate-500 mt-1">Daftar akun bank & kas tunai operasional.</p>
        </div>
        <button @click="openCreateModal = true" class="bg-[#D8A749] hover:bg-amber-600 text-white font-bold text-xs px-5 py-3 rounded-2xl shadow-lg transition flex items-center justify-center gap-2 w-full sm:w-auto">
            <svg class="w-4 h-4 stroke-[2.5]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
            </svg>
            <span>Tambah Rekening</span>
        </button>
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

    <!-- Tabel Rekening -->
    <div class="bg-white rounded-[2rem] shadow-sm border border-slate-100 overflow-hidden">
        <div class="overflow-x-auto w-full">
            <table class="w-full text-left text-xs min-w-[640px]">
                <thead class="bg-slate-50 text-slate-400 font-bold uppercase text-[10px] tracking-wider border-b border-slate-100">
                    <tr>
                        <th class="px-6 py-4">Nama Rekening</th>
                        <th class="px-6 py-4">Saldo Saat Ini</th>
                        <th class="px-6 py-4 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
                    @forelse($rekening as $item)
                    <tr class="hover:bg-slate-50 transition">
                        <td class="px-6 py-4 font-bold text-slate-800 whitespace-nowrap">
                            {{ $item->nama_rekening ?? $item->nama }}
                        </td>
                        <td class="px-6 py-4 font-black text-emerald-600 whitespace-nowrap">
                            Rp {{ number_format($item->saldo ?? 0, 0, ',', '.') }}
                        </td>
                        <td class="px-6 py-4 text-center whitespace-nowrap">
                            <div class="flex items-center justify-center gap-1">
                                <button @click="openEditModal = true; editData = {{ json_encode($item) }}" 
                                        class="p-2 text-slate-400 hover:text-[#D8A749] hover:bg-amber-50 rounded-xl transition" 
                                        title="Edit">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                    </svg>
                                </button>
                                <form action="{{ route('rekening.destroy', $item->id) }}" method="POST" onsubmit="return confirm('Hapus rekening ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" 
                                            class="p-2 text-slate-400 hover:text-rose-600 hover:bg-rose-50 rounded-xl transition" 
                                            title="Hapus">
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
                        <td colspan="3" class="px-6 py-8 text-center text-slate-400">Belum ada data rekening.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- MODAL TAMBAH REKENING -->
    <div x-show="openCreateModal" class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/40 backdrop-blur-sm p-4" x-cloak style="display: none;">
        <div @click.away="openCreateModal = false" class="bg-white rounded-[2rem] shadow-2xl w-full max-w-md p-6 space-y-4">
            <h3 class="text-base font-bold text-slate-900">Tambah Rekening Baru</h3>
            <form action="{{ route('rekening.store') }}" method="POST" class="space-y-3 text-xs">
                @csrf
                <div>
                    <label class="block font-semibold text-slate-600 mb-1">Nama Rekening / Bank</label>
                    <input type="text" name="nama_rekening" class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:border-[#D8A749]" placeholder="Contoh: Bank BCA / Kas Tunai" required>
                </div>
                <div>
                    <label class="block font-semibold text-slate-600 mb-1">Saldo Awal (Rp) <span class="text-gray-400 font-normal">(Opsional)</span></label>
                    <input type="number" name="saldo" class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:border-[#D8A749]" placeholder="0">
                </div>
                <div class="flex justify-end gap-2 pt-3">
                    <button type="button" @click="openCreateModal = false" class="px-4 py-2.5 bg-slate-100 text-slate-600 font-bold rounded-xl transition">Batal</button>
                    <button type="submit" class="px-5 py-2.5 bg-[#1C2A24] text-white font-bold rounded-xl hover:bg-[#2c3e36] transition">Simpan</button>
                </div>
            </form>
        </div>
    </div>

    <!-- MODAL EDIT REKENING -->
    <div x-show="openEditModal" class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/40 backdrop-blur-sm p-4" x-cloak style="display: none;">
        <div @click.away="openEditModal = false" class="bg-white rounded-[2rem] shadow-2xl w-full max-w-md p-6 space-y-4">
            <h3 class="text-base font-bold text-slate-900">Edit Rekening</h3>
            <form :action="'/rekening/' + editData.id" method="POST" class="space-y-3 text-xs">
                @csrf
                @method('PUT')
                <div>
                    <label class="block font-semibold text-slate-600 mb-1">Nama Rekening / Bank</label>
                    <input type="text" name="nama_rekening" x-model="editData.nama_rekening" class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:border-[#D8A749]" required>
                </div>
                <div>
                    <label class="block font-semibold text-slate-600 mb-1">Saldo (Rp) <span class="text-gray-400 font-normal">(Opsional)</span></label>
                    <input type="number" name="saldo" x-model="editData.saldo" class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:border-[#D8A749]">
                </div>
                <div class="flex justify-end gap-2 pt-3">
                    <button type="button" @click="openEditModal = false" class="px-4 py-2.5 bg-slate-100 text-slate-600 font-bold rounded-xl transition">Batal</button>
                    <button type="submit" class="px-5 py-2.5 bg-[#D8A749] text-white font-bold rounded-xl hover:bg-amber-600 transition">Update</button>
                </div>
            </form>
        </div>
    </div>

</div>
@endsection