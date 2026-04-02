@extends('layouts.app')

@push('styles')
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap4.min.css">
<style>
    /* Style untuk spinner */
    .btn-loading {
        position: relative;
        pointer-events: none;
    }
    
    .spinner-border-sm {
        width: 1rem;
        height: 1rem;
        border-width: 0.2em;
    }
</style>
@endpush

@section('content')
<div class="page-header">
    <h3 class="page-title"> Kelola Barang </h3>
    <button type="button" class="btn btn-gradient-primary btn-sm" data-toggle="modal" data-target="#createBarangModal" data-bs-toggle="modal" data-bs-target="#createBarangModal">
        Create Barang
    </button>
</div>

@if (session('success'))
<div class="alert alert-success" role="alert">{{ session('success') }}</div>
@endif

@if (session('error'))
<div class="alert alert-danger" role="alert">{{ session('error') }}</div>
@endif

@if ($errors->any())
<div class="alert alert-danger" role="alert">
    <ul class="mb-0">
        @foreach ($errors->all() as $error)
        <li>{{ $error }}</li>
        @endforeach
    </ul>
</div>
@endif

<div class="row">
    <div class="col-md-12 grid-margin stretch-card">
        <div class="card">
            <div class="card-body">
    <h4 class="card-title">Daftar Barang</h4>

    <form id="formCetakLabel" action="{{ route('barang.cetak_label') }}" method="POST" target="_blank">
        @csrf
        
        <div class="row mb-3 align-items-end p-3" style="background-color: #f8f9fa; border-radius: 5px;">
            <div class="col-md-3">
                <label class="font-weight-bold">Mulai Kolom (X):</label>
                <input type="number" name="x" id="kolom_x" class="form-control" min="1" max="5" value="1" required>
                <small class="text-muted">Maksimal 5 Kolom</small>
            </div>
            <div class="col-md-3">
                <label class="font-weight-bold">Mulai Baris (Y):</label>
                <input type="number" name="y" id="baris_y" class="form-control" min="1" max="8" value="1" required>
                <small class="text-muted">Maksimal 8 Baris</small>
            </div>
            <div class="col-md-3">
                <!-- UBAH: Submit button menjadi button biasa dengan onclick -->
                <button type="button" class="btn btn-success btn-icon-text" id="btnCetakLabel" onclick="submitCetakLabel()">
                    <i class="mdi mdi-printer btn-icon-prepend"></i> 
                    <span class="btn-text">Cetak Tag Harga</span>
                    <span class="spinner-border spinner-border-sm" style="display:none;"></span>
                </button>
            </div>
        </div>

        <div class="table-responsive">
            <table id="tabelBarang" class="table table-hover">
                <thead>
                    <tr>
                        <th style="width: 30px;">Cetak</th>
                        @foreach($columns as $column)
                        <th>{{ ucwords(str_replace('_', ' ', $column)) }}</th>
                        @endforeach
                        <th style="width: 180px;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($barang as $item)
                    @php $rowKey = $item->{$primaryKey}; @endphp
                    <tr>
                        <td>
                            <input type="checkbox" name="barang_ids[]" value="{{ $rowKey }}">
                        </td>
                        
                        @foreach($columns as $column)
                        <td>
                            @if ($column === 'harga' && $item->{$column} !== null && $item->{$column} !== '')
                            Rp {{ number_format((float) $item->{$column}, 0, ',', '.') }}
                            @else
                            {{ $item->{$column} }}
                            @endif
                        </td>
                        @endforeach
                        
                        <td>
                            <button type="button" class="btn btn-sm btn-gradient-warning" data-toggle="modal" data-target="#editBarangModal-{{ $rowKey }}" data-bs-toggle="modal" data-bs-target="#editBarangModal-{{ $rowKey }}">
                                Update
                            </button>
                            
                            <button type="button" class="btn btn-sm btn-gradient-danger" onclick="if(confirm('Hapus data ini?')) document.getElementById('delete-form-{{ $rowKey }}').submit();">
                                Delete
                            </button>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="{{ count($columns) + 2 }}" class="text-center">Belum ada data barang.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </form>
    
    @foreach($barang as $item)
        @php $rowKey = $item->{$primaryKey}; @endphp
        <form id="delete-form-{{ $rowKey }}" action="{{ route('barang.destroy', $rowKey) }}" method="POST" style="display: none;">
            @csrf
            @method('DELETE')
        </form>
    @endforeach

</div>
        </div>
    </div>
</div>

<!-- MODAL CREATE - DIUBAH -->
<div class="modal fade" id="createBarangModal" tabindex="-1" role="dialog" aria-labelledby="createBarangModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form id="formCreateBarang" action="{{ route('barang.store') }}" method="POST">
                @csrf
                <input type="hidden" name="_form_type" value="create">

                <div class="modal-header">
                    <h5 class="modal-title" id="createBarangModalLabel">Create Barang</h5>
                    <button type="button" class="close" data-dismiss="modal" data-bs-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>

                <div class="modal-body">
                    @foreach($inputColumns as $column)
                    <div class="form-group">
                        <label>{{ ucwords(str_replace('_', ' ', $column)) }}</label>
                        <input
                            type="{{ str_contains($column, 'harga') ? 'number' : (in_array($column, ['timestamp', 'tanggal', 'waktu'], true) ? 'date' : 'text') }}"
                            step="{{ str_contains($column, 'harga') ? 'any' : '' }}"
                            name="{{ $column }}"
                            id="create_{{ $column }}"
                            class="form-control"
                            value="{{ old('_form_type') === 'create' ? old($column) : '' }}"
                            required>
                    </div>
                    @endforeach
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-dismiss="modal" data-bs-dismiss="modal">Batal</button>
                    <!-- UBAH: Submit button menjadi button biasa -->
                    <button type="button" class="btn btn-gradient-primary" id="btnCreateBarang" onclick="submitCreateBarang()">
                        <span class="btn-text">Create</span>
                        <span class="spinner-border spinner-border-sm" style="display:none;"></span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- MODAL EDIT - DIUBAH -->
@foreach($barang as $item)
@php
$rowKey = $item->{$primaryKey};
@endphp
<div class="modal fade" id="editBarangModal-{{ $rowKey }}" tabindex="-1" role="dialog" aria-labelledby="editBarangModalLabel-{{ $rowKey }}" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form id="formEditBarang-{{ $rowKey }}" action="{{ route('barang.update', $rowKey) }}" method="POST">
                @csrf
                @method('PUT')
                <input type="hidden" name="_form_type" value="edit">
                <input type="hidden" name="_target_id" value="{{ $rowKey }}">

                <div class="modal-header">
                    <h5 class="modal-title" id="editBarangModalLabel-{{ $rowKey }}">Update Barang</h5>
                    <button type="button" class="close" data-dismiss="modal" data-bs-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>

                <div class="modal-body">
                    @foreach($inputColumns as $column)
                    <div class="form-group">
                        <label>{{ ucwords(str_replace('_', ' ', $column)) }}</label>
                        <input
                            type="{{ str_contains($column, 'harga') ? 'number' : (in_array($column, ['timestamp', 'tanggal', 'waktu'], true) ? 'date' : 'text') }}"
                            step="{{ str_contains($column, 'harga') ? 'any' : '' }}"
                            name="{{ $column }}"
                            id="edit_{{ $column }}_{{ $rowKey }}"
                            class="form-control"
                            value="{{ old('_form_type') === 'edit' && old('_target_id') == $rowKey ? old($column) : $item->{$column} }}"
                            required>
                    </div>
                    @endforeach
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-dismiss="modal" data-bs-dismiss="modal">Batal</button>
                    <!-- UBAH: Submit button menjadi button biasa -->
                    <button type="button" class="btn btn-gradient-warning" id="btnEditBarang-{{ $rowKey }}" onclick="submitEditBarang({{ $rowKey }})">
                        <span class="btn-text">Update</span>
                        <span class="spinner-border spinner-border-sm" style="display:none;"></span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endforeach
@endsection

@push('page-scripts')
<script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap4.min.js"></script>

<script>
    $(document).ready(function() {
        $('#tabelBarang').DataTable();
    });

    // ============================================
    // FUNGSI UNTUK CETAK LABEL
    // ============================================
    function submitCetakLabel() {
        var form = document.getElementById('formCetakLabel');
        var btn = document.getElementById('btnCetakLabel');
        
        // 1. Validasi HTML5 - cek apakah ada input required yang kosong
        if (!form.checkValidity()) {
            // Tampilkan pesan error built-in browser
            form.reportValidity();
            return false;
        }
        
        // 2. Validasi custom - cek apakah ada checkbox yang dipilih
        var checkboxes = document.querySelectorAll('input[name="barang_ids[]"]:checked');
        if (checkboxes.length === 0) {
            alert('Silahkan pilih minimal 1 barang untuk dicetak!');
            return false;
        }
        
        // 3. Semua validasi OK, ubah button jadi loading state
        btn.querySelector('.btn-text').style.display = 'none';
        btn.querySelector('.spinner-border').style.display = 'inline-block';
        btn.disabled = true;
        
        // 4. Submit form (akan buka tab baru karena target="_blank")
        form.submit();
        
        // 5. Kembalikan button ke state normal setelah 2 detik
        // (karena form dibuka di tab baru, halaman ini tidak reload)
        setTimeout(function() {
            btn.querySelector('.btn-text').style.display = 'inline';
            btn.querySelector('.spinner-border').style.display = 'none';
            btn.disabled = false;
        }, 2000);
    }

    // ============================================
    // FUNGSI UNTUK CREATE BARANG
    // ============================================
    function submitCreateBarang() {
        var form = document.getElementById('formCreateBarang');
        var btn = document.getElementById('btnCreateBarang');
        
        // 1. Validasi HTML5 - cek semua input required
        if (!form.checkValidity()) {
            form.reportValidity();
            return false;
        }
        
        // 2. Ubah button jadi loading
        btn.querySelector('.btn-text').style.display = 'none';
        btn.querySelector('.spinner-border').style.display = 'inline-block';
        btn.disabled = true;
        
        // 3. Submit form ke Laravel controller
        form.submit();
    }

    // ============================================
    // FUNGSI UNTUK EDIT BARANG
    // ============================================
    function submitEditBarang(id) {
        var form = document.getElementById('formEditBarang-' + id);
        var btn = document.getElementById('btnEditBarang-' + id);
        
        // 1. Validasi HTML5
        if (!form.checkValidity()) {
            form.reportValidity();
            return false;
        }
        
        // 2. Ubah button jadi loading
        btn.querySelector('.btn-text').style.display = 'none';
        btn.querySelector('.spinner-border').style.display = 'inline-block';
        btn.disabled = true;
        
        // 3. Submit form
        form.submit();
    }
</script>

@if ($errors->any())
<script>
    (function() {
        var formType = @json(old('_form_type'));
        var targetId = @json(old('_target_id'));
        var modalId = null;

        if (formType === 'create') {
            modalId = 'createBarangModal';
        }

        if (formType === 'edit' && targetId) {
            modalId = 'editBarangModal-' + targetId;
        }

        if (!modalId) {
            return;
        }

        var modalElement = document.getElementById(modalId);
        if (!modalElement) {
            return;
        }

        if (window.bootstrap && window.bootstrap.Modal) {
            window.bootstrap.Modal.getOrCreateInstance(modalElement).show();
            return;
        }

        if (window.jQuery && typeof window.jQuery.fn.modal === 'function') {
            window.jQuery(modalElement).modal('show');
        }
    })();
</script>
@endif
@endpush