@extends('layouts.app')

@section('content')
<div class="page-header">
    <h3 class="page-title">Manajemen Toko</h3>
    <a href="{{ route('geolocation.toko.create') }}" class="btn btn-primary">
        <i class="mdi mdi-plus"></i> Tambah Toko
    </a>
</div>

@if (session('success'))
<div class="alert alert-success alert-dismissible fade show" role="alert">
    {{ session('success') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
@endif

<div class="row">
    <div class="col-md-12 grid-margin stretch-card">
        <div class="card">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Nama Toko</th>
                                <th>Alamat</th>
                                <th>Latitude</th>
                                <th>Longitude</th>
                                <th>Accuracy</th>
                                <th>Barcode / QR</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($tokos as $index => $toko)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td>{{ $toko->nama_toko }}</td>
                                <td>{{ $toko->alamat ?? '-' }}</td>
                                <td>{{ $toko->latitude ?? '-' }}</td>
                                <td>{{ $toko->longitude ?? '-' }}</td>
                                <td>{{ $toko->accuracy ? $toko->accuracy . ' m' : '-' }}</td>
                                <td>
                                    <button type="button"
                                            class="btn btn-sm btn-outline-primary"
                                            onclick="tampilBarcodeQr('{{ route('geolocation.toko.barcode', $toko) }}', 'Barcode - {{ $toko->nama_toko }}')">
                                        Barcode
                                    </button>
                                    <button type="button"
                                            class="btn btn-sm btn-outline-info"
                                            onclick="tampilBarcodeQr('{{ route('geolocation.toko.qrcode', $toko) }}', 'QR Code - {{ $toko->nama_toko }}')">
                                        QR Code
                                    </button>
                                </td>
                                <td>
                                    <a href="{{ route('geolocation.toko.edit', $toko) }}"
                                       class="btn btn-sm btn-warning">Edit</a>
                                    <form action="{{ route('geolocation.toko.destroy', $toko) }}" method="POST"
                                          class="d-inline"
                                          onsubmit="return confirm('Hapus toko ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button class="btn btn-sm btn-danger">Hapus</button>
                                    </form>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="8" class="text-center">Belum ada toko.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="modalBarcodeQr" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalBarcodeQrLabel">Barcode / QR Code</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body text-center">
                <img id="imgBarcodeQr" src="" alt="Barcode / QR Code" class="img-fluid" style="max-height: 400px;">
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" onclick="cetakBarcodeQr()">
                    <i class="mdi mdi-printer"></i> Cetak
                </button>
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>

<iframe id="frameCetak" style="display:none;"></iframe>

@push('page-scripts')
<script>
function tampilBarcodeQr(url, title) {
    document.getElementById('modalBarcodeQrLabel').textContent = title;
    document.getElementById('imgBarcodeQr').src = url;
    var modal = new bootstrap.Modal(document.getElementById('modalBarcodeQr'));
    modal.show();
}

function cetakBarcodeQr() {
    var img = document.getElementById('imgBarcodeQr');
    var iframe = document.getElementById('frameCetak');
    var iframeDoc = iframe.contentDocument || iframe.contentWindow.document;
    iframeDoc.open();
    iframeDoc.write('\
        <html><head><style>\
            body { text-align: center; padding: 20px; }\
            img { max-width: 100%; height: auto; }\
        </style></head><body>\
            <img src="' + img.src + '">\
            <script>window.onload = function() { window.print(); }<' + '/script>\
        </body></html>');
    iframeDoc.close();
}
</script>
@endpush
@endsection
