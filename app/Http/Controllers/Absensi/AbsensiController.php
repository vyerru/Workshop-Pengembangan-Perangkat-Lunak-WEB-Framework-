<?php

namespace App\Http\Controllers\Absensi;

use App\Http\Controllers\Controller;
use App\Models\Absensi\Absensi;
use App\Models\Absensi\Pertemuan;
use Illuminate\Http\Request;

class AbsensiController extends Controller
{
    public function indexMahasiswa()
    {
        $pertemuans = Pertemuan::orderBy('tanggal', 'desc')->get();
        $riwayat = Absensi::with('pertemuan')
            ->where('user_id', auth()->id())
            ->orderBy('waktu_hadir', 'desc')
            ->get();

        return view('absensi.scan', compact('pertemuans', 'riwayat'));
    }

    public function scan(Request $request)
    {
        $request->validate([
            'pertemuan_id' => 'required|exists:pertemuans,id',
            'nfc_uid' => 'required|string',
        ]);

        $exists = Absensi::where('user_id', auth()->id())
            ->where('pertemuan_id', $request->pertemuan_id)
            ->exists();

        if ($exists) {
            return response()->json([
                'success' => false,
                'message' => 'Anda sudah absen pada pertemuan ini.',
            ], 422);
        }

        Absensi::create([
            'user_id' => auth()->id(),
            'pertemuan_id' => $request->pertemuan_id,
            'nfc_uid' => $request->nfc_uid,
            'waktu_hadir' => now(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Kehadiran tercatat untuk: ' . auth()->user()->name,
        ]);
    }

    public function adminIndex()
    {
        $pertemuans = Pertemuan::withCount('absensis')
            ->orderBy('tanggal', 'desc')
            ->get();

        return view('absensi.admin', compact('pertemuans'));
    }

    public function adminStorePertemuan(Request $request)
    {
        $request->validate([
            'pertemuan_ke' => 'required|integer|min:1',
            'topik' => 'required|string|max:255',
            'tanggal' => 'required|date',
        ]);

        Pertemuan::create($request->only(['pertemuan_ke', 'topik', 'tanggal']));

        return redirect()->route('absen.admin.index')
            ->with('success', 'Pertemuan berhasil dibuat.');
    }

    public function adminRekap(Pertemuan $pertemuan)
    {
        $absensis = Absensi::with('user')
            ->where('pertemuan_id', $pertemuan->id)
            ->orderBy('waktu_hadir', 'asc')
            ->get();

        return view('absensi.rekap', compact('pertemuan', 'absensis'));
    }
}
