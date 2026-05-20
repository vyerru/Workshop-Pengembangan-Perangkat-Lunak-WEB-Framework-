<?php

namespace App\Http\Controllers\Canteen;

use App\Http\Controllers\Controller;
use App\Models\Pesanan;
use Illuminate\Http\Request;
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

    public function verifyQr(Request $request)
    {
        $request->validate([
            'qr_token' => 'required|string|max:40',
        ]);

        $pesanan = Pesanan::with('detailPesanans.menu')
            ->where('qr_token', $request->qr_token)
            ->first();

        if (!$pesanan) {
            return response()->json([
                'status' => 'error',
                'message' => 'QR Token tidak valid atau pesanan tidak ditemukan.',
            ], 404);
        }

        $vendor = Auth::user()->vendor;

        if (!$vendor || $pesanan->vendor_id !== $vendor->id) {
            return response()->json([
                'status' => 'error',
                'message' => 'Pesanan ini bukan milik vendor Anda.',
            ], 403);
        }

        return response()->json([
            'status' => 'success',
            'data' => [
                'pesanan' => $pesanan,
                'detail_pesanan' => $pesanan->detailPesanans,
            ],
        ]);
    }
}
