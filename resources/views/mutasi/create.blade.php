            <p class="text-sm text-slate-500 mt-2">Catat transaksi keuangan dengan rapi dan mudah ditelusuri.</p>
        </div>
        <a href="{{ route('mutasi.index') }}" class="inline-flex items-center justify-center gap-2 px-4 py-2.5 bg-white border border-slate-200 hover:bg-slate-50 text-slate-700 text-sm font-semibold rounded-xl transition">
            <span aria-hidden="true">&larr;</span> Kembali ke Mutasi
        </a>
    </div>

    @if ($errors->any())
        <div class="rounded-2xl border border-red-200 bg-red-50 p-4">
            <p class="font-bold text-red-700 mb-2">Data belum bisa disimpan</p>
            <ul class="list-disc pl-5 space-y-1 text-sm text-red-600">
                @foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('mutasi.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
        @csrf

        <section class="bg-white rounded-2xl sm:rounded-3xl border border-slate-200 shadow-sm overflow-hidden">
            <div class="px-5 sm:px-7 py-5 border-b border-slate-100">
                <h2 class="text-base font-bold text-slate-900">Informasi Transaksi</h2>
                <p class="text-sm text-slate-500 mt-1">Tentukan tanggal, rekening, dan jenis transaksi.</p>
            </div>
            <div class="p-5 sm:p-7 grid grid-cols-1 md:grid-cols-2 gap-x-6 gap-y-5">
                <div>
                    <label for="tanggal" class="block text-sm font-semibold text-slate-700 mb-2">Tanggal Transaksi <span class="text-red-500">*</span></label>
                    <input id="tanggal" type="date" name="tanggal" x-model="tanggal" required class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-sm text-slate-800 focus:outline-none focus:ring-2 focus:ring-amber-400/40 focus:border-amber-400">
                    @error('tanggal')<p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label for="rekening_id" class="block text-sm font-semibold text-slate-700 mb-2">Rekening / Bank <span class="text-red-500">*</span></label>
                    <select id="rekening_id" name="rekening_id" x-ref="rekening" x-on:change="syncBank($refs.rekening)" required class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-sm text-slate-800 focus:outline-none focus:ring-2 focus:ring-amber-400/40 focus:border-amber-400">
                        <option value="">Pilih rekening / bank</option>
                        @foreach ($rekenings as $rek)
                            <option value="{{ $rek->id }}" data-bank="{{ $rek->nama_bank ?? $rek->nama_rekening }}" @selected(old('rekening_id') == $rek->id)>
                                {{ $rek->nama_rekening }} ({{ $rek->nama_bank ?? 'Cash' }})
                            </option>
                        @endforeach
                    </select>
                    @error('rekening_id')<p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label for="jenis" class="block text-sm font-semibold text-slate-700 mb-2">Jenis Transaksi <span class="text-red-500">*</span></label>
                    <select id="jenis" name="jenis" x-model="jenis" required class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-sm text-slate-800 focus:outline-none focus:ring-2 focus:ring-amber-400/40 focus:border-amber-400">
                        <option value="keluar">Kas Keluar (KK)</option>
                        <option value="masuk">Kas Masuk (KM)</option>
                        <option value="pindah">Pindah Buku (KK)</option>
                    </select>
                    @error('jenis')<p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label for="no_jurnal_preview" class="block text-sm font-semibold text-slate-700 mb-2">Pratinjau Nomor Jurnal</label>
                    <input id="no_jurnal_preview" type="text" x-bind:value="noJurnalAuto" readonly class="w-full px-4 py-3 bg-amber-50 border border-amber-200 rounded-xl text-sm font-mono font-bold text-slate-700 cursor-not-allowed">
                    <p class="text-xs text-slate-500 mt-2">Nomor final dibuat oleh sistem saat transaksi disimpan.</p>
                </div>
            </div>
        </section>

        <section class="bg-white rounded-2xl sm:rounded-3xl border border-slate-200 shadow-sm overflow-hidden">
            <div class="px-5 sm:px-7 py-5 border-b border-slate-100">
                <h2 class="text-base font-bold text-slate-900">Detail Keuangan</h2>
                <p class="text-sm text-slate-500 mt-1">Masukkan nominal dan akun COA yang sesuai.</p>
            </div>
            <div class="p-5 sm:p-7 grid grid-cols-1 md:grid-cols-2 gap-x-6 gap-y-5">
                <div>
                    <label for="nominal" class="block text-sm font-semibold text-slate-700 mb-2">Nominal (IDR) <span class="text-red-500">*</span></label>
                    <div class="flex rounded-xl overflow-hidden border border-slate-200 focus-within:border-amber-400 focus-within:ring-2 focus-within:ring-amber-400/20">
                        <span class="px-4 py-3 bg-slate-100 text-sm font-bold text-slate-600 border-r border-slate-200">Rp</span>
                        <input id="nominal" type="number" name="nominal" min="0.01" step="any" value="{{ old('nominal') }}" placeholder="0" required class="w-full min-w-0 px-4 py-3 bg-white text-sm text-slate-800 focus:outline-none">
                    </div>
                    @error('nominal')<p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label for="kategori_id" class="block text-sm font-semibold text-slate-700 mb-2">Kategori / Akun COA <span class="text-red-500">*</span></label>
                    <select id="kategori_id" name="kategori_id" required class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-sm text-slate-800 focus:outline-none focus:ring-2 focus:ring-amber-400/40 focus:border-amber-400">
                        <option value="">Pilih akun COA</option>
                        @foreach ($kategoris as $kat)
                            <option value="{{ $kat->id }}" @selected(old('kategori_id') == $kat->id)>{{ $kat->no_akun }} - {{ $kat->nama_akun }}</option>
                        @endforeach
                    </select>
                    @error('kategori_id')<p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror
                </div>
                <div class="md:col-span-2">
                    <label for="keterangan" class="block text-sm font-semibold text-slate-700 mb-2">Keterangan Transaksi</label>
                    <textarea id="keterangan" name="keterangan" rows="4" placeholder="Contoh: Pembayaran biaya operasional..." class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-sm text-slate-800 resize-y focus:outline-none focus:ring-2 focus:ring-amber-400/40 focus:border-amber-400">{{ old('keterangan') }}</textarea>
                    @error('keterangan')<p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror
                </div>
            </div>
        </section>

        <section class="bg-white rounded-2xl sm:rounded-3xl border border-slate-200 shadow-sm overflow-hidden">
            <div class="px-5 sm:px-7 py-5 border-b border-slate-100">
                <h2 class="text-base font-bold text-slate-900">Bukti Transaksi</h2>
                <p class="text-sm text-slate-500 mt-1">Lampirkan foto atau hasil scan bukti pembayaran.</p>
            </div>
            <div class="p-5 sm:p-7">
                <label for="bukti_foto" class="block text-sm font-semibold text-slate-700 mb-2">Upload Bukti Foto</label>
                <div class="rounded-2xl border-2 border-dashed border-slate-300 bg-slate-50 p-5 sm:p-7 text-center hover:border-amber-400 transition">
                    <div class="text-3xl text-amber-500 mb-3" aria-hidden="true">↑</div>
                    <p class="text-sm font-semibold text-slate-700">Pilih file bukti transaksi</p>
                    <p class="text-xs text-slate-500 mt-2">JPG, JPEG, PNG, WEBP, HEIC, atau HEIF. Maksimal 10 MB.</p>
                    <input id="bukti_foto" type="file" name="bukti_foto" accept=".jpg,.jpeg,.png,.webp,.heic,.heif,image/jpeg,image/png,image/webp,image/heic,image/heif" x-on:change="namaFile = $event.target.files.length ? $event.target.files[0].name : ''" class="block w-full max-w-md mx-auto mt-5 text-sm text-slate-600 file:mr-4 file:py-2.5 file:px-4 file:rounded-xl file:border-0 file:text-sm file:font-semibold file:bg-amber-100 file:text-amber-800 hover:file:bg-amber-200">
                    <p x-show="namaFile" x-text="namaFile" class="text-sm text-emerald-700 font-medium mt-4 break-all"></p>
                </div>
                @error('bukti_foto')<p class="text-xs text-red-600 mt-2">{{ $message }}</p>@enderror
            </div>
        </section>

        <div class="flex flex-col-reverse sm:flex-row sm:items-center sm:justify-between gap-3">
            <p class="text-xs text-slate-500"><span class="text-red-500">*</span> Wajib diisi</p>
            <div class="flex flex-col sm:flex-row gap-3">
                <a href="{{ route('mutasi.index') }}" class="inline-flex items-center justify-center px-5 py-3 bg-white border border-slate-200 hover:bg-slate-50 text-slate-700 text-sm font-semibold rounded-xl transition">Batal</a>
                <button type="submit" class="inline-flex items-center justify-center gap-2 px-6 py-3 bg-[#D8A749] hover:bg-[#c4953f] text-white text-sm font-bold rounded-xl transition shadow-sm">Simpan Transaksi</button>
            </div>
        </div>
    </form>
</div>
@endsection
