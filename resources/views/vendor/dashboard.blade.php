@extends('layouts.app')

@push('styles')
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap4.min.css">
@endpush

@section('content')
<div class="page-header">
    <h3 class="page-title">Dasbor Vendor - Pesanan Masuk (Lunas)</h3>
</div>

@if (session('success'))
<div class="alert alert-success" role="alert">{{ session('success') }}</div>
@endif

@if (session('error'))
<div class="alert alert-danger" role="alert">{{ session('error') }}</div>
@endif

<div class="row">
    <div class="col-md-12 grid-margin stretch-card">
        <div class="card">
            <div class="card-body">
                <h4 class="card-title">Daftar Pesanan Lunas</h4>

                <div class="table-responsive">
                    <table id="tabelPesanan" class="table table-hover table-striped">
                        <thead>
                            <tr>
                                <th>Tanggal</th>
                                <th>Kode Pesanan</th>
                                <th>Nama Pembeli</th>
                                <th>Total</th>
                                <th>Status</th>
                                <th>Daftar Pesanan</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($pesanans as $pesanan)
                            <tr>
                                <td>{{ $pesanan->created_at->format('d M Y H:i') }}</td>
                                <td>{{ $pesanan->kode_pesanan }}</td>
                                <td>{{ $pesanan->nama }}</td>
                                <td>Rp {{ number_format((float) $pesanan->total, 0, ',', '.') }}</td>
                                <td>
                                    <span class="badge badge-success">Lunas</span>
                                </td>
                                <td>
                                    <ul class="mb-0 pl-3" style="font-size: 0.85rem;">
                                        @foreach($pesanan->detailPesanans as $detail)
                                        <li>{{ $detail->jumlah }}x {{ $detail->menu->nama_menu }}</li>
                                        @endforeach
                                    </ul>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" class="text-center">
                                    <div class="alert alert-info mb-0" role="alert">
                                        Belum ada pesanan lunas saat ini.
                                    </div>
                                </td>
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

@push('page-scripts')
<script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap4.min.js"></script>
<script>
    $(document).ready(function () {
        $('#tabelPesanan').DataTable();
    });
</script>
@endpush