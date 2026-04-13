@extends('layouts.app')

@section('content')
<div class="card">
    <div class="card-body">
        <h4 class="card-title">Wilayah Administrasi <span class="badge badge-info" style="font-size: 0.7rem;">Axios</span></h4>

        <div class="form-group">
            <label>Provinsi</label>
            <select id="provinsi" class="form-control">
                <option value="">Pilih Provinsi</option>
            </select>
        </div>
        <div class="form-group">
            <label>Kota/Kabupaten</label>
            <select id="kota" class="form-control" disabled>
                <option value="">Pilih Kota</option>
            </select>
        </div>
        <div class="form-group">
            <label>Kecamatan</label>
            <select id="kecamatan" class="form-control" disabled>
                <option value="">Pilih Kecamatan</option>
            </select>
        </div>
        <div class="form-group">
            <label>Kelurahan</label>
            <select id="kelurahan" class="form-control" disabled>
                <option value="">Pilih Kelurahan</option>
            </select>
        </div>
    </div>
</div>
@endsection

@push('page-scripts')
{{-- Load Axios via CDN --}}
<script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>

<script>
    // Set CSRF token default untuk semua request Axios
    axios.defaults.headers.common['X-CSRF-TOKEN'] = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

    function resetSelect(id, placeholder) {
        const el = document.getElementById(id);
        el.innerHTML = '<option value="">' + placeholder + '</option>';
        el.disabled = true;
    }

    function loadData(url, targetId) {
        axios.get(url)
            .then(function(response) {
                const el = document.getElementById(targetId);
                response.data.forEach(function(item) {
                    const option = document.createElement('option');
                    option.value = item.code;
                    option.textContent = item.name;
                    el.appendChild(option);
                });
                el.disabled = false;
            })
            .catch(function(error) {
                console.error('Error:', error.response ? error.response.status : error.message);
                alert('Gagal memuat data. Silakan coba lagi.');
            });
    }

    // Load provinsi saat halaman dibuka
    loadData('/api/provinces', 'provinsi');

    document.getElementById('provinsi').addEventListener('change', function() {
        const code = this.value;
        resetSelect('kota', 'Pilih Kota');
        resetSelect('kecamatan', 'Pilih Kecamatan');
        resetSelect('kelurahan', 'Pilih Kelurahan');
        if (code) loadData('/api/cities/' + code, 'kota');
    });

    document.getElementById('kota').addEventListener('change', function() {
        const code = this.value;
        resetSelect('kecamatan', 'Pilih Kecamatan');
        resetSelect('kelurahan', 'Pilih Kelurahan');
        if (code) loadData('/api/districts/' + code, 'kecamatan');
    });

    document.getElementById('kecamatan').addEventListener('change', function() {
        const code = this.value;
        resetSelect('kelurahan', 'Pilih Kelurahan');
        if (code) loadData('/api/villages/' + code, 'kelurahan');
    });
</script>
@endpush