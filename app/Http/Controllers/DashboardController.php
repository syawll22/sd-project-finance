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
    // Hitung total kas / saldo aktif (Total Masuk - Total Keluar/Pindah)
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

    // Transaksi Terakhir (Pastikan variabel compact-nya $latestMutasi sesuai blade lu)
    $latestMutasi = Mutasi::with(['rekening', 'kategori'])
        ->latest('tanggal')
        ->take(5)
        ->get();

    // Data untuk Chart Statistik Kas (Contoh 6 bulan terakhir atau dummy aman)
    $chartLabels = ['May 2026', 'June 2026', 'July 2026', 'August 2026', 'September 2026', 'October 2026'];
    $chartMasuk = [0, 0, 0, 0, 0, $pemasukanBulanIni];
    $chartKeluar = [0, 0, 0, 0, 0, $pengeluaranBulanIni];

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