@extends('layouts.app')

@section('title', 'Dashboard - S&D Finance')

@section('content')
<div class="space-y-5 lg:space-y-6">

    <!-- TOP ROW CARDS (Mobile: 1 kolom, Tablet: 2 kolom, Desktop: 3 kolom) -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3 sm:gap-4 lg:gap-6">
        
        <!-- Card 1: Total Kas -->
        <div class="bg-goldAccent text-slate-900 p-5 rounded-2xl lg:rounded-[2rem] shadow-sm relative overflow-hidden flex flex-col justify-between min-h-[8.5rem]">
            <div class="z-10">
                <p class="text-[11px] lg:text-xs font-bold uppercase tracking-wider text-slate-800">Total Kas & Bank</p>
                <h2 class="text-2xl sm:text-3xl font-black mt-1 tracking-tight text-slate-900">
                    Rp {{ number_format($totalSaldo, 0, ',', '.') }}
                </h2>
            </div>
            <p class="text-[10px] sm:text-[11px] font-semibold text-slate-800/80 z-10 mt-3">Saldo aktif semua rekening</p>
            <div class="absolute -right-3 -bottom-5 text-slate-900/10 text-6xl sm:text-7xl font-black select-none pointer-events-none">💳</div>
        </div>

        <!-- Card 2: Pemasukan -->
        <div class="bg-sidebar text-white p-5 rounded-2xl lg:rounded-[2rem] shadow-sm relative overflow-hidden flex flex-col justify-between min-h-[8.5rem]">
            <div class="z-10">
                <p class="text-[11px] lg:text-xs font-bold uppercase tracking-wider text-slate-400">Pemasukan (Bulan Ini)</p>
                <h2 class="text-2xl sm:text-3xl font-black text-emerald-400 mt-1 tracking-tight">
                    + Rp {{ number_format($pemasukanBulanIni, 0, ',', '.') }}
                </h2>
            </div>
            <p class="text-[10px] sm:text-[11px] font-medium text-slate-400 z-10 mt-3">Total arus uang masuk</p>
            <div class="absolute -right-3 -bottom-5 text-white/5 text-6xl sm:text-7xl font-black select-none pointer-events-none">📈</div>
        </div>

        <!-- Card 3: Pengeluaran -->
        <div class="bg-sidebar text-white p-5 rounded-2xl lg:rounded-[2rem] shadow-sm relative overflow-hidden flex flex-col justify-between min-h-[8.5rem] sm:col-span-2 lg:col-span-1">
            <div class="z-10">
                <p class="text-[11px] lg:text-xs font-bold uppercase tracking-wider text-slate-400">Pengeluaran (Bulan Ini)</p>
                <h2 class="text-2xl sm:text-3xl font-black text-rose-400 mt-1 tracking-tight">
                    - Rp {{ number_format($pengeluaranBulanIni, 0, ',', '.') }}
                </h2>
            </div>
            <p class="text-[10px] sm:text-[11px] font-medium text-slate-400 z-10 mt-3">Total arus uang keluar</p>
            <div class="absolute -right-3 -bottom-5 text-white/5 text-6xl sm:text-7xl font-black select-none pointer-events-none">📉</div>
        </div>

    </div>

    <!-- MIDDLE ROW: TABLE & CHART -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-5 lg:gap-6">

        <!-- Transaksi Terakhir (2 Kolom) -->
        <div class="lg:col-span-2 bg-white p-4 sm:p-6 rounded-2xl lg:rounded-[2rem] shadow-sm">
            <div class="flex items-center justify-between mb-4">
                <div>
                    <h3 class="text-sm sm:text-base font-bold text-slate-800">Transaksi Terakhir</h3>
                    <p class="text-[11px] sm:text-xs text-slate-400">Overview mutasi keuangan terbaru</p>
                </div>
                <a href="{{ route('mutasi.index') }}" class="text-xs font-bold text-goldAccent hover:underline">Lihat Semua →</a>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs min-w-[280px]">
                    <thead class="text-slate-400 border-b border-slate-100 uppercase font-bold text-[10px] tracking-wider">
                        <tr>
                            <th class="pb-2 sm:pb-3">Rekening</th>
                            <th class="pb-2 sm:pb-3">Kategori</th>
                            <th class="pb-2 sm:pb-3 text-right">Nominal</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-50">
                        @forelse($latestMutasi as $item)
                        <tr class="hover:bg-slate-50">
                            <td class="py-2.5 sm:py-3 font-semibold text-slate-800">
                                {{ $item->rekening->nama_rekening ?? 'Rekening' }}
                            </td>
                            <td class="py-2.5 sm:py-3 text-slate-500">
                                {{ $item->kategori->nama_kategori ?? 'Umum' }}
                            </td>
                            <td class="py-2.5 sm:py-3 font-bold text-right {{ $item->jenis == 'masuk' ? 'text-emerald-600' : 'text-rose-600' }}">
                                {{ $item->jenis == 'masuk' ? '+' : '-' }} Rp {{ number_format($item->nominal, 0, ',', '.') }}
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="3" class="py-6 text-center text-slate-400 text-xs">Belum ada transaksi mutasi.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Grafik Arus Kas / Statistics (1 Kolom) -->
        <div class="bg-white p-4 sm:p-6 rounded-2xl lg:rounded-[2rem] shadow-sm flex flex-col justify-between">
            <div>
                <h3 class="text-sm sm:text-base font-bold text-slate-800">Statistik Kas</h3>
                <p class="text-[11px] sm:text-xs text-slate-400 mb-4">Perbandingan Pemasukan vs Pengeluaran</p>
            </div>
            <div class="h-48 sm:h-56 relative w-full">
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
                    backgroundColor: '#d4a338',
                    borderRadius: 6
                },
                {
                    label: 'Keluar',
                    data: {!! json_encode($chartKeluar) !!},
                    backgroundColor: '#2c3e3a',
                    borderRadius: 6
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