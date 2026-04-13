<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Siswa\DashboardController;

// 1. Jalur depan: Sekarang kita kasih gembok 'auth' juga.
// Jadi kalau buka 127.0.0.1:8000 belum login, dia bakal dicegat.
Route::get('/', [DashboardController::class, 'index'])
    ->name('home')
    ->middleware('auth');

// 2. Jalur Pinjam: Tetap wajib login.
Route::post('/pinjam/{buku}', [DashboardController::class, 'pinjam'])
    ->name('siswa.pinjam')
    ->middleware('auth');
    // Trik biar Laravel gak nyari file Authenticate.php
Route::get('/login', function () {
    return redirect('/admin/login');
})->name('login');