@extends('layouts.app')

@section('title', 'Rekening & Kas - S&D Finance')

@section('content')
<div class="space-y-6" x-data="{ openCreateModal: false, openEditModal: false, editData: {} }">
    
    <!-- Header Page (Responsive) -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-black text-slate-900 tracking-tight">Kelola Rekening & Kas</h1>
            <p class="text-xs text-slate-500 mt-1">Daftar akun bank & kas tunai operasional.</p>
        </div>
        <button @click="openCreateModal = true" class="bg-goldAccent hover:bg-amber-600 text-slate-900 font-bold text-xs px-5 py-3 rounded-2xl shadow-lg transition flex items-center justify-center gap-2 w-full sm:w-auto">
            <span>+ Tambah Rekening</span>
        </button>
    </div>

    <!-- Alert Success -->
    @if(session('success'))
        <div class="p-4 bg-emerald-100 border border-emerald-300 text-emerald-800 text-xs rounded-2xl font-bold">
            {{ session('success') }}
        </div>
    @endif

    <!-- Tabel Rekening (Responsive Wrapper) -->
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
                        <td class="px-6 py-4 font-bold text-slate-800 whitespace-nowrap">{{ $item->nama_rekening }}</td>
                        <td class="px-6 py-4 font-black text-slate-900 whitespace-nowrap">
                            Rp {{ number_format($item->saldo, 0, ',', '.') }}
                        </td>
                        <td class="px-6 py-4 text-center whitespace-nowrap">
                            <div class="flex items-center justify-center gap-2">
                                <button @click="openEditModal = true; editData = {{ json_encode($item) }}" class="p-2 text-amber-600 hover:bg-amber-50 rounded-xl transition" title="Edit">
                                    ✏️
                                </button>
                                <form action="{{ route('rekening.destroy', $item->id) }}" method="POST" onsubmit="return confirm('Hapus rekening ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="p-2 text-rose-600 hover:bg-rose-50 rounded-xl transition" title="Hapus">
                                        🗑️
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-6 py-8 text-center text-slate-400">Belum ada data rekening.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- MODAL TAMBAH REKENING -->
    <div x-show="openCreateModal" class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/40 backdrop-blur-sm p-4" x-cloak>
        <div class="bg-white rounded-[2rem] shadow-2xl w-full max-w-md p-6 space-y-4">
            <h3 class="text-base font-bold text-slate-900">Tambah Rekening Baru</h3>
            <form action="{{ route('rekening.store') }}" method="POST" class="space-y-3 text-xs">
                @csrf
                <div>
                    <label class="block font-semibold text-slate-600 mb-1">Nama Rekening / Bank</label>
                    <input type="text" name="nama_rekening" class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:border-goldAccent" placeholder="Contoh: Bank BCA / Kas Tunai" required>
                </div>
                <div>
                    <label class="block font-semibold text-slate-600 mb-1">Nomor Rekening</label>
                    <input type="text" name="nomor_rekening" class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:border-goldAccent" placeholder="1234567890">
                </div>
                <div>
                    <label class="block font-semibold text-slate-600 mb-1">Atas Nama</label>
                    <input type="text" name="atas_nama" class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:border-goldAccent" placeholder="PT S&D Finance">
                </div>
                <div>
                    <label class="block font-semibold text-slate-600 mb-1">Saldo Awal (Rp)</label>
                    <input type="number" name="saldo" class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:border-goldAccent" placeholder="0" required>
                </div>
                <div class="flex justify-end gap-2 pt-3">
                    <button type="button" @click="openCreateModal = false" class="px-4 py-2.5 bg-slate-100 text-slate-600 font-bold rounded-xl">Batal</button>
                    <button type="submit" class="px-5 py-2.5 bg-sidebar text-white font-bold rounded-xl hover:bg-sidebarHover">Simpan</button>
                </div>
            </form>
        </div>
    </div>

    <!-- MODAL EDIT REKENING -->
    <div x-show="openEditModal" class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/40 backdrop-blur-sm p-4" x-cloak>
        <div class="bg-white rounded-[2rem] shadow-2xl w-full max-w-md p-6 space-y-4">
            <h3 class="text-base font-bold text-slate-900">Edit Rekening</h3>
            <form :action="'/rekening/' + editData.id" method="POST" class="space-y-3 text-xs">
                @csrf
                @method('PUT')
                <div>
                    <label class="block font-semibold text-slate-600 mb-1">Nama Rekening / Bank</label>
                    <input type="text" name="nama_rekening" x-model="editData.nama_rekening" class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:border-goldAccent" required>
                </div>
                <div>
                    <label class="block font-semibold text-slate-600 mb-1">Nomor Rekening</label>
                    <input type="text" name="nomor_rekening" x-model="editData.nomor_rekening" class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:border-goldAccent">
                </div>
                <div>
                    <label class="block font-semibold text-slate-600 mb-1">Atas Nama</label>
                    <input type="text" name="atas_nama" x-model="editData.atas_nama" class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:border-goldAccent">
                </div>
                <div>
                    <label class="block font-semibold text-slate-600 mb-1">Saldo (Rp)</label>
                    <input type="number" name="saldo" x-model="editData.saldo" class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:border-goldAccent" required>
                </div>
                <div class="flex justify-end gap-2 pt-3">
                    <button type="button" @click="openEditModal = false" class="px-4 py-2.5 bg-slate-100 text-slate-600 font-bold rounded-xl">Batal</button>
                    <button type="submit" class="px-5 py-2.5 bg-amber-600 text-white font-bold rounded-xl hover:bg-amber-700">Update</button>
                </div>
            </form>
        </div>
    </div>

</div>
@endsection