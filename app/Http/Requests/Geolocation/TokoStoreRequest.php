<?php

namespace App\Http\Requests\Geolocation;

use Illuminate\Foundation\Http\FormRequest;

class TokoStoreRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check() && auth()->user()->hasRole('admin');
    }

    public function rules(): array
    {
        return [
            'nama_toko'  => 'required|string|max:100',
            'alamat'     => 'nullable|string|max:500',
            'latitude'   => 'nullable|numeric|between:-90,90',
            'longitude'  => 'nullable|numeric|between:-180,180',
            'accuracy'   => 'nullable|numeric|min:0|max:10000',
        ];
    }

    public function messages(): array
    {
        return [
            'latitude.between'  => 'Latitude harus antara -90 dan 90.',
            'longitude.between' => 'Longitude harus antara -180 dan 180.',
            'accuracy.max'      => 'Akurasi maksimal 10000 meter.',
        ];
    }
}
