@extends('layouts.app')

@section('content')
<div class="page-header d-flex justify-content-between align-items-center">
    <h3 class="page-title">
        <span class="page-title-icon bg-gradient-primary text-white me-2">
            <i class="mdi mdi-wifi"></i>
        </span> Rekap Absensi — Pertemuan {{ $pertemuan->pertemuan_ke }}: {{ $pertemuan->topik }}
    </h3>
    <a href="{{ route('absen.admin.index') }}" class="btn btn-sm btn-outline-secondary">Kembali</a>
</div>

<div class="row">
    <div class="col-md-8 grid-margin stretch-card mx-auto">
        <div class="card">
            <div class="card-body">
                <p class="text-muted">Tanggal: {{ $pertemuan->tanggal->format('d M Y') }}</p>
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Nama</th>
                                <th>NFC UID</th>
                                <th>Waktu Hadir</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($absensis as $a)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $a->user->name }}</td>
                                <td><code>{{ $a->nfc_uid }}</code></td>
                                <td>{{ $a->waktu_hadir->format('H:i:s') }}</td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="4" class="text-center text-muted">Belum ada mahasiswa yang hadir.</td>
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
