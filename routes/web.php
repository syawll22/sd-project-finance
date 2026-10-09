<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\RekeningController;
use App\Http\Controllers\KategoriController;
use App\Http\Controllers\MutasiController;
use App\Http\Controllers\SettingController;

// Dashboard
Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

// Rekening
Route::resource('rekening', RekeningController::class);

// Kategori COA (Temporary Route)
Route::resource('kategori', KategoriController::class);

// Mutasi Transaksi (Temporary Route)
Route::get('/mutasi', [MutasiController::class, 'index'])->name('mutasi.index');
Route::get('/mutasi/create', [MutasiController::class, 'create'])->name('mutasi.create');
Route::post('/mutasi', [MutasiController::class, 'store'])->name('mutasi.store');
Route::put('/mutasi/{id}', [MutasiController::class, 'update'])->name('mutasi.update');
//biar bisa diakses dari route resource, tapi tetap pakai controller MutasiController
Route::get('/mutasi/{mutasi}/edit', [MutasiController::class, 'edit'])->name('mutasi.edit');
Route::put('/mutasi/{mutasi}', [MutasiController::class, 'update'])->name('mutasi.update');
Route::delete('/mutasi/{mutasi}', [MutasiController::class, 'destroy'])->name('mutasi.destroy');

// Settings (Temporary Route)
Route::get('/settings', [SettingController::class, 'index'])->name('settings.index');
Route::put('/settings/profile', [SettingController::class, 'updateProfile'])->name('settings.updateProfile');
Route::put('/settings/password', [SettingController::class, 'updatePassword'])->name('settings.updatePassword');
Route::get('/run-migrate-fix', function () {\Illuminate\Support\Facades\Artisan::call('migrate --force');return 'Migration sukses disebarkan bray!';});