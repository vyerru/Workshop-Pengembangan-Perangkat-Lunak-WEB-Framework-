@extends('layouts.app')
@section('content')
<div class="row">
    <div class="col-md-4">
        <div class="card">
            <div class="card-body">
                <h4 class="card-title">Pencarian Barang</h4>
                <div class="form-group">
                    <label>Kode Barang (Tekan Enter)</label>
                    <input type="text" id="kode" class="form-control" placeholder="Masukkan Kode">
                </div>
                <div class="form-group">
                    <label>Nama Barang</label>
                    <input type="text" id="nama" class="form-control" readonly>
                </div>
                <div class="form-group">
                    <label>Harga Barang</label>
                    <input type="number" id="harga" class="form-control" readonly>
                </div>
                <div class="form-group">
                    <label>Jumlah</label>
                    <input type="number" id="jumlah" class="form-control" value="1" min="1">
                </div>
                <button type="button" id="btn-tambah" class="btn btn-primary btn-block" disabled>Tambahkan</button>
            </div>
        </div>
    </div>
    
    <div class="col-md-8">
        <div class="card">
            <div class="card-body">
                <h4 class="card-title">Detail Penjualan</h4>
                <table class="table table-bordered" id="table-cart">
                    <thead>
                        <tr>
                            <th>Kode</th><th>Nama</th><th>Harga</th><th>Jumlah</th><th>Subtotal</th><th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
                <div class="mt-3 text-right">
                    <h3>Total: Rp <span id="label-total">0</span></h3>
                    <button type="button" id="btn-bayar" class="btn btn-success btn-lg mt-2" disabled>Bayar</button>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
$(document).ready(function() {
    let cart = [];

    // Fitur Enter untuk mencari data
    $('#kode').on('keypress', function(e) {
        if (e.which === 13) {
            let id = $(this).val();
            // Loading spinner logic
            let originalText = $('#kode').val();
            $('#kode').prop('disabled', true);
            
            $.ajax({
                url: `/pos/barang/${id}`,
                type: 'GET',
                success: function(response) {
                    let b = response.data;
                    $('#nama').val(b.nama);
                    $('#harga').val(b.harga);
                    $('#jumlah').val(1);
                    $('#btn-tambah').prop('disabled', false); // Aktifkan tombol tambah
                },
                error: function(xhr) {
                    Swal.fire('Error', 'Barang tidak ditemukan', 'error');
                    resetForm();
                },
                complete: function() {
                    $('#kode').prop('disabled', false).focus();
                }
            });
        }
    });

    $('#btn-tambah').click(function() {
        let item = {
            id_barang: $('#kode').val(),
            nama: $('#nama').val(),
            harga: parseInt($('#harga').val()),
            jumlah: parseInt($('#jumlah').val()),
        };
        item.subtotal = item.harga * item.jumlah;

        if (item.jumlah > 0) {
            let existingItem = cart.find(x => x.id_barang === item.id_barang);
            if (existingItem) {
                existingItem.jumlah += item.jumlah; // Jika ada, update jumlah & subtotal
                existingItem.subtotal = existingItem.jumlah * existingItem.harga;
            } else {
                cart.push(item);
            }
            renderCart();
            resetForm();
        }
    });

    function renderCart() {
        let html = '';
        let total = 0;
        cart.forEach((item, index) => {
            total += item.subtotal;
            html += `<tr>
                <td>${item.id_barang}</td>
                <td>${item.nama}</td>
                <td>${item.harga}</td>
                <td><input type="number" class="form-control qty-edit" data-index="${index}" value="${item.jumlah}" min="1"></td>
                <td>${item.subtotal}</td>
                <td><button class="btn btn-danger btn-sm btn-hapus" data-index="${index}">Hapus</button></td>
            </tr>`;
        });
        $('#table-cart tbody').html(html);
        $('#label-total').text(total);
        $('#btn-bayar').prop('disabled', cart.length === 0);
    }

    function resetForm() {
        $('#kode').val(''); $('#nama').val(''); $('#harga').val('');
        $('#jumlah').val(1); $('#btn-tambah').prop('disabled', true);
    }

    // Update jika jumlah di table diganti
    $(document).on('change', '.qty-edit', function() {
        let idx = $(this).data('index');
        let val = parseInt($(this).val());
        if (val > 0) {
            cart[idx].jumlah = val;
            cart[idx].subtotal = val * cart[idx].harga;
            renderCart();
        }
    });

    // Hapus baris dari table
    $(document).on('click', '.btn-hapus', function() {
        let idx = $(this).data('index');
        cart.splice(idx, 1);
        renderCart();
    });

    // Submit Pembayaran dengan AJAX & Spinner
    $('#btn-bayar').click(function() {
        let btn = $(this);
        let originalText = btn.text();
        // Cegah double submit dengan loading spinner
        btn.html('<span class="spinner-border spinner-border-sm"></span> Processing...').prop('disabled', true);

        let payload = {
            _token: '{{ csrf_token() }}',
            total: parseInt($('#label-total').text()),
            items: cart
        };

        $.ajax({
            url: '/pos/store',
            type: 'POST',
            data: payload,
            success: function(response) {
                Swal.fire('Success!', response.message, 'success');
                cart = []; // Kosongi keranjang
                renderCart();
            },
            error: function(xhr) {
                Swal.fire('Error!', 'Gagal menyimpan transaksi', 'error');
            },
            complete: function() {
                btn.html(originalText).prop('disabled', cart.length === 0);
            }
        });
    });
});
</script>
@endsection