<?php

namespace App\Http\Controllers;

use App\Models\Mutasi;
use App\Models\Rekening;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        // 1. Total Saldo Semua Rekening
        $totalSaldo = Rekening::sum('saldo') ?? 0;
        $rekenings = Rekening::all();

        // 2. Total Pemasukan & Pengeluaran Bulan Ini
        $pemasukanBulanIni = Mutasi::where('jenis', 'masuk')
            ->whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->sum('nominal') ?? 0;

        $pengeluaranBulanIni = Mutasi::where('jenis', 'keluar')
            ->whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->sum('nominal') ?? 0;

        $netCashflow = $pemasukanBulanIni - $pengeluaranBulanIni;

        // 3. 5 Transaksi Terakhir
        $latestMutasi = Mutasi::with(['rekening', 'kategori'])
            ->latest()
            ->take(5)
            ->get();

        // 4. Data Chart (6 Bulan Terakhir)
        $chartLabels = [];
        $chartMasuk = [];
        $chartKeluar = [];

        for ($i = 5; $i >= 0; $i--) {
            $month = now()->subMonths($i);
            $chartLabels[] = $month->translatedFormat('F Y');

            $chartMasuk[] = Mutasi::where('jenis', 'masuk')
                ->whereMonth('created_at', $month->month)
                ->whereYear('created_at', $month->year)
                ->sum('nominal') ?? 0;

            $chartKeluar[] = Mutasi::where('jenis', 'keluar')
                ->whereMonth('created_at', $month->month)
                ->whereYear('created_at', $month->year)
                ->sum('nominal') ?? 0;
        }

        return view('dashboard', compact(
            'totalSaldo',
            'rekenings',
            'pemasukanBulanIni',
            'pengeluaranBulanIni',
            'netCashflow',
            'latestMutasi',
            'chartLabels',
            'chartMasuk',
            'chartKeluar'
        ));
    }
}