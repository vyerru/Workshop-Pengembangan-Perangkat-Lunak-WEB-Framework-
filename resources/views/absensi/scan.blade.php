@extends('layouts.app')

@section('content')
<div class="page-header">
    <h3 class="page-title">
        <span class="page-title-icon bg-gradient-primary text-white me-2">
            <i class="mdi mdi-wifi"></i>
        </span> Absensi NFC
    </h3>
</div>

<div class="row">
    <div class="col-md-8 grid-margin stretch-card mx-auto">
        <div class="card">
            <div class="card-body text-center">
                <div class="form-group mb-4">
                    <label for="pertemuan-select" class="font-weight-bold">Pilih Pertemuan</label>
                    <select id="pertemuan-select" class="form-control">
                        <option value="">-- Pilih Pertemuan --</option>
                        @foreach($pertemuans as $p)
                        <option value="{{ $p->id }}">Pertemuan {{ $p->pertemuan_ke }} — {{ $p->topik }} ({{ $p->tanggal->format('d M Y') }})</option>
                        @endforeach
                    </select>
                </div>

                <div id="status-alert" class="alert alert-secondary">
                    <i class="mdi mdi-information-outline"></i>
                    <span id="status-text">Pilih pertemuan, lalu scan KTM</span>
                </div>

                <button id="btn-scan" class="btn btn-gradient-primary btn-lg btn-rounded w-100" disabled>
                    <i class="mdi mdi-wifi"></i>
                    <span id="btn-text">Mulai Scan KTM (NFC)</span>
                </button>

                <div class="mt-4 text-muted">
                    <small>Tempelkan KTM di belakang ponsel untuk merekam kehadiran</small>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-8 grid-margin stretch-card mx-auto">
        <div class="card">
            <div class="card-body">
                <h4 class="card-title">Riwayat Absensi Saya</h4>
                <div class="table-responsive">
                    <table class="table table-sm table-striped">
                        <thead>
                            <tr>
                                <th>Pertemuan</th>
                                <th>Topik</th>
                                <th>Tanggal</th>
                                <th>Waktu Hadir</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($riwayat as $absensi)
                            <tr>
                                <td>{{ $absensi->pertemuan->pertemuan_ke }}</td>
                                <td>{{ $absensi->pertemuan->topik }}</td>
                                <td>{{ $absensi->pertemuan->tanggal->format('d M Y') }}</td>
                                <td>{{ $absensi->waktu_hadir->format('H:i:s') }}</td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="4" class="text-center text-muted">Belum ada riwayat absensi.</td>
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
<script>
let abortController = null;

document.getElementById('pertemuan-select').addEventListener('change', function() {
    document.getElementById('btn-scan').disabled = !this.value;
});

function setState(state, message) {
    var alert = document.getElementById('status-alert');
    var text = document.getElementById('status-text');
    var btn = document.getElementById('btn-scan');
    var btnText = document.getElementById('btn-text');

    alert.className = 'alert alert-secondary';
    text.textContent = message || '';

    btn.disabled = true;

    switch (state) {
        case 'idle':
            alert.className = 'alert alert-secondary';
            text.textContent = 'Pilih pertemuan, lalu scan KTM';
            btn.disabled = !document.getElementById('pertemuan-select').value;
            btnText.textContent = 'Mulai Scan KTM (NFC)';
            break;
        case 'meminta_izin':
            alert.className = 'alert alert-info';
            text.textContent = 'Mengakses NFC...';
            btnText.textContent = 'Meminta izin...';
            break;
        case 'scanning':
            alert.className = 'alert alert-warning';
            text.textContent = 'NFC aktif. Silakan tempelkan KTM Anda di belakang ponsel...';
            btnText.textContent = 'Tempelkan KTM...';
            break;
        case 'reading':
            alert.className = 'alert alert-info';
            text.textContent = 'Membaca data NFC...';
            btnText.textContent = 'Membaca...';
            break;
        case 'submitting':
            alert.className = 'alert alert-info';
            text.textContent = 'Menyimpan kehadiran...';
            btnText.textContent = 'Menyimpan...';
            break;
        case 'success':
            alert.className = 'alert alert-success';
            text.textContent = message || 'Hadir tercatat!';
            btnText.textContent = 'Selesai';
            setTimeout(function() { setState('idle'); }, 3000);
            break;
        case 'error':
            alert.className = 'alert alert-danger';
            text.textContent = message || 'Terjadi kesalahan.';
            btnText.textContent = 'Coba Lagi';
            btn.disabled = false;
            break;
        case 'timeout':
            alert.className = 'alert alert-warning';
            text.textContent = 'Waktu scan habis. Silakan coba lagi.';
            btnText.textContent = 'Coba Lagi';
            btn.disabled = false;
            break;
    }
}

document.getElementById('btn-scan').addEventListener('click', async function() {
    var pertemuanId = document.getElementById('pertemuan-select').value;
    if (!pertemuanId) {
        setState('error', 'Pilih pertemuan terlebih dahulu.');
        return;
    }

    if (!("NDEFReader" in window)) {
        setState('error', 'Browser ini tidak mendukung Web NFC. Gunakan Chrome for Android.');
        return;
    }

    setState('meminta_izin');

    abortController = new AbortController();
    var ndef = new NDEFReader();

    var timeout = setTimeout(function() {
        abortController.abort();
        setState('timeout');
    }, 30000);

    try {
        await ndef.scan({ signal: abortController.signal });
        setState('scanning');

        ndef.addEventListener("reading", function(e) {
            clearTimeout(timeout);
            abortController.abort();
            setState('reading');
            kirimAbsensi(e.serialNumber, pertemuanId);
        }, { once: true });

    } catch (error) {
        clearTimeout(timeout);
        if (error.name === 'AbortError') return;
        setState('error', error.message || 'Gagal mengakses NFC.');
    }
});

function kirimAbsensi(uid, pertemuanId) {
    setState('submitting');

    axios.post('{{ route("absen.scan") }}', {
        nfc_uid: uid,
        pertemuan_id: pertemuanId,
    })
    .then(function(response) {
        setState('success', response.data.message);
        setTimeout(function() { location.reload(); }, 2000);
    })
    .catch(function(error) {
        var msg = error.response?.data?.message || 'Terjadi kesalahan pada server.';
        setState('error', msg);
    });
}

window.addEventListener('beforeunload', function() {
    if (abortController) abortController.abort();
});
</script>
@endpush
