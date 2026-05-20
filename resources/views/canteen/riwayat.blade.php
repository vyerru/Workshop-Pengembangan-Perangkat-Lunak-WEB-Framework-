@extends('layouts.app')

@section('content')
<div class="page-header">
    <h3 class="page-title">Riwayat Pesanan Saya</h3>
</div>

@forelse($pesanans as $pesanan)
<div class="row mb-3">
    <div class="col-md-12 grid-margin stretch-card">
        <div class="card">
            <div class="card-body">
                <div class="row">
                    <div class="col-md-12">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <h5 class="card-title mb-1">{{ $pesanan->kode_pesanan }}</h5>
                                <p class="text-muted mb-2">{{ $pesanan->created_at->format('d M Y H:i') }}</p>
                            </div>
                            <div class="text-right">
                                @if($pesanan->status_bayar === 0)
                                    <span class="badge badge-warning">Pending</span>
                                @elseif($pesanan->status_bayar === 1)
                                    <span class="badge badge-success">Lunas</span>
                                @else
                                    <span class="badge badge-danger">Batal</span>
                                @endif
                                <button type="button" class="btn btn-sm btn-outline-primary ml-2" data-bs-toggle="modal" data-bs-target="#qrModal" data-qr="{{ $pesanan->qr_token }}" data-kode="{{ $pesanan->kode_pesanan }}">
                                    <i class="mdi mdi-qrcode"></i> QR
                                </button>
                            </div>
                        </div>
                        <p class="mb-1"><strong>Vendor:</strong> {{ $pesanan->vendor->nama_vendor ?? '-' }}</p>
                        <p class="mb-0"><strong>Total:</strong> Rp {{ number_format((float) $pesanan->total, 0, ',', '.') }}</p>
                        <ul class="mb-0 pl-3 mt-2" style="font-size: 0.85rem;">
                            @foreach($pesanan->detailPesanans as $detail)
                            <li>{{ $detail->jumlah }}x {{ $detail->menu->nama_menu }} — Rp {{ number_format((float) $detail->subtotal, 0, ',', '.') }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@empty
<div class="row">
    <div class="col-md-12">
        <div class="alert alert-info" role="alert">Belum ada riwayat pesanan.</div>
    </div>
</div>
@endforelse

<!-- Modal QR -->
<div class="modal fade" id="qrModal" tabindex="-1" role="dialog" aria-labelledby="qrModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-sm modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header d-block text-center border-0 pb-0">
                <h5 class="modal-title" id="qrModalLabel">QR Code Pesanan</h5>
                <button type="button" class="btn-close position-absolute top-0 end-0 mt-2 me-2" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body text-center">
                <div id="modalQrContainer" class="d-flex justify-content-center mb-3"></div>
                <p id="modalKodePesanan" class="text-muted mb-0"></p>
            </div>
        </div>
    </div>
</div>
@endsection

@push('page-scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>
<script>
    $('#qrModal').on('shown.bs.modal', function (event) {
        const button = $(event.relatedTarget);
        const qrToken = button.data('qr');
        const kodePesanan = button.data('kode');
        $('#modalKodePesanan').text(kodePesanan);

        const container = document.getElementById('modalQrContainer');
        container.innerHTML = '';
        new QRCode(container, {
            text: qrToken,
            width: 200,
            height: 200,
        });
    });
</script>
@endpush

