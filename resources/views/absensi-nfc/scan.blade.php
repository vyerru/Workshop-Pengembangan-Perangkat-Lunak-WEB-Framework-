@extends('layouts.app')

@section('content')
<div class="page-header">
    <h3 class="page-title">
        <span class="page-title-icon bg-gradient-primary text-white me-2">
            <i class="mdi mdi-wifi"></i>
        </span> Absensi Praktikum (NFC)
    </h3>
</div>

<div class="row">
    <div class="col-md-8 grid-margin stretch-card mx-auto">
        <div class="card">
            <div class="card-body text-center">
                <input type="hidden" id="topik_mingguan" value="Pengenalan Web NFC">
                <input type="hidden" id="pertemuan_ke" value="Pertemuan 5">

                <div id="status-alert" class="alert alert-secondary">
                    <i class="mdi mdi-information-outline"></i> Menunggu inisialisasi...
                </div>

                <button id="btn-scan" class="btn btn-gradient-primary btn-lg btn-rounded w-100">
                    <i class="mdi mdi-wifi"></i> Mulai Scan KTM (NFC)
                </button>

                <div class="mt-4 text-muted">
                    <small>Tempelkan KTM di belakang ponsel untuk merekam kehadiran</small>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('page-scripts')
<script>
document.getElementById('btn-scan').addEventListener('click', async () => {
    const alertBox = document.getElementById('status-alert');
    const topik = document.getElementById('topik_mingguan').value;
    const pertemuan = document.getElementById('pertemuan_ke').value;

    if (!("NDEFReader" in window)) {
        alertBox.className = "alert alert-danger";
        alertBox.innerHTML = '<i class="mdi mdi-alert-circle"></i> Browser ini tidak mendukung Web NFC. Gunakan Chrome for Android.';
        return;
    }

    try {
        const ndef = new NDEFReader();
        await ndef.scan();

        alertBox.className = "alert alert-warning";
        alertBox.innerHTML = '<i class="mdi mdi-nfc"></i> NFC aktif. Silakan tempelkan KTM Anda di belakang ponsel...';

        ndef.addEventListener("reading", ({ serialNumber }) => {
            alertBox.className = "alert alert-info";
            alertBox.innerHTML = '<i class="mdi mdi-loading mdi-spin"></i> Membaca data NFC...';
            kirimAbsensi(serialNumber, topik, pertemuan);
        });

    } catch (error) {
        alertBox.className = "alert alert-danger";
        alertBox.innerHTML = '<i class="mdi mdi-alert-circle"></i> Akses NFC Gagal: ' + error.message;
    }
});

function kirimAbsensi(uid, topik, pertemuan) {
    const alertBox = document.getElementById('status-alert');

    axios.post('{{ route("absensi.nfc.store") }}', {
        nfc_uid: uid,
        topik_mingguan: topik,
        pertemuan_ke: pertemuan,
        _token: '{{ csrf_token() }}'
    })
    .then(response => {
        alertBox.className = "alert alert-success";
        alertBox.innerHTML = '<i class="mdi mdi-check-circle"></i> ' + response.data.message;
    })
    .catch(error => {
        alertBox.className = "alert alert-danger";
        alertBox.innerHTML = '<i class="mdi mdi-alert-circle"></i> ' + (error.response?.data?.message || 'Terjadi kesalahan pada server.');
    });
}
</script>
@endpush
