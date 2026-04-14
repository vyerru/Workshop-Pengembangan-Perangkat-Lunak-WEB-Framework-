<?php

namespace App\Http\Controllers\Canteen;

use App\Http\Controllers\Controller;
use App\Models\Menu;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class VendorMenuController extends Controller
{
    public function index()
    {
        // Pastikan vendor tidak null sebelum panggil menus()
        $vendor = Auth::user()->vendor;

        if (!$vendor) {
            return "Gagal: Akun Anda tidak terdaftar sebagai profil Vendor di database.";
        }

        $menus = $vendor->menus()->get();
        return view('vendor.menus.index', compact('menus'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_menu'   => 'required|string|max:255',
            'harga'       => 'required|integer|min:100',
            'path_gambar' => 'nullable|image|mimes:jpg,jpeg,png|max:2048', // Diubah jadi nullable
        ]);

        $imagePath = null;
        if ($request->hasFile('path_gambar')) {
            $imagePath = $request->file('path_gambar')->store('menu_images', 'public');
        }

        // Gunakan vendor_id dan simpan melalui relasi
        Auth::user()->vendor->menus()->create([
            'nama_menu'   => $request->nama_menu,
            'harga'       => $request->harga,
            'path_gambar' => $imagePath, // Akan bernilai null jika tidak ada upload
        ]);

        return redirect()->route('vendor.menus.index')->with('success', 'Menu berhasil disimpan.');
    }
}
