@extends('layouts.app')
@section('content')
<div class="card">
    <div class="card-body">
        <h4 class="card-title">Wilayah Administrasi</h4>

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
<script>
function resetSelect(id, placeholder) {
    $('#' + id)
        .html('<option value="">' + placeholder + '</option>')
        .prop('disabled', true);
}

function loadData(url, targetId, placeholder) {
    $.ajax({
        url: url,
        type: 'GET',
        dataType: 'json',
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        success: function(data) {
            data.forEach(function(item) {
                $('#' + targetId).append(
                    '<option value="' + item.code + '">' + item.name + '</option>'
                );
            });
            $('#' + targetId).prop('disabled', false);
        },
        error: function(xhr) {
            console.error('Error:', xhr.status, xhr.responseText);
            alert('Gagal memuat data ' + placeholder);
        }
    });
}

$(document).ready(function() {

    // Load provinsi saat halaman dibuka
    loadData('/api/provinces', 'provinsi', 'Provinsi');

    $('#provinsi').change(function() {
        var code = $(this).val();
        resetSelect('kota', 'Pilih Kota');
        resetSelect('kecamatan', 'Pilih Kecamatan');
        resetSelect('kelurahan', 'Pilih Kelurahan');
        if (code) loadData('/api/cities/' + code, 'kota', 'Kota');
    });

    $('#kota').change(function() {
        var code = $(this).val();
        resetSelect('kecamatan', 'Pilih Kecamatan');
        resetSelect('kelurahan', 'Pilih Kelurahan');
        if (code) loadData('/api/districts/' + code, 'kecamatan', 'Kecamatan');
    });

    $('#kecamatan').change(function() {
        var code = $(this).val();
        resetSelect('kelurahan', 'Pilih Kelurahan');
        if (code) loadData('/api/villages/' + code, 'kelurahan', 'Kelurahan');
    });

});
</script>
@endpush