@extends('layouts.app')

@push('styles')
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
    #reader__scan_region img {
        display: block;
        margin: 0 auto;
    }
    #reader video {
        transform: scaleX(-1);
    }
    #hasilScan {
        display: none;
    }
</style>
@endpush

@section('content')
<div class="page-header">
    <h3 class="page-title">Scanner Barcode Barang</h3>
</div>

<audio id="audioBeep" src="{{ asset('assets/audio/BEEP_Beep of a cash register (ID 1417)_BigSoundBank.com.mp3') }}" preload="auto"></audio>

<div class="row">
    <div class="col-md-12 grid-margin stretch-card">
        <div class="card">
            <div class="card-body">
                <h4 class="card-title">Arahkan Kamera ke Barcode</h4>
                <div id="reader"></div>
            </div>
        </div>
    </div>
</div>

<div class="row" id="hasilScan">
    <div class="col-md-12 grid-margin stretch-card">
        <div class="card">
            <div class="card-body">
                <h4 class="card-title">Hasil Scan</h4>
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>ID Barang</th>
                            <th>Nama Barang</th>
                            <th>Harga</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td id="scanIdBarang"></td>
                            <td id="scanNamaBarang"></td>
                            <td id="scanHarga"></td>
                        </tr>
                    </tbody>
                </table>
                <button class="btn btn-primary mt-3" onclick="restartScanner()">Scan Lagi</button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('page-scripts')
<script src="https://unpkg.com/html5-qrcode@2.3.8/html5-qrcode.min.js"></script>
<script>
    const audioBeep = document.getElementById('audioBeep');
    const hasilScan = document.getElementById('hasilScan');
    const scanIdBarang = document.getElementById('scanIdBarang');
    const scanNamaBarang = document.getElementById('scanNamaBarang');
    const scanHarga = document.getElementById('scanHarga');

    let html5QrCode = null;

    function startScanner() {
        html5QrCode = new Html5Qrcode("reader");
        html5QrCode.start(
            { facingMode: "environment" },
            { fps: 10, qrbox: { width: 250, height: 80 } },
            onScanSuccess
        ).catch(function (err) {
            console.error('Gagal memulai kamera:', err);
            alert('Gagal mengakses kamera. Pastikan izin kamera diberikan.');
        });
    }

    function onScanSuccess(decodedText) {
        html5QrCode.stop().then(() => {
            audioBeep.currentTime = 0;
            audioBeep.play();

            axios.get('/barang/api/' + encodeURIComponent(decodedText))
                .then(function (response) {
                    if (response.data.status === 'success') {
                        const d = response.data.data;
                        scanIdBarang.textContent = d.id_barang;
                        scanNamaBarang.textContent = d.nama_barang;
                        scanHarga.textContent = 'Rp ' + new Intl.NumberFormat('id-ID').format(d.harga);
                        hasilScan.style.display = 'block';
                        $('#reader').closest('.card').hide();
                    }
                })
                .catch(function (error) {
                    let msg = 'Barcode tidak dikenal.';
                    if (error.response && error.response.status === 404) {
                        msg = 'Barang dengan kode tersebut tidak ditemukan.';
                    } else if (error.response && error.response.data && error.response.data.message) {
                        msg = error.response.data.message;
                    }
                    alert(msg);
                    restartScanner();
                });
        }).catch(function (err) {
            console.error('Gagal menghentikan kamera:', err);
        });
    }

    function restartScanner() {
        hasilScan.style.display = 'none';
        $('#reader').closest('.card').show();
        if (html5QrCode) {
            html5QrCode.stop().then(() => startScanner()).catch(() => startScanner());
        } else {
            startScanner();
        }
    }

    $(document).ready(function () {
        startScanner();
    });
</script>
@endpush
