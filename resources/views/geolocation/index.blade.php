@extends('layouts.app')

@section('content')
<div class="page-header">
    <h3 class="page-title">Kunjungan Toko</h3>
</div>

<div class="row">
    <div class="col-md-12 grid-margin stretch-card">
        <div class="card">
            <div class="card-body">
                <h4 class="card-title">Daftar Toko Terdekat</h4>
                <div class="table-responsive">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>Nama Toko</th>
                                <th>Alamat</th>
                                <th>Koordinat</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($tokos as $toko)
                            <tr>
                                <td>{{ $toko->nama_toko }}</td>
                                <td>{{ $toko->alamat ?? '-' }}</td>
                                <td>
                                    {{ $toko->latitude }}, {{ $toko->longitude }}
                                </td>
                                <td>
                                    <a href="{{ route('geolocation.kunjungan.scan-barcode') }}"
                                       class="btn btn-sm btn-outline-success">
                                        <i class="mdi mdi-barcode"></i> Barcode
                                    </a>
                                    <a href="{{ route('geolocation.kunjungan.scan') }}"
                                       class="btn btn-sm btn-outline-info">
                                        <i class="mdi mdi-qrcode"></i> QR Code
                                    </a>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="4" class="text-center">Belum ada toko tersedia.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
