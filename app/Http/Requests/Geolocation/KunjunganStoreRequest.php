<?php

namespace App\Http\Requests\Geolocation;

use Illuminate\Foundation\Http\FormRequest;

class KunjunganStoreRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check() && auth()->user()->hasRole('sales');
    }

    public function rules(): array
    {
        return [
            'barcode_token'   => 'required|string|exists:tokos,barcode_token',
            'latitude_sales'  => 'required|numeric|between:-90,90',
            'longitude_sales' => 'required|numeric|between:-180,180',
            'accuracy_sales'  => 'required|numeric|min:0|max:10000',
        ];
    }

    public function messages(): array
    {
        return [
            'barcode_token.exists'    => 'Toko tidak ditemukan.',
            'latitude_sales.required' => 'Posisi Anda tidak valid. Coba ambil ulang lokasi.',
            'longitude_sales.required' => 'Posisi Anda tidak valid. Coba ambil ulang lokasi.',
            'accuracy_sales.required' => 'Akurasi GPS tidak ditemukan.',
            'accuracy_sales.max'      => 'Akurasi GPS terlalu rendah. Coba di tempat terbuka.',
        ];
    }
}
