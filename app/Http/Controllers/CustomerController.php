<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Customer;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class CustomerController extends Controller
{
    /**
     * Memuat antarmuka penangkap biometrik.
     */
    public function customerSatu()
    {
        // Pastikan hirarki file view-mu benar: resources/views/customer/customer1.blade.php
        return view('customer.customer1'); 
    }

    public function customerDua()
    {
        return view('customer.customer2'); 
    }

    /**
     * Memanipulasi payload Base64 dan mengelola Disk I/O.
     */
    public function store(Request $request)
    {
        // Ekstrak data base64 (Formatnya: "data:image/jpeg;base64,/9j/4AAQSkZJRg...")
        $fotoBase64 = $request->foto_base64;
        $skenario = $request->skenario; // 'blob' atau 'path'

        $customerData = [
            'nama' => $request->nama,
            'alamat' => $request->alamat,
            // ... ekstrak field lainnya sesuai dengan input form-mu ...
        ];

        if ($fotoBase64) {
            if ($skenario === 'blob') {
                // SKENARIO 1: Anti-pattern. Menyuntikkan raw base64 langsung ke memori tabel.
                $customerData['foto_blob'] = $fotoBase64; 
                
            } elseif ($skenario === 'path') {
                // SKENARIO 2: Membelah header MIME dan mendekode string menjadi binary fisik.
                $image_parts = explode(";base64,", $fotoBase64);
                
                if (count($image_parts) >= 2) {
                    $image_base64 = base64_decode($image_parts[1]);
                    
                    // Generate nama file unik menggunakan UUID
                    $fileName = 'customers/' . Str::uuid() . '.jpg';
                    
                    // Tulis file fisik ke local storage (disk 'public')
                    Storage::disk('public')->put($fileName, $image_base64);
                    
                    // Rekam path direktori ke database
                    $customerData['foto_path'] = $fileName;
                }
            }
        }

        Customer::create($customerData);

        return redirect()->back()->with('success', 'Data Customer berhasil disimpan dengan skenario: ' . $skenario);
    }
}
