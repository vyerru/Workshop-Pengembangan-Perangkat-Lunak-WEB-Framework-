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
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\Geolocation\TokoController;
use App\Http\Controllers\Geolocation\KunjunganController;

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
    Route::get('/barang/scan', [BarangController::class, 'scan'])->name('barang.scan');
    Route::get('/barang/api/{barcode}', [BarangController::class, 'apiShow'])->name('barang.api');
    Route::post('/barang/cetak-label', [BarangController::class, 'cetakLabel'])->name('barang.cetak_label');

    Route::get('/wilayah/data-asinkron', function () {
        return view('wilayah.ajax');
    })->name('wilayah.ajax');
    Route::get('/wilayah/data-asinkron-modern', function () {
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
    Route::get('/pesanan', [OrderController::class, 'index'])->name('pesanan');

    // Rute untuk Axios fetch data
    Route::get('/pesanan/menus/{vendor_id}', [OrderController::class, 'getMenus'])->name('menus');

    // Rute POST untuk submit keranjang
    Route::post('/checkout', [OrderController::class, 'checkout'])->name('checkout');
    Route::get('/customer/satu', [CustomerController::class, 'customerSatu'])->name('customer.satu');
    Route::get('/customer/dua', [CustomerController::class, 'customerDua'])->name('customer.dua');
    Route::post('/customer', [CustomerController::class, 'store'])->name('customer.store');
    Route::get('/riwayat', [CustomerController::class, 'history'])->name('riwayat');
});

Route::middleware(['auth', 'role:vendor'])->prefix('vendor')->name('vendor.')->group(function () {
    Route::get('/dashboard', [VendorDashboardController::class, 'index'])->name('dashboard');
    Route::post('/verify-qr', [VendorDashboardController::class, 'verifyQr'])->name('verify-qr');

    // Rute CRUD Menu (Hanya Vendor)
    Route::resource('menus', VendorMenuController::class)->except(['show']);
});

// =====================
// MODUL GEOLOCATION
// =====================

Route::middleware(['auth', 'role:admin'])->prefix('geolocation')->name('geolocation.')->group(function () {
    Route::get('/toko', [TokoController::class, 'index'])->name('toko.index');
    Route::get('/toko/create', [TokoController::class, 'create'])->name('toko.create');
    Route::post('/toko', [TokoController::class, 'store'])->name('toko.store');
    Route::get('/toko/{toko}/edit', [TokoController::class, 'edit'])->name('toko.edit');
    Route::put('/toko/{toko}', [TokoController::class, 'update'])->name('toko.update');
    Route::delete('/toko/{toko}', [TokoController::class, 'destroy'])->name('toko.destroy');
    Route::get('/toko/{toko}/barcode', [TokoController::class, 'cetakBarcode'])->name('toko.barcode');
    Route::get('/toko/{toko}/qrcode', [TokoController::class, 'cetakQrCode'])->name('toko.qrcode');
});

Route::middleware(['auth', 'role:sales'])->prefix('geolocation')->name('geolocation.')->group(function () {
    Route::get('/kunjungan', [KunjunganController::class, 'index'])->name('kunjungan.index');
    Route::get('/kunjungan/scan', [KunjunganController::class, 'scan'])->name('kunjungan.scan');
    Route::get('/kunjungan/scan-barcode', [KunjunganController::class, 'scanBarcode'])->name('kunjungan.scan-barcode');
    Route::get('/kunjungan/toko/{barcode_token}', [KunjunganController::class, 'getTokoByBarcode'])->name('kunjungan.toko-by-barcode');
    Route::post('/kunjungan', [KunjunganController::class, 'store'])
        ->middleware('throttle:10,1')
        ->name('kunjungan.store');
    Route::get('/kunjungan/riwayat', [KunjunganController::class, 'riwayat'])->name('kunjungan.riwayat');
});
