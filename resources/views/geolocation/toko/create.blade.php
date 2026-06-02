@extends('layouts.app')

@push('styles')
<style>
    #map {
        height: 350px;
        width: 100%;
        border-radius: 8px;
        border: 1px solid #dee2e6;
    }
</style>
@endpush

@section('content')
<div class="page-header">
    <h3 class="page-title">Tambah Toko</h3>
</div>

<div class="row">
    <div class="col-md-12 grid-margin stretch-card">
        <div class="card">
            <div class="card-body">
                <form method="POST" action="{{ route('geolocation.toko.store') }}">
                    @csrf

                    <div class="mb-3">
                        <label class="form-label">Nama Toko <span class="text-danger">*</span></label>
                        <input name="nama_toko" class="form-control @error('nama_toko') is-invalid @enderror"
                               value="{{ old('nama_toko') }}" required maxlength="100">
                        @error('nama_toko')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Alamat</label>
                        <textarea name="alamat" class="form-control @error('alamat') is-invalid @enderror"
                                  rows="3" maxlength="500">{{ old('alamat') }}</textarea>
                        @error('alamat')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Pilih Lokasi di Peta</label>
                        <div id="map"></div>
                        <small class="text-muted">Klik pada peta atau seret marker untuk menentukan lokasi toko.</small>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-4">
                            <label class="form-label">Latitude</label>
                            <input name="latitude" id="latitude"
                                   class="form-control @error('latitude') is-invalid @enderror"
                                   step="any" readonly value="{{ old('latitude') }}">
                            @error('latitude')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Longitude</label>
                            <input name="longitude" id="longitude"
                                   class="form-control @error('longitude') is-invalid @enderror"
                                   step="any" readonly value="{{ old('longitude') }}">
                            @error('longitude')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Accuracy (meter)</label>
                            <input name="accuracy" class="form-control @error('accuracy') is-invalid @enderror"
                                   type="number" min="0" step="1" value="{{ old('accuracy') }}"
                                   placeholder="Kosongkan jika input manual">
                            @error('accuracy')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <button type="button" id="btnManual" class="btn btn-warning btn-sm mb-3">
                        <i class="mdi mdi-pencil"></i> Input Manual Koordinat
                    </button>

                    <div class="d-flex justify-content-between">
                        <a href="{{ route('geolocation.toko.index') }}" class="btn btn-secondary">Batal</a>
                        <button type="submit" class="btn btn-primary">Simpan Toko</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('page-scripts')
<script
    src="https://maps.googleapis.com/maps/api/js?key={{ config('services.google_maps.key') ?? '' }}&callback=initMap"
    async defer>
</script>
<script>
    let map, marker;

    function initMap() {
        const defaultLoc = { lat: -6.1754, lng: 106.8272 };

        map = new google.maps.Map(document.getElementById('map'), {
            center: defaultLoc,
            zoom: 13,
        });

        marker = new google.maps.Marker({
            position: defaultLoc,
            map: map,
            draggable: true,
        });

        google.maps.event.addListener(map, 'click', function (event) {
            placeMarker(event.latLng);
        });

        google.maps.event.addListener(marker, 'dragend', function (event) {
            updateCoords(event.latLng);
        });

        updateCoords(defaultLoc);
    }

    function placeMarker(location) {
        marker.setPosition(location);
        updateCoords(location);
    }

    function updateCoords(location) {
        document.getElementById('latitude').value = location.lat().toFixed(7);
        document.getElementById('longitude').value = location.lng().toFixed(7);
    }

    document.getElementById('btnManual').addEventListener('click', function () {
        const latInput = document.getElementById('latitude');
        const lngInput = document.getElementById('longitude');

        if (latInput.hasAttribute('readonly')) {
            latInput.removeAttribute('readonly');
            lngInput.removeAttribute('readonly');
            latInput.placeholder = 'Isi latitude manual';
            lngInput.placeholder = 'Isi longitude manual';
            this.textContent = 'Kembali ke Peta';
        } else {
            latInput.setAttribute('readonly', 'readonly');
            lngInput.setAttribute('readonly', 'readonly');
            this.innerHTML = '<i class="mdi mdi-pencil"></i> Input Manual Koordinat';
        }
    });
</script>
@endpush
