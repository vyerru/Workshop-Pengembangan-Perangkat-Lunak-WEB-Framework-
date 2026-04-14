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
use App\Http\Controllers\PosController;
use Laravolt\Indonesia\Models\Province;
use Laravolt\Indonesia\Models\City;
use Laravolt\Indonesia\Models\District;
use Laravolt\Indonesia\Models\Village;
use App\Http\Controllers\WilayahController;
use App\Http\Controllers\Canteen\VendorMenuController;
use App\Http\Controllers\Canteen\OrderController;
use App\Http\Controllers\Canteen\VendorDashboardController;

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

    Route::get('/wilayah/ajax', function () {
        return view('wilayah.ajax');
    })->name('wilayah.ajax');
    Route::get('/wilayah/axios', function () {
        return view('wilayah.axios');
    })->name('wilayah.axios');
    Route::get('/wilayah/provinsi', [WilayahController::class, 'provinsi']);
    Route::get('/wilayah/kota/{id}', [WilayahController::class, 'kota']);
    Route::get('/wilayah/kecamatan/{id}', [WilayahController::class, 'kecamatan']);
    Route::get('/wilayah/kelurahan/{id}', [WilayahController::class, 'kelurahan']);

    // Route Studi Kasus 2: POS Kasir
    Route::get('/pos/ajax', [PosController::class, 'ajax'])->name('pos.ajax');
    Route::get('/pos/axios', [PosController::class, 'axios'])->name('pos.axios');
    Route::get('/pos/barang/{id}', [PosController::class, 'getBarang']);
    Route::post('/pos/store', [PosController::class, 'store']);
});

Route::middleware(['auth', 'role:customer'])->prefix('canteen')->name('canteen.')->group(function () {
    Route::get('/pos', [OrderController::class, 'index'])->name('pos');

    // Rute untuk Axios fetch data
    Route::get('/pos/menus/{vendor_id}', [OrderController::class, 'getMenus'])->name('menus');

    // Rute POST untuk submit keranjang
    Route::post('/checkout', [OrderController::class, 'checkout'])->name('checkout');
});

Route::middleware(['auth', 'role:vendor'])->prefix('vendor')->name('vendor.')->group(function () {
    Route::get('/dashboard', [VendorDashboardController::class, 'index'])->name('dashboard');

    // Rute CRUD Menu (Hanya Vendor)
    Route::resource('menus', VendorMenuController::class)->except(['show']);
});
