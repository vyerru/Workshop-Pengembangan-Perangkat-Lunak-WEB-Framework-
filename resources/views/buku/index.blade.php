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
    <h3 class="page-title"> Koleksi Buku </h3>
</div>
<div class="row">
    <div class="col-12 grid-margin stretch-card">
        <div class="card">
            <div class="card-body">
                <h4 class="card-title">Tambah Buku Baru</h4>
                <!-- UBAH: Tambahkan id pada form -->
                <form id="formTambahBuku" class="forms-sample" action="{{ route('buku.store') }}" method="POST">
                    @csrf
                    <div class="row">
                        <div class="col-md-3">
                            <label>Kategori</label> 
                            <select name="idkategori" id="idkategori" class="form-control" required>
                                @foreach($kategori as $k)
                                    <option value="{{ $k->idkategori }}">{{ $k->nama_kategori }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label>Kode</label> 
                            <input type="text" name="kode" id="kode" class="form-control" required>
                        </div>
                        <div class="col-md-4">
                            <label>Judul</label> 
                            <input type="text" name="judul" id="judul" class="form-control" required>
                        </div>
                        <div class="col-md-3">
                            <label>Pengarang</label> 
                            <input type="text" name="pengarang" id="pengarang" class="form-control" required>
                        </div>
                    </div>
                    <!-- UBAH: Submit button menjadi button biasa dengan spinner -->
                    <button type="button" id="btnTambahBuku" class="btn btn-gradient-success mt-3" onclick="submitTambahBuku()">
                        <span class="btn-text">Tambah Buku</span>
                        <span class="spinner-border spinner-border-sm" style="display:none;"></span>
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-12 grid-margin">
        <div class="card">
            <div class="card-body">
                <h4 class="card-title">Data Buku</h4>
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr><th>Kode</th><th>Judul</th><th>Pengarang</th><th>Kategori</th></tr>
                        </thead>
                        <tbody>
                            @foreach($buku as $b)
                            <tr>
                                <td>{{ $b->kode }}</td>
                                <td>{{ $b->judul }}</td>
                                <td>{{ $b->pengarang }}</td>
                                <td><label class="badge badge-info">{{ $b->kategori->nama_kategori }}</label></td>
                            </tr>
                            @endforeach
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
    // ============================================
    // FUNGSI UNTUK TAMBAH BUKU
    // ============================================
    function submitTambahBuku() {
        var form = document.getElementById('formTambahBuku');
        var btn = document.getElementById('btnTambahBuku');
        
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