<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Barang; // Sesuaikan dengan nama model barang Anda
use App\Models\Penjualan;
use App\Models\DetailPenjualan;
use Illuminate\Support\Facades\DB;

class PosController extends Controller
{
    public function ajax() { return view('pos.ajax'); }
    public function axios() { return view('pos.axios'); }

    // Mengambil data barang
    public function getBarang($id)
    {
        $barang = Barang::where('id_barang', $id)->first();
        if ($barang) {
            return response()->json(['status' => 'success', 'data' => $barang]);
        }
        return response()->json(['status' => 'error', 'message' => 'Barang tidak ditemukan'], 404);
    }

    // Menyimpan transaksi
    public function store(Request $request)
    {
        DB::beginTransaction();
        try {
            $penjualan = Penjualan::create([
                'total' => $request->total
            ]);

            foreach ($request->items as $item) {
                DetailPenjualan::create([
                    'id_penjualan' => $penjualan->id_penjualan,
                    'id_barang' => $item['id_barang'],
                    'jumlah' => $item['jumlah'],
                    'subtotal' => $item['subtotal']
                ]);
            }
            DB::commit();
            return response()->json(['status' => 'success', 'message' => 'Transaksi berhasil disimpan!']);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }
}