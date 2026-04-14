<?php

namespace App\Http\Controllers\Canteen;

use App\Http\Controllers\Controller;
use App\Models\Pesanan;
use Illuminate\Support\Facades\Auth;

class VendorDashboardController extends Controller
{
    public function index()
    {
        $vendor = Auth::user()->vendor;
        
        if (!$vendor) {
            abort(403, 'Profil Vendor tidak ditemukan.');
        }

        // Kritis: Kueri terisolasi dengan Eager Loading untuk mencegah N+1 Problem
        $pesanans = Pesanan::with('detailPesanans.menu') // Memuat relasi tabel anak
            ->where('vendor_id', $vendor->id)
            ->where('status_bayar', 1) // 
            ->orderBy('created_at', 'desc')
            ->get();

        return view('vendor.dashboard', compact('pesanans'));
    }
}
