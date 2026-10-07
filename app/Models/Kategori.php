<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Kategori extends Model
{
    use HasFactory;

    protected $table = 'kategoris'; 

    // DAFTARKAN SEMUA KEMUNGKINAN NAMA KOLOM BIAR GAK DIBUANG LARAVEL!
    protected $fillable = [
        'kode',
        'no_akun',
        'nama_kategori',
        'nama_akun',
    ];
}