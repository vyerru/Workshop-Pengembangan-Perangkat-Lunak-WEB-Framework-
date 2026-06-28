@extends('layouts.app')

@section('content')
<div class="page-header">
    <h3 class="page-title">
        <span class="page-title-icon bg-gradient-primary text-white me-2">
            <i class="mdi mdi-wifi"></i>
        </span> Admin Absensi NFC
    </h3>
</div>

@if (session('success'))
<div class="alert alert-success">{{ session('success') }}</div>
@endif

<div class="row">
    <div class="col-md-6 grid-margin stretch-card">
        <div class="card">
            <div class="card-body">
                <h4 class="card-title">Buat Pertemuan Baru</h4>
                <form method="POST" action="{{ route('absen.admin.pertemuan.store') }}">
                    @csrf
                    <div class="form-group">
                        <label>Pertemuan Ke-</label>
                        <input type="number" name="pertemuan_ke" class="form-control" min="1" required>
                    </div>
                    <div class="form-group">
                        <label>Topik</label>
                        <input type="text" name="topik" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label>Tanggal</label>
                        <input type="date" name="tanggal" class="form-control" value="{{ date('Y-m-d') }}" required>
                    </div>
                    <button type="submit" class="btn btn-gradient-primary">Simpan</button>
                </form>
            </div>
        </div>
    </div>

    <div class="col-md-6 grid-margin stretch-card">
        <div class="card">
            <div class="card-body">
                <h4 class="card-title">Daftar Pertemuan</h4>
                <div class="table-responsive">
                    <table class="table table-sm">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Topik</th>
                                <th>Tanggal</th>
                                <th>Hadir</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($pertemuans as $p)
                            <tr>
                                <td>{{ $p->pertemuan_ke }}</td>
                                <td>{{ $p->topik }}</td>
                                <td>{{ $p->tanggal->format('d M Y') }}</td>
                                <td>{{ $p->absensis_count }}</td>
                                <td>
                                    <a href="{{ route('absen.admin.rekap', $p->id) }}" class="btn btn-sm btn-outline-primary">Rekap</a>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="text-center text-muted">Belum ada pertemuan.</td>
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
