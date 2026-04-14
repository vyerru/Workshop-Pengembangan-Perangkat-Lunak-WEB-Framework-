@extends('layouts.app')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <h4 class="card-title">Kelola Menu Master</h4>

                @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        {{ session('success') }}
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                @endif

                {{-- ===== FORM TAMBAH MENU ===== --}}
                <form action="{{ route('vendor.menus.store') }}" method="POST" enctype="multipart/form-data" class="mb-4">
                    @csrf
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="nama_menu">Nama Menu <span class="text-danger">*</span></label>
                                <input type="text"
                                       name="nama_menu"
                                       id="nama_menu"
                                       class="form-control @error('nama_menu') is-invalid @enderror"
                                       placeholder="Contoh: Nasi Goreng Spesial"
                                       value="{{ old('nama_menu') }}">
                                @error('nama_menu')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="harga">Harga (Rp) <span class="text-danger">*</span></label>
                                <input type="number"
                                       name="harga"
                                       id="harga"
                                       class="form-control @error('harga') is-invalid @enderror"
                                       placeholder="Contoh: 15000"
                                       min="100"
                                       value="{{ old('harga') }}">
                                @error('harga')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="path_gambar">Gambar Menu <span class="text-danger">*</span></label>
                                <input type="file"
                                       name="path_gambar"
                                       id="path_gambar"
                                       class="form-control @error('path_gambar') is-invalid @enderror"
                                       accept="image/jpg,image/jpeg,image/png">
                                @error('path_gambar')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-2 d-flex align-items-end">
                            <div class="form-group w-100">
                                <button type="submit" class="btn btn-primary btn-block">
                                    <i class="mdi mdi-plus"></i> Tambah Menu
                                </button>
                            </div>
                        </div>
                    </div>
                </form>

                {{-- ===== TABEL DATA MENU ===== --}}
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Gambar</th>
                                <th>Nama Menu</th>
                                <th>Harga</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($menus as $index => $menu)
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td>
                                        <img src="{{ $menu->path_gambar ? asset('storage/' . $menu->path_gambar) : asset('assets/images/no-image.png') }}">
                                             alt="{{ $menu->nama_menu }}"
                                             height="50"
                                             style="border-radius: 4px; object-fit: cover; aspect-ratio: 1/1;">
                                    </td>
                                    <td>{{ $menu->nama_menu }}</td>
                                    <td>Rp {{ number_format($menu->harga, 0, ',', '.') }}</td>
                                    <td>
                                        <form action="{{ route('vendor.menus.destroy', $menu->id) }}"
                                              method="POST"
                                              onsubmit="return confirm('Yakin hapus?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger btn-sm">
                                                <i class="mdi mdi-delete"></i> Hapus
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center text-muted py-4">
                                        Belum ada menu. Tambahkan menu pertama Anda di atas.
                                    </td>
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