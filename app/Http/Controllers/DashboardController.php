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
            // Hitung total kas / saldo aktif
            $totalMasukSemua = Mutasi::where('jenis', 'masuk')->sum('nominal');
            $totalKeluarSemua = Mutasi::whereIn('jenis', ['keluar', 'pindah'])->sum('nominal');
            $totalSaldo = $totalMasukSemua - $totalKeluarSemua;

            // Total Pemasukan Bulan Ini
            $pemasukanBulanIni = Mutasi::where('jenis', 'masuk')
                ->whereMonth('tanggal', date('m'))
                ->whereYear('tanggal', date('Y'))
                ->sum('nominal');

            // Total Pengeluaran Bulan Ini
            $pengeluaranBulanIni = Mutasi::whereIn('jenis', ['keluar', 'pindah'])
                ->whereMonth('tanggal', date('m'))
                ->whereYear('tanggal', date('Y'))
                ->sum('nominal');

            // Transaksi Terakhir
            $latestMutasi = Mutasi::with(['rekening', 'kategori'])
                ->latest('tanggal')
                ->take(5)
                ->get();

            // DINAMIS: Ambil data 6 bulan terakhir dari database
            $chartLabels = [];
            $chartMasuk = [];
            $chartKeluar = [];

            for ($i = 5; $i >= 0; $i--) {
                $date = now()->subMonths($i);
                $monthName = $date->format('F Y'); // Contoh: June 2026
                $chartLabels[] = $monthName;

                // Hitung pemasukan per bulan
                $masuk = Mutasi::where('jenis', 'masuk')
                    ->whereYear('tanggal', $date->year)
                    ->whereMonth('tanggal', $date->month)
                    ->sum('nominal');
                $chartMasuk[] = $masuk;

                // Hitung pengeluaran per bulan
                $keluar = Mutasi::whereIn('jenis', ['keluar', 'pindah'])
                    ->whereYear('tanggal', $date->year)
                    ->whereMonth('tanggal', $date->month)
                    ->sum('nominal');
                $chartKeluar[] = $keluar;
            }

            return view('dashboard', compact(
                'totalSaldo', 
                'pemasukanBulanIni', 
                'pengeluaranBulanIni', 
                'latestMutasi', 
                'chartLabels', 
                'chartMasuk', 
                'chartKeluar'
            ));
        }
}