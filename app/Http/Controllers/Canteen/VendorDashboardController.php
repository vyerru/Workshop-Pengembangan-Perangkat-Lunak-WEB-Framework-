<?php

namespace App\Http\Controllers\Canteen;

use App\Http\Controllers\Controller;
use App\Jobs\GenerateTtsJob;
use App\Models\Pesanan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

class VendorDashboardController extends Controller
{
    public function index()
    {
        $vendor = Auth::user()->vendor;
        
        if (!$vendor) {
            abort(403, 'Profil Vendor tidak ditemukan.');
        }

        $pesanans = Pesanan::with('detailPesanans.menu')
            ->where('vendor_id', $vendor->id)
            ->where('status_bayar', 1)
            ->orderBy('created_at', 'desc')
            ->get();

        $antrianHariIni = Pesanan::with('detailPesanans.menu')
            ->forVendorToday($vendor->id)
            ->where('status_bayar', 1)
            ->orderBy('nomor_antrian', 'asc')
            ->get();

        return view('vendor.dashboard', compact('pesanans', 'antrianHariIni'));
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

    public function queue()
    {
        $vendor = Auth::user()->vendor;

        if (!$vendor) {
            abort(403, 'Profil Vendor tidak ditemukan.');
        }

        $antrians = Pesanan::with('detailPesanans.menu')
            ->forVendorToday($vendor->id)
            ->where('status_bayar', 1)
            ->orderBy('nomor_antrian', 'asc')
            ->get();

        if (request()->ajax()) {
            return view('vendor.antrian-list', compact('antrians'))->render();
        }

        return view('vendor.antrian', compact('antrians'));
    }

    public function papanAntrian()
    {
        $vendor = Auth::user()->vendor;

        if (!$vendor) {
            abort(403, 'Profil Vendor tidak ditemukan.');
        }

        return view('vendor.papan-antrian', ['vendorId' => $vendor->id]);
    }

    public function updateStatus(Request $request, Pesanan $pesanan)
    {
        $request->validate([
            'status' => 'required|in:pending,diproses,siap_dipanggil,selesai',
        ]);

        $vendor = Auth::user()->vendor;

        if (!$vendor || $pesanan->vendor_id !== $vendor->id) {
            return response()->json([
                'status' => 'error',
                'message' => 'Pesanan ini bukan milik vendor Anda.',
            ], 403);
        }

        $pesanan->update(['status_antrian' => $request->status]);
        Cache::forget('sse:queue:' . $vendor->id . ':data');

        if ($request->status === 'siap_dipanggil') {
            GenerateTtsJob::dispatch(
                'Nomor antrian ' . $pesanan->nomor_antrian . ', ' . $pesanan->nama,
                'Nomor antrian ' . $pesanan->nomor_antrian,
            );
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Status antrian berhasil diperbarui.',
            'data' => [
                'id' => $pesanan->id,
                'nomor_antrian' => $pesanan->nomor_antrian,
                'status_antrian' => $pesanan->status_antrian,
            ],
        ]);
    }

    public function panggilUlang(Pesanan $pesanan)
    {
        $vendor = Auth::user()->vendor;

        if (!$vendor || $pesanan->vendor_id !== $vendor->id) {
            return response()->json([
                'status' => 'error',
                'message' => 'Pesanan ini bukan milik vendor Anda.',
            ], 403);
        }

        $pesanan->touch();
        Cache::forget('sse:queue:' . $vendor->id . ':data');
        GenerateTtsJob::dispatch(
            'Nomor antrian ' . $pesanan->nomor_antrian . ', ' . $pesanan->nama,
            'Nomor antrian ' . $pesanan->nomor_antrian,
        );

        return response()->json([
            'status' => 'success',
            'message' => 'Pemanggilan ulang berhasil dikirim.',
            'data' => [
                'id' => $pesanan->id,
                'nomor_antrian' => $pesanan->nomor_antrian,
                'updated_at' => $pesanan->updated_at,
            ],
        ]);
    }

}
