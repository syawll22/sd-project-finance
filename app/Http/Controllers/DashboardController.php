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
    // Total Pemasukan (Jenis = 'masuk')
    $pemasukanBulanIni = Mutasi::where('jenis', 'masuk')
        ->whereMonth('tanggal', date('m'))
        ->whereYear('tanggal', date('Y'))
        ->sum('nominal');

    // Total Pengeluaran (Jenis = 'keluar' atau 'pindah')
    $pengeluaranBulanIni = Mutasi::whereIn('jenis', ['keluar', 'pindah'])
        ->whereMonth('tanggal', date('m'))
        ->whereYear('tanggal', date('Y'))
        ->sum('nominal');

    // Transaksi Terakhir
    $transaksiTerakhir = Mutasi::with(['rekening', 'kategori'])
        ->latest('tanggal')
        ->take(5)
        ->get();

    return view('dashboard', compact('pemasukanBulanIni', 'pengeluaranBulanIni', 'transaksiTerakhir'));
}
}