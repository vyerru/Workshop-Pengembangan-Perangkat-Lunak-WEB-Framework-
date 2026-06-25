<?php

// app/Http/Controllers/Canteen/OrderController.php
namespace App\Http\Controllers\Canteen;

use App\Http\Controllers\Controller;
use App\Models\Vendor;
use App\Models\Menu;
use Illuminate\Http\Request;
use App\Models\Pesanan;
use App\Models\DetailPesanan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class OrderController extends Controller
{
    // Menampilkan halaman utama POS
    public function index()
    {
        // Ambil semua vendor untuk dropdown
        $vendors = Vendor::all(); 
        return view('canteen.pos.index', compact('vendors'));
    }

    // Endpoint API/AJAX untuk mengambil menu berdasarkan ID Vendor
    public function getMenus($vendor_id)
    {
        // Pastikan vendor ada, lalu ambil menu-menunya
        $menus = Menu::where('vendor_id', $vendor_id)->get(); // Ingat, tabel menu Anda berelasi dengan user_id (vendor auth), bukan ID di tabel vendors. Sesuaikan dengan struktur relasi Anda jika berbeda.
        return response()->json([
            'status' => 'success',
            'data' => $menus
        ]);
    }

    // Endpoint untuk memproses form checkout (Nanti diisi logika Midtrans)
    public function checkout(Request $request)
{
    // 1. Validasi struktur payload dari frontend
    $request->validate([
        'vendor_id' => 'required|exists:vendors,id',
        'cart' => 'required|array|min:1',
        'cart.*.menu_id' => 'required|exists:menus,id',
        'cart.*.jumlah' => 'required|integer|min:1',
    ]);

    // Gunakan Database Transaction agar jika Midtrans gagal, data tidak tersimpan setengah-setengah.
    DB::beginTransaction();
    try {
        $vendor_id = $request->vendor_id;
        $total_harga = 0;
        $cart_items = [];

        // 2. Hitung ulang total harga secara mutlak dari database
        foreach ($request->cart as $item) {
            $menu = Menu::where('vendor_id', $vendor_id)->findOrFail($item['menu_id']);
            $subtotal = $menu->harga * $item['jumlah'];
            $total_harga += $subtotal;

            $cart_items[] = [
                'menu_id' => $menu->id,
                'nama_menu' => $menu->nama_menu,
                'harga' => $menu->harga,
                'jumlah' => $item['jumlah'],
                'subtotal' => $subtotal,
            ];
        }

        // 3. Buat Record Pesanan
        $nama_customer = $request->nama ?? auth()->user()->name;
        $kode_pesanan = 'ORD-' . strtoupper(Str::random(5)) . '-' . time();

        // Generate nomor antrian harian per vendor
        $lastAntrian = Pesanan::where('vendor_id', $vendor_id)
            ->whereDate('created_at', today())
            ->max('nomor_antrian');
        $nomorAntrian = ($lastAntrian ?? 0) + 1;
        
        $pesanan = Pesanan::create([
            'user_id' => auth()->id(),
            'nama' => $nama_customer,
            'vendor_id' => $vendor_id,
            'kode_pesanan' => $kode_pesanan,
            'qr_token' => Str::random(40),
            'total' => $total_harga,
            'status_bayar' => 0, // 0 = pending, menunggu konfirmasi Midtrans
            'metode_bayar' => null,
            'nomor_antrian' => $nomorAntrian,
            'status_antrian' => Pesanan::ANTRIAN_PENDING,
        ]);

        // 4. Buat Detail Pesanan
        foreach ($cart_items as $item) {
            \App\Models\DetailPesanan::create([
                'pesanan_id' => $pesanan->id,
                'menu_id' => $item['menu_id'],
                'jumlah' => $item['jumlah'],
                'harga' => $item['harga'],
                'subtotal' => $item['subtotal'],
            ]);
        }

        // 5. Konfigurasi Midtrans & Dapatkan Snap Token
        \Midtrans\Config::$serverKey = config('midtrans.server_key');
        \Midtrans\Config::$isProduction = config('midtrans.is_production');
        \Midtrans\Config::$isSanitized = config('midtrans.is_sanitized');
        \Midtrans\Config::$is3ds = config('midtrans.is_3ds');

        $params = [
            'transaction_details' => [
                'order_id' => $pesanan->kode_pesanan,
                'gross_amount' => $pesanan->total,
            ],
            'customer_details' => [
                'first_name' => auth()->user()->name,
                'email' => auth()->user()->email,
            ],
            'callbacks' => [
                'finish' => route('canteen.riwayat'),
            ],
        ];

        $snapToken = \Midtrans\Snap::getSnapToken($params);
        
        // Simpan token ke database untuk referensi
        $pesanan->update(['snap_token' => $snapToken]);

        DB::commit();

        $qrSvg = QrCode::size(150)->generate($pesanan->qr_token);

        return response()->json([
            'status' => 'success',
            'snap_token' => $snapToken,
            'id_pesanan' => $pesanan->kode_pesanan,
            'nomor_antrian' => $pesanan->nomor_antrian,
            'qr_html' => (string) $qrSvg,
        ]);

    } catch (\Exception $e) {
        DB::rollBack();
        return response()->json([
            'status' => 'error',
            'message' => 'Gagal memproses pesanan: ' . $e->getMessage()
        ], 500);
    }
}
}