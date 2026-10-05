@extends('layouts.app')

@section('title', 'Kategori COA - S&D Finance')

@section('content')
<div class="space-y-6" x-data="{ openCreateModal: false, openEditModal: false, editData: {} }">
    
    <!-- Header Page -->
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-black text-slate-900 tracking-tight">Kategori COA</h1>
            <p class="text-xs text-slate-500 mt-1">Chart of Accounts untuk klasifikasi Pemasukan & Pengeluaran.</p>
        </div>
        <button @click="openCreateModal = true" class="bg-goldAccent hover:bg-amber-600 text-slate-900 font-bold text-xs px-5 py-3 rounded-2xl shadow-lg transition flex items-center gap-2">
            <span>+ Tambah Kategori</span>
        </button>
    </div>

    <!-- Alert Success -->
    @if(session('success'))
        <div class="p-4 bg-emerald-100 border border-emerald-300 text-emerald-800 text-xs rounded-2xl font-bold">
            {{ session('success') }}
        </div>
    @endif

    <!-- Tabel Kategori -->
    <div class="bg-white rounded-[2rem] shadow-sm border border-slate-100 overflow-hidden">
        <table class="w-full text-left text-xs">
            <thead class="bg-slate-50 text-slate-400 font-bold uppercase text-[10px] tracking-wider border-b border-slate-100">
                <tr>
                    <th class="px-6 py-4">Kode</th>
                    <th class="px-6 py-4">Nama Kategori</th>
                    <th class="px-6 py-4">Tipe COA</th>
                    <th class="px-6 py-4">Deskripsi</th>
                    <th class="px-6 py-4 text-center">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-50">
                @forelse($kategoris as $item)
                <tr class="hover:bg-slate-50 transition">
                    <td class="px-6 py-4 font-semibold text-slate-400">{{ $item->kode_kategori ?? '-' }}</td>
                    <td class="px-6 py-4 font-bold text-slate-800">{{ $item->nama_kategori }}</td>
                    <td class="px-6 py-4">
                        @if($item->tipe == 'masuk')
                            <span class="px-3 py-1 bg-emerald-100 text-emerald-800 font-extrabold text-[10px] rounded-full uppercase">Pemasukan</span>
                        @else
                            <span class="px-3 py-1 bg-rose-100 text-rose-800 font-extrabold text-[10px] rounded-full uppercase">Pengeluaran</span>
                        @endif
                    </td>
                    <td class="px-6 py-4 text-slate-500">{{ $item->deskripsi ?? '-' }}</td>
                    <td class="px-6 py-4 text-center">
                        <div class="flex items-center justify-center gap-2">
                            <button @click="openEditModal = true; editData = {{ json_encode($item) }}" class="p-2 text-amber-600 hover:bg-amber-50 rounded-xl transition" title="Edit">
                                ✏️
                            </button>
                            <form action="{{ route('kategori.destroy', $item->id) }}" method="POST" onsubmit="return confirm('Hapus kategori ini?')">
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
                    <td colspan="5" class="px-6 py-8 text-center text-slate-400">Belum ada data kategori COA.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- MODAL TAMBAH KATEGORI -->
    <div x-show="openCreateModal" class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/40 backdrop-blur-sm" x-cloak>
        <div class="bg-white rounded-[2rem] shadow-2xl w-full max-w-md p-6 space-y-4">
            <h3 class="text-base font-bold text-slate-900">Tambah Kategori Baru</h3>
            <form action="{{ route('kategori.store') }}" method="POST" class="space-y-3 text-xs">
                @csrf
                <div>
                    <label class="block font-semibold text-slate-600 mb-1">Kode Kategori (Opsional)</label>
                    <input type="text" name="kode_kategori" class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:border-goldAccent" placeholder="Misal: INC-01 / EXP-01">
                </div>
                <div>
                    <label class="block font-semibold text-slate-600 mb-1">Nama Kategori</label>
                    <input type="text" name="nama_kategori" class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:border-goldAccent" placeholder="Misal: Gaji Karyawan / Project Fee" required>
                </div>
                <div>
                    <label class="block font-semibold text-slate-600 mb-1">Tipe COA</label>
                    <select name="tipe" class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:border-goldAccent" required>
                        <option value="masuk">Pemasukan (Uang Masuk)</option>
                        <option value="keluar">Pengeluaran (Uang Keluar)</option>
                    </select>
                </div>
                <div>
                    <label class="block font-semibold text-slate-600 mb-1">Deskripsi</label>
                    <textarea name="deskripsi" class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:border-goldAccent" rows="2" placeholder="Keterangan singkat..."></textarea>
                </div>
                <div class="flex justify-end gap-2 pt-3">
                    <button type="button" @click="openCreateModal = false" class="px-4 py-2.5 bg-slate-100 text-slate-600 font-bold rounded-xl">Batal</button>
                    <button type="submit" class="px-5 py-2.5 bg-sidebar text-white font-bold rounded-xl hover:bg-sidebarHover">Simpan</button>
                </div>
            </form>
        </div>
    </div>

    <!-- MODAL EDIT KATEGORI -->
    <div x-show="openEditModal" class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/40 backdrop-blur-sm" x-cloak>
        <div class="bg-white rounded-[2rem] shadow-2xl w-full max-w-md p-6 space-y-4">
            <h3 class="text-base font-bold text-slate-900">Edit Kategori</h3>
            <form :action="'/kategori/' + editData.id" method="POST" class="space-y-3 text-xs">
                @csrf
                @method('PUT')
                <div>
                    <label class="block font-semibold text-slate-600 mb-1">Kode Kategori</label>
                    <input type="text" name="kode_kategori" x-model="editData.kode_kategori" class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:border-goldAccent">
                </div>
                <div>
                    <label class="block font-semibold text-slate-600 mb-1">Nama Kategori</label>
                    <input type="text" name="nama_kategori" x-model="editData.nama_kategori" class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:border-goldAccent" required>
                </div>
                <div>
                    <label class="block font-semibold text-slate-600 mb-1">Tipe COA</label>
                    <select name="tipe" x-model="editData.tipe" class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:border-goldAccent" required>
                        <option value="masuk">Pemasukan (Uang Masuk)</option>
                        <option value="keluar">Pengeluaran (Uang Keluar)</option>
                    </select>
                </div>
                <div>
                    <label class="block font-semibold text-slate-600 mb-1">Deskripsi</label>
                    <textarea name="deskripsi" x-model="editData.deskripsi" class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:border-goldAccent" rows="2"></textarea>
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