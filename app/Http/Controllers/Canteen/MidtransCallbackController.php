<?php

namespace App\Http\Controllers\Canteen;

use App\Http\Controllers\Controller;
use App\Models\Pesanan;
use Illuminate\Http\Request;

class MidtransCallbackController extends Controller
{
    public function notification(Request $request)
    {
        $serverKey = config('midtrans.server_key');
        $hashed = hash('sha512', $request->order_id . $request->status_code . $request->gross_amount . $serverKey);

        if ($hashed !== $request->signature_key) {
            return response()->json(['status' => 'invalid signature'], 403);
        }

        $pesanan = Pesanan::where('kode_pesanan', $request->order_id)->first();
        if (!$pesanan) {
            return response()->json(['status' => 'order not found'], 404);
        }

        $transactionStatus = $request->transaction_status;
        $paymentType = $request->payment_type;

        if ($transactionStatus === 'capture' || $transactionStatus === 'settlement') {
            $pesanan->update([
                'status_bayar' => 1,
                'transaction_id' => $request->transaction_id,
                'metode_bayar' => $paymentType,
            ]);
        } elseif (in_array($transactionStatus, ['deny', 'cancel', 'expire'])) {
            $pesanan->update([
                'status_bayar' => 2,
                'transaction_id' => $request->transaction_id,
            ]);
        }

        return response()->json(['status' => 'ok']);
    }
}