<?php

namespace App\Http\Controllers\Geolocation;

use App\Http\Controllers\Controller;
use App\Http\Requests\Geolocation\KunjunganStoreRequest;
use App\Models\Kunjungan;
use App\Models\Toko;
use App\Services\GeolocationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class KunjunganController extends Controller
{
    public function __construct(
        protected GeolocationService $geolocationService
    ) {}

    public function index(): View
    {
        $tokos = Toko::whereNotNull('latitude')
            ->whereNotNull('longitude')
            ->orderBy('nama_toko')
            ->get();

        return view('geolocation.index', compact('tokos'));
    }

    public function scan(): View
    {
        return view('geolocation.scan');
    }

    public function scanBarcode(): View
    {
        return view('geolocation.scan-barcode');
    }

    public function getTokoByBarcode(string $barcodeToken): JsonResponse
    {
        $toko = Toko::where('barcode_token', $barcodeToken)->first();

        if (!$toko) {
            return response()->json([
                'status' => 'error',
                'message' => 'Toko tidak ditemukan.',
            ], 404);
        }

        return response()->json([
            'status' => 'success',
            'data'   => [
                'id'            => $toko->id,
                'nama_toko'     => $toko->nama_toko,
                'alamat'        => $toko->alamat,
                'barcode_token' => $toko->barcode_token,
                'latitude'      => $toko->latitude,
                'longitude'     => $toko->longitude,
            ],
        ]);
    }

    public function store(KunjunganStoreRequest $request): JsonResponse
    {
        $toko = Toko::where('barcode_token', $request->barcode_token)->firstOrFail();

        if (is_null($toko->latitude) || is_null($toko->longitude)) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Toko belum memiliki titik lokasi. Hubungi admin.',
            ], 422);
        }

        $recent = Kunjungan::where('toko_id', $toko->id)
            ->where('sales_id', auth()->id())
            ->where('waktu_kunjungan', '>=', now()->subMinutes(5))
            ->exists();

        if ($recent) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Toko ini sudah dikunjungi dalam 5 menit terakhir.',
            ], 429);
        }

        $accToko = $toko->accuracy ?? 0;
        $maxJarak = config('geolocation.max_distance', 100);

        $jarak = $this->geolocationService->haversineDistance(
            $request->latitude_sales,
            $request->longitude_sales,
            (float) $toko->latitude,
            (float) $toko->longitude
        );

        $threshold = $this->geolocationService->hitungThresholdEfektif(
            $maxJarak,
            $accToko,
            $request->accuracy_sales
        );

        $diterima = $this->geolocationService->isValid($jarak, $threshold);

        Kunjungan::create([
            'toko_id'          => $toko->id,
            'sales_id'         => auth()->id(),
            'latitude_sales'   => $request->latitude_sales,
            'longitude_sales'  => $request->longitude_sales,
            'accuracy_sales'   => $request->accuracy_sales,
            'latitude_toko'    => $toko->latitude,
            'longitude_toko'   => $toko->longitude,
            'accuracy_toko'    => $accToko,
            'jarak_terhitung'  => round($jarak, 2),
            'threshold_efektif' => round($threshold, 2),
            'status'           => $diterima ? 'diterima' : 'ditolak',
            'waktu_kunjungan'  => now(),
        ]);

        $message = $diterima
            ? 'Kunjungan DITERIMA'
            : 'Kunjungan DITOLAK (Jarak: ' . round($jarak, 1) . 'm, Threshold: ' . round($threshold, 1) . 'm)';

        return response()->json([
            'status'  => $diterima ? 'diterima' : 'ditolak',
            'message' => $message,
            'data'    => [
                'jarak'     => round($jarak, 2),
                'threshold' => round($threshold, 2),
                'acc_toko'  => $accToko,
                'acc_sales' => $request->accuracy_sales,
                'latitude_sales'  => $request->latitude_sales,
                'longitude_sales' => $request->longitude_sales,
            ],
        ]);
    }

    public function riwayat(): View
    {
        $kunjungans = Kunjungan::with('toko')
            ->where('sales_id', auth()->id())
            ->orderByDesc('waktu_kunjungan')
            ->get();

        return view('geolocation.riwayat', compact('kunjungans'));
    }
}
