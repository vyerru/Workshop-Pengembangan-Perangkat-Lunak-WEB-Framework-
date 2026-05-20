@extends('layouts.app')

@push('styles')
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap4.min.css">
<style>
    #reader {
        width: 50%;
        margin: 0 auto;
        border: 2px dashed #dee2e6;
        border-radius: 8px;
        padding: 16px;
        background: #f8f9fa;
    }
    #reader__dashboard_section_swiper button {
        background: #007bff;
        color: #fff;
        border: none;
        padding: 6px 16px;
        border-radius: 4px;
    }
    #reader__dashboard_section_swiper button:hover {
        background: #0056b3;
    }
</style>
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

<audio id="audioBeep" src="{{ asset('assets/audio/BEEP_Beep of a cash register (ID 1417)_BigSoundBank.com.mp3') }}" preload="auto"></audio>

<div class="row">
    <div class="col-md-12 grid-margin stretch-card">
        <div class="card">
            <div class="card-body">
                <h4 class="card-title">Pindai QR Code Pesanan</h4>
                <div id="reader"></div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-12 grid-margin stretch-card">
        <div class="card">
            <div class="card-body">
                <h4 class="card-title">Hasil Pindai</h4>
                <div class="table-responsive">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>Kode Pesanan</th>
                                <th>Pembeli</th>
                                <th>Total</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody id="hasilScanBody">
                            <tr id="hasilScanEmpty">
                                <td colspan="4" class="text-center text-muted">Belum ada hasil pindai.</td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <hr>

                <h5 class="mt-3">Detail Menu</h5>
                <div class="table-responsive">
                    <table class="table table-sm table-striped">
                        <thead>
                            <tr>
                                <th>Menu</th>
                                <th>Harga</th>
                                <th>Jumlah</th>
                                <th>Subtotal</th>
                                <th>Catatan</th>
                            </tr>
                        </thead>
                        <tbody id="detailMenuBody">
                            <tr id="detailMenuEmpty">
                                <td colspan="5" class="text-center text-muted">Belum ada detail menu.</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

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
<script src="https://unpkg.com/html5-qrcode@2.3.8/html5-qrcode.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
<script>
    axios.defaults.headers.common['X-CSRF-TOKEN'] = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    axios.defaults.headers.common['Accept'] = 'application/json';

    $(document).ready(function () {
        $('#tabelPesanan').DataTable();

        const audioBeep = document.getElementById('audioBeep');
        const hasilScanBody = document.getElementById('hasilScanBody');
        const hasilScanEmpty = document.getElementById('hasilScanEmpty');
        const detailMenuBody = document.getElementById('detailMenuBody');
        const detailMenuEmpty = document.getElementById('detailMenuEmpty');

        function renderHasil(pesanan, detailPesanan) {
            hasilScanEmpty.style.display = 'none';
            detailMenuEmpty.style.display = 'none';

            hasilScanBody.innerHTML = `
                <tr>
                    <td>${pesanan.kode_pesanan}</td>
                    <td>${pesanan.nama}</td>
                    <td>Rp ${new Intl.NumberFormat('id-ID').format(pesanan.total)}</td>
                    <td><span class="badge badge-success">${pesanan.status_bayar === 1 ? 'Lunas' : 'Pending'}</span></td>
                </tr>
            `;

            detailMenuBody.innerHTML = (detailPesanan || []).map(item => `
                <tr>
                    <td>${item.menu ? item.menu.nama_menu : '-'}</td>
                    <td>Rp ${new Intl.NumberFormat('id-ID').format(item.harga)}</td>
                    <td>${item.jumlah}</td>
                    <td>Rp ${new Intl.NumberFormat('id-ID').format(item.subtotal)}</td>
                    <td>${item.catatan || '-'}</td>
                </tr>
            `).join('');
        }

        function onScanSuccess(decodedText) {
            html5QrCode.stop().then(() => {
                audioBeep.currentTime = 0;
                audioBeep.play();

                axios.post('{{ route("vendor.verify-qr") }}', {
                    qr_token: decodedText
                }).then(function (response) {
                    if (response.data.status === 'success') {
                        renderHasil(response.data.data.pesanan, response.data.data.detail_pesanan);
                    }
                }).catch(function (error) {
                    let msg = 'Terjadi kesalahan tidak dikenal.';
                    if (error.response) {
                        if (error.response.status === 404) {
                            msg = 'QR Token tidak valid atau pesanan tidak ditemukan.';
                        } else if (error.response.status === 403) {
                            msg = 'Pesanan ini bukan milik vendor Anda.';
                        } else if (error.response.data && error.response.data.message) {
                            msg = error.response.data.message;
                        }
                    }
                    alert(msg);
                });
            }).catch(function (err) {
                console.error('Gagal menghentikan kamera:', err);
            });
        }

        const html5QrCode = new Html5Qrcode("reader");
        html5QrCode.start(
            { facingMode: "environment" },
            { fps: 10, qrbox: { width: 250, height: 250 } },
            onScanSuccess
        ).catch(function (err) {
            console.error('Gagal memulai kamera:', err);
            alert('Gagal mengakses kamera. Pastikan izin kamera diberikan.');
        });
    });
</script>
@endpush