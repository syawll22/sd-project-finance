            </div>
            <div class="p-5 sm:p-7 grid grid-cols-1 md:grid-cols-2 gap-x-6 gap-y-5">
                <div>
                    <label for="no_jurnal" class="block text-sm font-semibold text-slate-700 mb-2">Nomor Jurnal</label>
                    <input id="no_jurnal" type="text" value="{{ $mutasi->no_jurnal }}" readonly class="w-full px-4 py-3 bg-amber-50 border border-amber-200 rounded-xl text-sm font-mono font-bold text-slate-700 cursor-not-allowed">
                    <p class="text-xs text-slate-500 mt-2">Jika rekening, bulan, atau jenis berubah, sistem akan menyesuaikan nomor jurnal saat disimpan.</p>
                </div>
                <div>
                    <label for="tanggal" class="block text-sm font-semibold text-slate-700 mb-2">Tanggal Transaksi <span class="text-red-500">*</span></label>
                    <input id="tanggal" type="date" name="tanggal" value="{{ old('tanggal', $mutasi->tanggal ? \Illuminate\Support\Carbon::parse($mutasi->tanggal)->format('Y-m-d') : '') }}" required class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-sm text-slate-800 focus:outline-none focus:ring-2 focus:ring-amber-400/40 focus:border-amber-400">
                    @error('tanggal')<p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label for="rekening_id" class="block text-sm font-semibold text-slate-700 mb-2">Rekening / Bank <span class="text-red-500">*</span></label>
                    <select id="rekening_id" name="rekening_id" required class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-sm text-slate-800 focus:outline-none focus:ring-2 focus:ring-amber-400/40 focus:border-amber-400">
                        <option value="">Pilih rekening / bank</option>
                        @foreach ($rekenings as $rek)
                            <option value="{{ $rek->id }}" @selected(old('rekening_id', $mutasi->rekening_id) == $rek->id)>{{ $rek->nama_rekening }} ({{ $rek->nama_bank ?? 'Cash' }})</option>
                        @endforeach
                    </select>
                    @error('rekening_id')<p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label for="jenis" class="block text-sm font-semibold text-slate-700 mb-2">Jenis Transaksi <span class="text-red-500">*</span></label>
                    <select id="jenis" name="jenis" required class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-sm text-slate-800 focus:outline-none focus:ring-2 focus:ring-amber-400/40 focus:border-amber-400">
                        <option value="keluar" @selected(old('jenis', $mutasi->jenis) === 'keluar')>Kas Keluar (KK)</option>
                        <option value="masuk" @selected(old('jenis', $mutasi->jenis) === 'masuk')>Kas Masuk (KM)</option>
                        <option value="pindah" @selected(old('jenis', $mutasi->jenis) === 'pindah')>Pindah Buku (KK)</option>
                    </select>
                    @error('jenis')<p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror
                </div>
            </div>
        </section>

        <section class="bg-white rounded-2xl sm:rounded-3xl border border-slate-200 shadow-sm overflow-hidden">
            <div class="px-5 sm:px-7 py-5 border-b border-slate-100">
                <h2 class="text-base font-bold text-slate-900">Detail Keuangan</h2>
                <p class="text-sm text-slate-500 mt-1">Periksa kembali nominal dan akun yang digunakan.</p>
            </div>
            <div class="p-5 sm:p-7 grid grid-cols-1 md:grid-cols-2 gap-x-6 gap-y-5">
                <div>
                    <label for="nominal" class="block text-sm font-semibold text-slate-700 mb-2">Nominal (IDR) <span class="text-red-500">*</span></label>
                    <div class="flex rounded-xl overflow-hidden border border-slate-200 focus-within:border-amber-400 focus-within:ring-2 focus-within:ring-amber-400/20">
                        <span class="px-4 py-3 bg-slate-100 text-sm font-bold text-slate-600 border-r border-slate-200">Rp</span>
                        <input id="nominal" type="number" name="nominal" min="0.01" step="any" value="{{ old('nominal', $mutasi->nominal) }}" required class="w-full min-w-0 px-4 py-3 bg-white text-sm text-slate-800 focus:outline-none">
                    </div>
                    @error('nominal')<p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label for="kategori_id" class="block text-sm font-semibold text-slate-700 mb-2">Kategori / Akun COA <span class="text-red-500">*</span></label>
                    <select id="kategori_id" name="kategori_id" required class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-sm text-slate-800 focus:outline-none focus:ring-2 focus:ring-amber-400/40 focus:border-amber-400">
                        <option value="">Pilih akun COA</option>
                        @foreach ($kategoris as $kat)
                            <option value="{{ $kat->id }}" @selected(old('kategori_id', $mutasi->kategori_id) == $kat->id)>{{ $kat->no_akun }} - {{ $kat->nama_akun }}</option>
                        @endforeach
                    </select>
                    @error('kategori_id')<p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror
                </div>
                <div class="md:col-span-2">
                    <label for="keterangan" class="block text-sm font-semibold text-slate-700 mb-2">Keterangan Transaksi</label>
                    <textarea id="keterangan" name="keterangan" rows="4" class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-sm text-slate-800 resize-y focus:outline-none focus:ring-2 focus:ring-amber-400/40 focus:border-amber-400">{{ old('keterangan', $mutasi->keterangan) }}</textarea>
                    @error('keterangan')<p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror
                </div>
            </div>
        </section>

        <section class="bg-white rounded-2xl sm:rounded-3xl border border-slate-200 shadow-sm overflow-hidden">
            <div class="px-5 sm:px-7 py-5 border-b border-slate-100">
                <h2 class="text-base font-bold text-slate-900">Bukti Transaksi</h2>
                <p class="text-sm text-slate-500 mt-1">Bukti lama akan dipertahankan jika kamu tidak mengunggah file baru.</p>
            </div>
            <div class="p-5 sm:p-7 space-y-4">
                @if ($mutasi->bukti_foto)
                    <div class="rounded-2xl border border-slate-200 bg-slate-50 p-4">
                        <p class="text-sm font-semibold text-slate-700 mb-3">Bukti yang tersimpan saat ini</p>
                        <div class="flex flex-wrap gap-3">
                            <a href="{{ route('mutasi.bukti', $mutasi->id) }}" target="_blank" rel="noopener noreferrer" class="inline-flex items-center justify-center px-4 py-2.5 bg-amber-100 text-amber-800 hover:bg-amber-200 rounded-xl text-sm font-semibold">Buka Bukti</a>
                            <a href="{{ route('mutasi.bukti', ['id' => $mutasi->id, 'download' => 1]) }}" class="inline-flex items-center justify-center px-4 py-2.5 bg-white border border-slate-200 text-slate-700 hover:bg-slate-100 rounded-xl text-sm font-semibold">Unduh Bukti</a>
                        </div>
                    </div>
                @else
                    <p class="text-sm text-slate-500">Belum ada bukti transaksi yang diunggah.</p>
                @endif

                <div class="rounded-2xl border-2 border-dashed border-slate-300 bg-slate-50 p-5 sm:p-7 text-center hover:border-amber-400 transition">
                    <label for="bukti_foto" class="block text-sm font-semibold text-slate-700 mb-2">Unggah bukti baru (opsional)</label>
                    <p class="text-xs text-slate-500 mb-4">JPG, JPEG, PNG, WEBP, HEIC, atau HEIF. Maksimal 10 MB.</p>
                    <input id="bukti_foto" type="file" name="bukti_foto" accept=".jpg,.jpeg,.png,.webp,.heic,.heif,image/jpeg,image/png,image/webp,image/heic,image/heif" class="block w-full max-w-md mx-auto text-sm text-slate-600 file:mr-4 file:py-2.5 file:px-4 file:rounded-xl file:border-0 file:text-sm file:font-semibold file:bg-amber-100 file:text-amber-800 hover:file:bg-amber-200">
                    @error('bukti_foto')<p class="text-xs text-red-600 mt-2">{{ $message }}</p>@enderror
                </div>
            </div>
        </section>

        <div class="flex flex-col-reverse sm:flex-row sm:items-center sm:justify-between gap-3">
            <p class="text-xs text-slate-500"><span class="text-red-500">*</span> Wajib diisi</p>
            <div class="flex flex-col sm:flex-row gap-3">
                <a href="{{ route('mutasi.index') }}" class="inline-flex items-center justify-center px-5 py-3 bg-white border border-slate-200 hover:bg-slate-50 text-slate-700 text-sm font-semibold rounded-xl transition">Batal</a>
                <button type="submit" class="inline-flex items-center justify-center px-6 py-3 bg-[#D8A749] hover:bg-[#c4953f] text-white text-sm font-bold rounded-xl transition shadow-sm">Simpan Perubahan</button>
            </div>
        </div>
    </form>
</div>
@endsection
