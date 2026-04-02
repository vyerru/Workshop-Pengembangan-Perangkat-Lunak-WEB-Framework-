@extends('layouts.app')

@push('styles')
<style>
    /* Style untuk spinner - tidak mengubah style yang ada */
    .spinner-border-sm {
        width: 1rem;
        height: 1rem;
        border-width: 0.2em;
    }
</style>
@endpush

@section('content')
<div class="page-header">
    <h3 class="page-title"> Kelola Kategori </h3>
</div>
<div class="row">
    <div class="col-md-4 grid-margin stretch-card">
        <div class="card">
            <div class="card-body">
                <h4 class="card-title">Tambah Kategori</h4>
                <!-- UBAH: Tambahkan id pada form -->
                <form id="formTambahKategori" action="{{ route('kategori.store') }}" method="POST">
                    @csrf
                    <div class="form-group">
                        <label>Nama Kategori</label>
                        <input type="text" name="nama_kategori" id="nama_kategori" class="form-control" required>
                    </div>
                    <!-- UBAH: Submit button menjadi button biasa dengan spinner -->
                    <button type="button" id="btnTambahKategori" class="btn btn-gradient-primary me-2" onclick="submitTambahKategori()">
                        <span class="btn-text">Simpan</span>
                        <span class="spinner-border spinner-border-sm" style="display:none;"></span>
                    </button>
                </form>
            </div>
        </div>
    </div>
    <div class="col-md-8 grid-margin stretch-card">
        <div class="card">
            <div class="card-body">
                <h4 class="card-title">Daftar Kategori</h4>
                <table class="table table-hover">
                    <thead>
                        <tr><th>ID</th><th>Kategori</th></tr>
                    </thead>
                    <tbody>
                        @foreach($kategori as $item)
                        <tr>
                            <td>{{ $item->idkategori }}</td>
                            <td>{{ $item->nama_kategori }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection

@push('page-scripts')
<script>
    // ============================================
    // FUNGSI UNTUK TAMBAH KATEGORI
    // ============================================
    function submitTambahKategori() {
        var form = document.getElementById('formTambahKategori');
        var btn = document.getElementById('btnTambahKategori');
        
        // 1. Validasi HTML5 - cek apakah semua input required terisi
        if (!form.checkValidity()) {
            // Tampilkan pesan error bawaan browser
            form.reportValidity();
            return false;
        }
        
        // 2. Semua validasi OK, ubah button jadi loading state
        btn.querySelector('.btn-text').style.display = 'none';
        btn.querySelector('.spinner-border').style.display = 'inline-block';
        btn.disabled = true;
        
        // 3. Submit form ke Laravel controller
        form.submit();
    }
</script>
@endpush