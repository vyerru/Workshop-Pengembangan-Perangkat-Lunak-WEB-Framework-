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
                            <th>Kode</th>
                            <th>Nama</th>
                            <th>Harga</th>
                            <th>Jumlah</th>
                            <th>Subtotal</th>
                            <th>Aksi</th>
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

@push('page-scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
<script>
    // Setup Axios global: CSRF token otomatis di semua request
    axios.defaults.headers.common['X-CSRF-TOKEN'] = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    axios.defaults.headers.common['Accept'] = 'application/json';

    let cart = [];

    // Cari barang saat Enter ditekan
    document.getElementById('kode').addEventListener('keypress', async function(e) {
        if (e.key !== 'Enter') return;

        const id = this.value.trim();
        if (!id) return;

        this.disabled = true;

        try {
            const response = await axios.get(`/pos/barang/${id}`);
            const b = response.data.data;

            document.getElementById('nama').value = b.nama;
            document.getElementById('harga').value = b.harga;
            document.getElementById('jumlah').value = 1;
            document.getElementById('btn-tambah').disabled = false;

        } catch (error) {
            const message = error.response?.data?.message ?? 'Terjadi kesalahan pada server';
            Swal.fire('Error', message, 'error');
            resetForm();
        } finally {
            this.disabled = false;
            this.focus();
        }
    });

    // Tambah item ke cart
    document.getElementById('btn-tambah').addEventListener('click', function() {
        const item = {
            id_barang: document.getElementById('kode').value,
            nama: document.getElementById('nama').value,
            harga: parseInt(document.getElementById('harga').value),
            jumlah: parseInt(document.getElementById('jumlah').value),
        };
        item.subtotal = item.harga * item.jumlah;

        if (item.jumlah > 0) {
            const existingItem = cart.find(x => x.id_barang === item.id_barang);
            if (existingItem) {
                existingItem.jumlah += item.jumlah;
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
        document.querySelector('#table-cart tbody').innerHTML = html;
        document.getElementById('label-total').textContent = total;
        document.getElementById('btn-bayar').disabled = cart.length === 0;
    }

    function resetForm() {
        document.getElementById('kode').value = '';
        document.getElementById('nama').value = '';
        document.getElementById('harga').value = '';
        document.getElementById('jumlah').value = 1;
        document.getElementById('btn-tambah').disabled = true;
    }

    // Update jumlah dari tabel
    document.querySelector('#table-cart tbody').addEventListener('change', function(e) {
        if (!e.target.classList.contains('qty-edit')) return;
        const idx = parseInt(e.target.dataset.index);
        const val = parseInt(e.target.value);
        if (val > 0) {
            cart[idx].jumlah = val;
            cart[idx].subtotal = val * cart[idx].harga;
            renderCart();
        }
    });

    // Hapus item dari cart
    document.querySelector('#table-cart tbody').addEventListener('click', function(e) {
        if (!e.target.classList.contains('btn-hapus')) return;
        const idx = parseInt(e.target.dataset.index);
        cart.splice(idx, 1);
        renderCart();
    });

    // Submit pembayaran
    document.getElementById('btn-bayar').addEventListener('click', async function() {
        const btn = this;
        const originalText = btn.textContent;
        btn.innerHTML = '<span class="spinner-border spinner-border-sm"></span> Processing...';
        btn.disabled = true;

        const payload = {
            total: parseInt(document.getElementById('label-total').textContent),
            items: cart
        };

        try {
            const response = await axios.post('/pos/store', payload);
            Swal.fire('Success!', response.data.message, 'success');
            cart = [];
            renderCart();
        } catch (error) {
            const message = error.response?.data?.message ?? 'Gagal menyimpan transaksi';
            Swal.fire('Error!', message, 'error');
        } finally {
            btn.textContent = originalText;
            btn.disabled = cart.length === 0;
        }
    });
</script>
@endpush