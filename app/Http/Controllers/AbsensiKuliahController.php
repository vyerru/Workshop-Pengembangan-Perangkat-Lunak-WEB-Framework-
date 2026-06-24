<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AbsensiKuliahController extends Controller
{
    public function index()
    {
        return view('absensi-nfc.scan');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nfc_uid' => 'required|string',
            'topik_mingguan' => 'required|string',
            'pertemuan_ke' => 'required|string'
        ]);

        $user = auth()->user();

        $user->update(['nfc_uid' => $request->nfc_uid]);

        DB::table('absensi_kuliahs')->insert([
            'user_id' => $user->id,
            'topik_mingguan' => $request->topik_mingguan,
            'pertemuan_ke' => $request->pertemuan_ke,
            'waktu_hadir' => now(),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Kehadiran dicatat untuk: ' . $user->name
        ], 200);
    }
}
