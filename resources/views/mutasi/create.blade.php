@extends('layouts.app')

@section('title', 'Tambah Mutasi Transaksi - S&D Finance')

@section('content')
<div class="max-w-4xl mx-auto space-y-6 text-xs" 
     x-data="{ 
         namaBank: '', 
         jenis: 'keluar', 
         tanggal: '{{ date('Y-m-d') }}',
         get noJurnalAuto() {
             if (!this.namaBank) return 'Pilih rekening & bank terlebih dahulu...';
             let kodeJenis = (this.jenis === 'masuk') ? 'KM' : 'KK';
             let d = new Date(this.tanggal);
             if (isNaN(d)) return '';
             let yy = String(d.getFullYear()).slice(-2);
             let mm = String(d.getMonth() + 1).padStart(2, '0');
             let ym = yy + mm;
             // Format preview live (nomor urut otomatis di-handle di backend saat submit)
             return `${this.namaBank}/${kodeJenis}-${ym}001`;
         }
     }">

    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-black text-slate-900 tracking-tight">Tambah Mutasi Transaksi</h1>
            <p class="text-xs text-slate-500 mt-1">Form pencatatan transaksi kas masuk, kas keluar, dan pindah buku.</p>
        </div>
        <a href="{{ route('mutasi.index') }}" class="px-4 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-600 font-bold rounded-xl transition">
            &larr; Kembali
        </a>
    </div>

    <form action="{{ route('mutasi.store') }}" method="POST" enctype="multipart/form-data" class="bg-white rounded-[2rem] p-8 shadow-sm border border-slate-100 space-y-6">
        @csrf

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- TANGGAL TRANSAKSI -->
            <div>
                <label class="block font-semibold text-slate-600 mb-2">TANGGAL TRANSAKSI</label>
                <input type="date" name="tanggal" x-model="tanggal" class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:border-[#D8A749]" required>
            </div>

            <!-- REKENING / BANK -->
            <div>
                <label class="block font-semibold text-slate-600 mb-2">REKENING / BANK</label>
                <select name="rekening_id" @change="
                    let opt = $el.options[$el.selectedIndex];
                    namaBank = opt.getAttribute('data-bank') || '';
                " class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:border-[#D8A749]" required>
                    <option value="">-- Pilih Rekening / Bank --</option>
                    @foreach($rekenings as$rek)
                        <option value="{{ $rek->id }}" data-bank="{{ $rek->nama_bank ?? $rek->nama_rekening }}">
                            {{ $rek->nama_rekening }} ({{$rek->nama_bank ?? 'Cash' }})
                        </option>
                    @endforeach
                </select>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- JENIS TRANSAKSI -->
            <div>
                <label class="block font-semibold text-slate-600 mb-2">JENIS TRANSAKSI</label>
                <select name="jenis" x-model="jenis" class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:border-[#D8A749]" required>
                    <option value="keluar">Keluar (KK)</option>
                    <option value="masuk">Masuk (KM)</option>
                    <option value="pindah">Pindah Buku (KK)</option>
                </select>
            </div>

            <!-- NOMOR JURNAL OTOMATIS -->
            <div>
                <label class="block font-semibold text-slate-600 mb-2">NOMOR JURNAL (OTOMATIS)</label>
                <input type="text" name="no_jurnal" x-bind:value="noJurnalAuto" readonly class="w-full px-4 py-2.5 bg-slate-100 font-mono font-bold text-slate-700 border border-slate-200 rounded-xl cursor-not-allowed" placeholder="Otomatis terisi..." required>
                <span class="text-[10px] text-slate-400 mt-1 block">Nomor jurnal digenerate otomatis oleh sistem sesuai format bank dan tanggal.</span>
            </div>
        </div>

        <!-- NOMINAL & KATEGORI -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <label class="block font-semibold text-slate-600 mb-2">NOMINAL (IDR)</label>
                <input type="number" name="nominal" step="any" placeholder="Contoh: 1500000" class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:border-[#D8A749]" required>
            </div>
            <div>
                <label class="block font-semibold text-slate-600 mb-2">KATEGORI / AKUN COA</label>
                <select name="kategori_id" class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:border-[#D8A749]" required>
                    <option value="">-- Pilih Kategori COA --</option>
                    @foreach($kategoris ?? [] as $kat)
                        <option value="{{ $kat->id }}">{{ $kat->no_akun }} - {{$kat->nama_akun }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <!-- KETERANGAN -->
        <div>
            <label class="block font-semibold text-slate-600 mb-2">KETERANGAN / DESKRIPSI</label>
            <textarea name="keterangan" rows="3" placeholder="Keterangan transaksi..." class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:border-[#D8A749]"></textarea>
        </div>

        <!-- UPLOAD BUKTI FOTO -->
        <div>
            <label class="block font-semibold text-slate-600 mb-2">BUKTI FOTO TRANSAKSI</label>
            <input type="file" name="bukti_foto" accept="image/*" class="w-full px-4 py-2 bg-slate-50 border border-slate-200 rounded-xl text-slate-600 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-amber-50 file:text-amber-700 hover:file:bg-amber-100">
        </div>

        <!-- TOMBOL SUBMIT -->
        <div class="pt-4 flex justify-end">
            <button type="submit" class="px-6 py-3 bg-[#D8A749] hover:bg-[#c4953f] text-white font-bold rounded-xl transition shadow-sm">
                Simpan Transaksi Mutasi
            </button>
        </div>
    </form>
</div>
@endsection