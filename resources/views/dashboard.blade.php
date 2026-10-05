@extends('layouts.app')

@section('title', 'Dashboard - S&D Finance')

@section('content')
<div class="space-y-6">

    <!-- TOP ROW CARDS (Inspirasi Boeing/Airbus & Total Flights) -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        
        <!-- Top Card 1: Mustard Gold Card (Total Kas) -->
        <div class="bg-goldAccent text-slate-900 p-6 rounded-[2rem] shadow-sm relative overflow-hidden flex flex-col justify-between h-36">
            <div class="z-10">
                <p class="text-xs font-bold uppercase tracking-wider text-slate-800">Total Kas & Bank</p>
                <h2 class="text-3xl font-black mt-1">Rp {{ number_format($totalSaldo, 0, ',', '.') }}</h2>
            </div>
            <p class="text-[11px] font-semibold text-slate-800/80 z-10">Saldo aktif semua rekening</p>
            <!-- Aksen hiasan background -->
            <div class="absolute -right-4 -bottom-6 text-slate-900/10 text-8xl font-black select-none">💳</div>
        </div>

        <!-- Top Card 2: Dark Slate Card (Pemasukan) -->
        <div class="bg-sidebar text-white p-6 rounded-[2rem] shadow-sm relative overflow-hidden flex flex-col justify-between h-36">
            <div class="z-10">
                <p class="text-xs font-bold uppercase tracking-wider text-slate-400">Pemasukan (Bulan Ini)</p>
                <h2 class="text-3xl font-black text-emerald-400 mt-1">+ Rp {{ number_format($pemasukanBulanIni, 0, ',', '.') }}</h2>
            </div>
            <p class="text-[11px] font-medium text-slate-400 z-10">Total arus uang masuk</p>
            <div class="absolute -right-4 -bottom-6 text-white/5 text-8xl font-black select-none">📈</div>
        </div>

        <!-- Top Card 3: Dark Slate Card (Pengeluaran) -->
        <div class="bg-sidebar text-white p-6 rounded-[2rem] shadow-sm relative overflow-hidden flex flex-col justify-between h-36">
            <div class="z-10">
                <p class="text-xs font-bold uppercase tracking-wider text-slate-400">Pengeluaran (Bulan Ini)</p>
                <h2 class="text-3xl font-black text-rose-400 mt-1">- Rp {{ number_format($pengeluaranBulanIni, 0, ',', '.') }}</h2>
            </div>
            <p class="text-[11px] font-medium text-slate-400 z-10">Total arus uang keluar</p>
            <div class="absolute -right-4 -bottom-6 text-white/5 text-8xl font-black select-none">📉</div>
        </div>

    </div>

    <!-- MIDDLE ROW: TABLE & CHART -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        <!-- Transaksi Terakhir (2 Kolom) -->
        <div class="lg:col-span-2 bg-white p-6 rounded-[2rem] shadow-sm">
            <div class="flex items-center justify-between mb-4">
                <div>
                    <h3 class="text-base font-bold text-slate-800">Transaksi Terakhir</h3>
                    <p class="text-xs text-slate-400">Overview mutasi keuangan terbaru</p>
                </div>
                <a href="{{ route('mutasi.index') }}" class="text-xs font-bold text-goldAccent hover:underline">Lihat Semua →</a>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead class="text-slate-400 border-b border-slate-100 uppercase font-bold text-[10px] tracking-wider">
                        <tr>
                            <th class="pb-3">Rekening</th>
                            <th class="pb-3">Kategori</th>
                            <th class="pb-3">Nominal</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-50">
                        @forelse($latestMutasi as $item)
                        <tr class="hover:bg-slate-50">
                            <td class="py-3 font-semibold text-slate-800">
                                {{ $item->rekening->nama_rekening ?? 'Rekening' }}
                            </td>
                            <td class="py-3 text-slate-500">
                                {{ $item->kategori->nama_kategori ?? 'Umum' }}
                            </td>
                            <td class="py-3 font-bold {{ $item->tipe == 'masuk' ? 'text-emerald-600' : 'text-rose-600' }}">
                                {{ $item->tipe == 'masuk' ? '+' : '-' }} Rp {{ number_format($item->nominal, 0, ',', '.') }}
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="3" class="py-6 text-center text-slate-400">Belum ada transaksi mutasi.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Grafik Arus Kas / Statistics (1 Kolom) -->
        <div class="bg-white p-6 rounded-[2rem] shadow-sm flex flex-col justify-between">
            <div>
                <h3 class="text-base font-bold text-slate-800">Statistik Kas</h3>
                <p class="text-xs text-slate-400 mb-4">Perbandingan Pemasukan vs Pengeluaran</p>
            </div>
            <div class="h-56">
                <canvas id="cashflowChart"></canvas>
            </div>
        </div>

    </div>

</div>

<!-- Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    const ctx = document.getElementById('cashflowChart').getContext('2d');
    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: {!! json_encode($chartLabels) !!},
            datasets: [
                {
                    label: 'Masuk',
                    data: {!! json_encode($chartMasuk) !!},
                    backgroundColor: '#d4a338', // Gold
                    borderRadius: 8
                },
                {
                    label: 'Keluar',
                    data: {!! json_encode($chartKeluar) !!},
                    backgroundColor: '#2c3e3a', // Dark Teal
                    borderRadius: 8
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { position: 'bottom' }
            },
            scales: {
                y: { display: false },
                x: { grid: { display: false } }
            }
        }
    });
</script>
@endsection