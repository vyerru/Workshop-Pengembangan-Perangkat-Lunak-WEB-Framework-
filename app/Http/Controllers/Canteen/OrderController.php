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
        
        // JIKA Anda mengikat menu ke tabel vendors (vendor_id), gunakan ini:
        // $menus = Menu::where('vendor_id', $vendor_id)->get();

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
       $nama_customer = auth()->check() ? auth()->user()->name : 'Guest_' . str_pad(rand(1, 9999), 5, '0', STR_PAD_LEFT);
        $kode_pesanan = 'ORD-' . strtoupper(Str::random(5)) . '-' . time();
        
        $pesanan = Pesanan::create([
            'nama' => $nama_customer,
            'vendor_id' => $vendor_id,
            'kode_pesanan' => $kode_pesanan,
            'total' => $total_harga,
            'status_bayar' => 1, // 1 = Lunas
            'metode_bayar' => 'QRIS',
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
        // \Midtrans\Config::$serverKey = config('midtrans.server_key');
        // \Midtrans\Config::$isProduction = config('midtrans.is_production');
        // \Midtrans\Config::$isSanitized = config('midtrans.is_sanitized');
        // \Midtrans\Config::$is3ds = config('midtrans.is_3ds');

        // \Midtrans\Config::$serverKey = 'SB-Mid-server-doRA1XoMosZ5gmyMwqbtld8u'; 
        // \Midtrans\Config::$isProduction = false; // Paksa mutlak ke false
        // \Midtrans\Config::$isSanitized = true;
        // \Midtrans\Config::$is3ds = true;

        // if (empty(\Midtrans\Config::$serverKey)) {
        //      throw new \Exception("DIAGNOSIS FATAL: Server Key terbaca KOSONG (null) oleh Laravel. Cek cache config atau file .env Anda.");
        // }
        // if (strpos(\Midtrans\Config::$serverKey, 'SB-Mid-server-') === false) {
        //      throw new \Exception("DIAGNOSIS FATAL: Format Server Key SALAH. Nilai yang dikirim adalah: " . \Midtrans\Config::$serverKey . ". Seharusnya diawali dengan 'SB-Mid-server-'.");
        // }

        // $params = [
        //     'transaction_details' => [
        //         'order_id' => $pesanan->kode_pesanan,
        //         'gross_amount' => $pesanan->total,
        //     ],
        //     'customer_details' => [
        //         'first_name' => auth()->user()->name,
        //         'email' => auth()->user()->email,
        //     ]
        // ];

        // $snapToken = \Midtrans\Snap::getSnapToken($params);
        
        // // Simpan token ke database untuk referensi (opsional tapi disarankan)
        // $pesanan->update(['snap_token' => $snapToken]);

        DB::commit();

        return response()->json([
            'status' => 'success',
            'message' => 'Pembayaran berhasil disimulasikan. Pesanan Lunas.'
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