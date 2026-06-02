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
    #formKunjungan { display: none; }
    #hasilKunjungan { display: none; }
    #lokasiLoading { display: none; }
</style>
@endpush

@section('content')
<div class="page-header">
    <h3 class="page-title">Scan QR Code Toko</h3>
</div>

<audio id="audioBeep" src="{{ asset('assets/audio/BEEP_Beep of a cash register (ID 1417)_BigSoundBank.com.mp3') }}" preload="auto"></audio>

<div class="row">
    <div class="col-md-12 grid-margin stretch-card">
        <div class="card">
            <div class="card-body">
                <h4 class="card-title">Arahkan Kamera ke QR Code Toko</h4>
                <div id="reader"></div>
            </div>
        </div>
    </div>
</div>

<div class="row" id="formKunjungan">
    <div class="col-md-12 grid-margin stretch-card">
        <div class="card">
            <div class="card-body">
                <h4 class="card-title">Data Toko</h4>
                <table class="table table-bordered">
                    <tr><th style="width:150px">Nama Toko</th><td id="tokoNama"></td></tr>
                    <tr><th>Alamat</th><td id="tokoAlamat"></td></tr>
                    <tr><th>Koordinat Toko</th><td id="tokoKoordinat"></td></tr>
                </table>
                <div id="lokasiLoading" class="alert alert-info mt-3">
                    <div class="spinner-border spinner-border-sm me-2" role="status"></div>
                    Mengambil posisi GPS ...
                </div>
                <div id="lokasiStatus" class="alert mt-3" style="display:none"></div>
                <div class="mt-4">
                    <button id="btnAmbilLokasi" class="btn btn-primary btn-lg">
                        <i class="mdi mdi-crosshairs-gps"></i> Ambil Lokasi & Kirim
                    </button>
                    <button class="btn btn-secondary btn-lg" onclick="resetForm()">
                        <i class="mdi mdi-camera"></i> Scan Lagi
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row" id="hasilKunjungan">
    <div class="col-md-12 grid-margin stretch-card">
        <div class="card">
            <div class="card-body text-center">
                <div id="alertHasil" class="alert">
                    <h1 id="statusIcon" class="display-1"></h1>
                    <h2 id="statusText"></h2>
                    <hr>
                    <div class="row mt-4">
                        <div class="col-md-3"><small class="text-muted">Latitude</small><p class="fw-bold" id="hasilLat">-</p></div>
                        <div class="col-md-3"><small class="text-muted">Longitude</small><p class="fw-bold" id="hasilLng">-</p></div>
                        <div class="col-md-3"><small class="text-muted">Akurasi GPS</small><p class="fw-bold" id="hasilAcc">-</p></div>
                        <div class="col-md-3"><small class="text-muted">Jarak ke Toko</small><p class="fw-bold" id="hasilJarak">-</p></div>
                    </div>
                    <div class="row mt-2">
                        <div class="col-md-6"><small class="text-muted">Threshold Efektif</small><p class="fw-bold" id="hasilThreshold">-</p></div>
                        <div class="col-md-6"><small class="text-muted">Pesan</small><p class="fw-bold" id="hasilPesan">-</p></div>
                    </div>
                </div>
                <button onclick="resetForm()" class="btn btn-primary mt-3">
                    <i class="mdi mdi-camera"></i> Scan Toko Lain
                </button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('page-scripts')
<script src="https://unpkg.com/html5-qrcode@2.3.8/html5-qrcode.min.js"></script>
<script>
    const audioBeep = document.getElementById('audioBeep');
    const formKunjungan = document.getElementById('formKunjungan');
    const hasilKunjungan = document.getElementById('hasilKunjungan');
    const lokasiLoading = document.getElementById('lokasiLoading');
    const lokasiStatus = document.getElementById('lokasiStatus');

    let html5QrCode = null;
    let tokoData = null;
    let scanStarting = false;

    function startScanner() {
        if (scanStarting) return;
        scanStarting = true;

        document.getElementById('reader').innerHTML = '';
        html5QrCode = new Html5Qrcode("reader");
        html5QrCode.start(
            { facingMode: "environment" },
            { fps: 10, qrbox: { width: 250, height: 80 } },
            onScanSuccess
        ).then(function () { scanStarting = false; })
        .catch(function (err) {
            scanStarting = false;
            console.error('Gagal memulai kamera:', err);
            alert('Gagal mengakses kamera. Pastikan izin kamera diberikan.');
        });
    }

    function onScanSuccess(decodedText) {
        html5QrCode.stop().then(() => {
            audioBeep.currentTime = 0;
            audioBeep.play();

            axios.get('/geolocation/kunjungan/toko/' + encodeURIComponent(decodedText))
                .then(function (response) {
                    if (response.data.status === 'success') {
                        tokoData = response.data.data;
                        $('#reader').closest('.card').hide();
                        formKunjungan.style.display = 'block';
                        document.getElementById('tokoNama').textContent = tokoData.nama_toko;
                        document.getElementById('tokoAlamat').textContent = tokoData.alamat || '-';
                        document.getElementById('tokoKoordinat').textContent =
                            tokoData.latitude + ', ' + tokoData.longitude;
                    }
                })
                .catch(function (error) {
                    var msg = 'QR Code tidak dikenal.';
                    if (error.response && error.response.status === 404) msg = 'Toko dengan kode tersebut tidak ditemukan.';
                    alert(msg);
                    restartScanner();
                });
        }).catch(function (err) { console.error('Gagal menghentikan kamera:', err); });
    }

    function getAccuratePosition(targetAccuracy, maxWait) {
        targetAccuracy = targetAccuracy || 50; maxWait = maxWait || 20000;
        return new Promise(function (resolve, reject) {
            var best = null, start = Date.now();
            var id = navigator.geolocation.watchPosition(
                function (p) { if (!best || p.coords.accuracy < best.coords.accuracy) best = p; if (p.coords.accuracy <= targetAccuracy) { navigator.geolocation.clearWatch(id); resolve(best); } if (Date.now() - start >= maxWait) { navigator.geolocation.clearWatch(id); if (best) resolve(best); else reject(new Error('Timeout')); } },
                function (e) { reject(e); },
                { enableHighAccuracy: true, maximumAge: 0, timeout: maxWait }
            );
        });
    }

    document.getElementById('btnAmbilLokasi').addEventListener('click', async function () {
        var btn = this;
        btn.disabled = true;
        btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span> Mengambil lokasi...';
        lokasiLoading.style.display = 'block';
        lokasiStatus.style.display = 'none';

        try {
            var pos = await getAccuratePosition(50, 20000);
            var lat = pos.coords.latitude, lng = pos.coords.longitude, acc = pos.coords.accuracy;
            lokasiLoading.style.display = 'none';
            lokasiStatus.style.display = 'block';
            lokasiStatus.className = 'alert alert-success';
            lokasiStatus.innerHTML = '<strong>Lokasi didapat:</strong> ' + lat.toFixed(6) + ', ' + lng.toFixed(6) + ' (akurasi: ' + acc.toFixed(0) + 'm)';

            axios.post('/geolocation/kunjungan', {
                barcode_token: tokoData.barcode_token, latitude_sales: lat, longitude_sales: lng, accuracy_sales: acc
            }).then(function (res) {
                formKunjungan.style.display = 'none';
                hasilKunjungan.style.display = 'block';
                var alertEl = document.getElementById('alertHasil'), iconEl = document.getElementById('statusIcon'), textEl = document.getElementById('statusText'), pesanEl = document.getElementById('hasilPesan');
                if (res.data.status === 'diterima') {
                    alertEl.className = 'alert alert-success'; iconEl.innerHTML = '&#10003;'; iconEl.className = 'display-1 text-success'; textEl.textContent = 'KUNJUNGAN DITERIMA'; textEl.className = 'text-success';
                } else {
                    alertEl.className = 'alert alert-danger'; iconEl.innerHTML = '&#10007;'; iconEl.className = 'display-1 text-danger'; textEl.textContent = 'KUNJUNGAN DITOLAK'; textEl.className = 'text-danger';
                }
                document.getElementById('hasilLat').textContent = res.data.data.latitude_sales;
                document.getElementById('hasilLng').textContent = res.data.data.longitude_sales;
                document.getElementById('hasilAcc').textContent = res.data.data.acc_sales + ' m';
                document.getElementById('hasilJarak').textContent = res.data.data.jarak + ' m';
                document.getElementById('hasilThreshold').textContent = res.data.data.threshold + ' m';
                pesanEl.textContent = res.data.message;
            }).catch(function (error) {
                var msg = 'Gagal menyimpan kunjungan.';
                if (error.response && error.response.data && error.response.data.message) msg = error.response.data.message;
                lokasiStatus.className = 'alert alert-danger';
                lokasiStatus.textContent = msg;
            });
        } catch (error) {
            lokasiLoading.style.display = 'none';
            lokasiStatus.style.display = 'block';
            lokasiStatus.className = 'alert alert-danger';
            if (error.code === 1) lokasiStatus.textContent = 'Izin lokasi ditolak. Aktifkan GPS dan izinkan akses lokasi.';
            else if (error.code === 2) lokasiStatus.textContent = 'Posisi tidak tersedia. Coba di luar ruangan.';
            else if (error.message === 'Timeout') lokasiStatus.textContent = 'Waktu habis. Akurasi GPS belum memadai. Coba di tempat terbuka.';
            else lokasiStatus.textContent = 'Gagal mendapatkan lokasi: ' + error.message;
        } finally {
            btn.disabled = false;
            btn.innerHTML = '<i class="mdi mdi-crosshairs-gps"></i> Ambil Lokasi & Kirim';
        }
    });

    function restartScanner() {
        formKunjungan.style.display = 'none';
        hasilKunjungan.style.display = 'none';
        lokasiStatus.style.display = 'none';
        lokasiLoading.style.display = 'none';
        $('#reader').closest('.card').show();
        if (html5QrCode) {
            html5QrCode.stop().then(function () { startScanner(); }).catch(function () { startScanner(); });
        } else { startScanner(); }
    }

    function resetForm() { restartScanner(); }

    $(document).ready(function () { startScanner(); });
</script>
@endpush
