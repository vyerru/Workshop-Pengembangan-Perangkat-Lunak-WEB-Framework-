@extends('layouts.app')

@section('content')
<div class="page-header">
    <h3 class="page-title">Riwayat Kunjungan</h3>
</div>

<div class="row">
    <div class="col-md-12 grid-margin stretch-card">
        <div class="card">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Toko</th>
                                <th>Waktu</th>
                                <th>Posisi Sales</th>
                                <th>Jarak</th>
                                <th>Threshold</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($kunjungans as $index => $k)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td>{{ $k->toko->nama_toko ?? '-' }}</td>
                                <td>{{ $k->waktu_kunjungan->format('d/m/Y H:i:s') }}</td>
                                <td>
                                    {{ $k->latitude_sales }}, {{ $k->longitude_sales }}
                                    <br><small class="text-muted">Acc: {{ $k->accuracy_sales }}m</small>
                                </td>
                                <td>{{ $k->jarak_terhitung }} m</td>
                                <td>{{ $k->threshold_efektif }} m</td>
                                <td>
                                    @if ($k->status === 'diterima')
                                        <span class="badge bg-success">DITERIMA</span>
                                    @else
                                        <span class="badge bg-danger">DITOLAK</span>
                                    @endif
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="7" class="text-center">Belum ada kunjungan.</td>
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
