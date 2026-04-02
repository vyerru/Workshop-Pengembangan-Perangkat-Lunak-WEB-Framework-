<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\KategoriController;
use App\Http\Controllers\BukuController;
use Laravel\Socialite\Socialite;
use App\Http\Controllers\LoginCallbackController;
use App\Http\Controllers\pdfController;
use App\Http\Controllers\BarangController;

Route::get('/', function () {
    return redirect()->route('login');
});

// Alur Google Auth [cite: 7]
Route::get('/auth/google/redirect', [LoginCallbackController::class, 'redirect'])->name('google.redirect');
Route::get('/auth/google/callback', [LoginCallbackController::class, 'callback'])->name('google.callback');

// Alur OTP [cite: 11, 13]
Route::get('/otp-verify', [LoginCallbackController::class, 'otpView'])->name('otp.view');
Route::post('/otp-verify', [LoginCallbackController::class, 'otpVerify'])->name('otp.verify');
Auth::routes();

Route::middleware(['auth'])->group(function () {
    Route::get('/home', [HomeController::class, 'index'])->name('home');

    Route::resource('kategori', KategoriController::class);
    Route::resource('buku', BukuController::class);
    Route::resource('barang', BarangController::class)->except(['show', 'create', 'edit']);

    Route::get('/cetak/sertifikat', [pdfController::class, 'cetakSertifikat'])->name('cetak.sertifikat');
    Route::get('/cetak/undangan', [pdfController::class, 'cetakUndangan'])->name('cetak.undangan');
    Route::post('/barang/cetak-label', [BarangController::class, 'cetakLabel'])->name('barang.cetak_label');
});
