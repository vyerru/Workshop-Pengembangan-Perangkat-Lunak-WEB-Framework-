<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Http;
use Illuminate\Http\Request;

class WilayahController extends Controller
{
    private $baseUrl = 'https://emsifa.github.io/api-wilayah-indonesia/api';

    public function provinsi()
    {
        return Http::get($this->baseUrl . '/provinces.json')->json();
    }

    public function kota($id)
    {
        return Http::get($this->baseUrl . "/regencies/$id.json")->json();
    }

    public function kecamatan($id)
    {
        return Http::get($this->baseUrl . "/districts/$id.json")->json();
    }

    public function kelurahan($id)
    {
        return Http::get($this->baseUrl . "/villages/$id.json")->json();
    }
}