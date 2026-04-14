@extends('layouts.app')

@section('content')
<div class="row mb-3">
    <div class="col-12">
        <h4 class="font-weight-bold">Kantin - Pesan Makanan</h4>
        <p class="text-muted">Pilih vendor dan tambahkan menu ke keranjang belanja Anda.</p>
    </div>
</div>

{{-- Baris Utama: Katalog (kiri) + Keranjang (kanan) --}}
<div class="row">

    {{-- ==================== KOLOM KIRI: Pilih Vendor + Katalog Menu ==================== --}}
    <div class="col-md-8">

        {{-- Dropdown Pilih Vendor --}}
        <div class="card mb-3">
            <div class="card-body">
                <h5 class="card-title mb-3">Pilih Vendor</h5>
                <div class="form-group mb-0">
                    <select id="vendor-select" class="form-control form-control-lg">
                        <option value="">-- Pilih Vendor --</option>
                        @foreach ($vendors as $vendor)
                        <option value="{{ $vendor->id }}">{{ $vendor->nama_vendor }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>

        {{-- Katalog Menu --}}
        <div id="menuContainer">
            {{-- State kosong awal --}}
            <div id="empty-state" class="card">
                <div class="card-body text-center py-5">
                    <i class="mdi mdi-store-outline" style="font-size: 60px; color: #c4c4c4;"></i>
                    <h5 class="mt-3 text-muted">Belum Ada Vendor Dipilih</h5>
                    <p class="text-muted">Pilih vendor di atas untuk melihat daftar menu yang tersedia.</p>
                </div>
            </div>

            {{-- Loading state (tersembunyi) --}}
            <div id="loading-state" class="card d-none">
                <div class="card-body text-center py-5">
                    <div class="spinner-border text-primary" role="status">
                        <span class="sr-only">Loading...</span>
                    </div>
                    <p class="mt-3 text-muted">Memuat daftar menu...</p>
                </div>
            </div>

            {{-- Grid menu (tersembunyi, diisi oleh JS) --}}
            <div id="menu-grid" class="row d-none"></div>

            {{-- State jika tidak ada menu --}}
            <div id="no-menu-state" class="card d-none">
                <div class="card-body text-center py-5">
                    <i class="mdi mdi-food-off" style="font-size: 60px; color: #c4c4c4;"></i>
                    <h5 class="mt-3 text-muted">Tidak Ada Menu</h5>
                    <p class="text-muted">Vendor ini belum memiliki menu yang tersedia saat ini.</p>
                </div>
            </div>
        </div>

    </div>

    {{-- ==================== KOLOM KANAN: Keranjang Belanja ==================== --}}
    <div class="col-md-4">
        <div class="card sticky-top" style="top: 20px;">
            <div class="card-body">
                <h5 class="card-title mb-3">
                    <i class="mdi mdi-cart-outline mr-2"></i>Keranjang Belanja
                </h5>

                {{-- List item keranjang --}}
                <div id="cart-items">
                    <p id="cart-empty-msg" class="text-center text-muted py-3">
                        <i class="mdi mdi-cart-remove" style="font-size: 32px;"></i><br>
                        Keranjang masih kosong
                    </p>
                </div>

                <hr>

                {{-- Total harga --}}
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h6 class="mb-0 font-weight-bold">Total Pembayaran:</h6>
                    <h5 class="mb-0 text-primary font-weight-bold">
                        Rp <span id="label-total">0</span>
                    </h5>
                </div>

                {{-- Tombol Bayar --}}
                <button type="button" id="btn-bayar" class="btn btn-success btn-block btn-lg" disabled>
                    <i class="mdi mdi-cash-multiple mr-1"></i> Bayar Sekarang
                </button>
            </div>
        </div>
    </div>

</div>
@endsection

@push('page-scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
<script type="text/javascript" src="https://app.sandbox.midtrans.com/snap/snap.js" data-client-key="SB-Mid-client-kkXxfx2yPHiPM5-W"></script>
<script>
    // ====================================================
    // SETUP AXIOS GLOBAL
    // ====================================================
    axios.defaults.headers.common['X-CSRF-TOKEN'] = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    axios.defaults.headers.common['Accept'] = 'application/json';

    // ====================================================
    // STATE KERANJANG
    // ====================================================
    let cart = [];

    // ====================================================
    // HELPER: Format angka ke format Rupiah tanpa simbol
    // ====================================================
    function formatRupiah(angka) {
        return angka.toLocaleString('id-ID');
    }

    // ====================================================
    // HELPER: Tampilkan / sembunyikan state di katalog
    // ====================================================
    function showState(state) {
        document.getElementById('empty-state').classList.add('d-none');
        document.getElementById('loading-state').classList.add('d-none');
        document.getElementById('menu-grid').classList.add('d-none');
        document.getElementById('no-menu-state').classList.add('d-none');

        if (state === 'empty') document.getElementById('empty-state').classList.remove('d-none');
        if (state === 'loading') document.getElementById('loading-state').classList.remove('d-none');
        if (state === 'grid') document.getElementById('menu-grid').classList.remove('d-none');
        if (state === 'no-menu') document.getElementById('no-menu-state').classList.remove('d-none');
    }

    // ====================================================
    // FETCH MENU SAAT VENDOR BERUBAH
    // ====================================================
    document.getElementById('vendor-select').addEventListener('change', async function() {
        const vendorId = this.value;

        if (!vendorId) {
            showState('empty');
            return;
        }

        showState('loading');

        try {
            const response = await axios.get(`/canteen/pos/menus/${vendorId}`);
            const menus = response.data.data;

            if (!menus || menus.length === 0) {
                showState('no-menu');
                return;
            }

            renderMenuGrid(menus);
            showState('grid');

        } catch (error) {
            const message = error.response?.data?.message ?? 'Gagal memuat data menu. Coba lagi.';
            Swal.fire('Gagal!', message, 'error');
            showState('empty');
        }
    });

    // ====================================================
    // RENDER GRID KARTU MENU
    // ====================================================
    function renderMenuGrid(menus) {
        const grid = document.getElementById('menu-grid');
        let html = '';

        menus.forEach(menu => {
            const hargaFormatted = formatRupiah(parseInt(menu.harga));
            const gambar = menu.gambar ?
                `/storage/${menu.gambar}` :
                `https://via.placeholder.com/300x180?text=No+Image`;

            html += `
                <div class="col-sm-6 col-lg-4 mb-3">
                    <div class="card h-100 shadow-sm">
                        <img src="${gambar}"
                             class="card-img-top"
                             alt="${menu.nama}"
                             style="height: 150px; object-fit: cover;"
                             onerror="this.src='https://via.placeholder.com/300x180?text=No+Image'">
                        <div class="card-body d-flex flex-column">
                            <h6 class="card-title font-weight-bold mb-1">${menu.nama_menu}</h6>
                            <p class="text-muted small mb-2">${menu.deskripsi ?? ''}</p>
                            <p class="text-primary font-weight-bold mb-3">Rp ${hargaFormatted}</p>
                            <button
                                class="btn btn-primary btn-sm mt-auto btn-tambah-menu"
                                data-id="${menu.id}"
                                data-nama="${menu.nama}"
                                data-harga="${menu.harga}"
                                data-gambar="${gambar}">
                                <i class="mdi mdi-cart-plus"></i> Tambah
                            </button>
                        </div>
                    </div>
                </div>
            `;
        });

        grid.innerHTML = html;
    }

    // ====================================================
    // EVENT: TAMBAH MENU KE KERANJANG (delegasi dari grid)
    // ====================================================
    document.getElementById('menu-grid').addEventListener('click', function(e) {
        const btn = e.target.closest('.btn-tambah-menu');
        if (!btn) return;

        const item = {
            id_menu: btn.dataset.id,
            nama: btn.dataset.nama,
            harga: parseInt(btn.dataset.harga),
            jumlah: 1,
        };
        item.subtotal = item.harga * item.jumlah;

        const existing = cart.find(x => x.id_menu === item.id_menu);
        if (existing) {
            existing.jumlah++;
            existing.subtotal = existing.jumlah * existing.harga;
        } else {
            cart.push(item);
        }

        renderCart();

        // Animasi feedback kecil pada tombol
        btn.innerHTML = '<i class="mdi mdi-check"></i> Ditambahkan';
        btn.classList.replace('btn-primary', 'btn-success');
        setTimeout(() => {
            btn.innerHTML = '<i class="mdi mdi-cart-plus"></i> Tambah';
            btn.classList.replace('btn-success', 'btn-primary');
        }, 1000);
    });

    // ====================================================
    // RENDER KERANJANG
    // ====================================================
    function renderCart() {
        const container = document.getElementById('cart-items');
        const emptyMsg = document.getElementById('cart-empty-msg');

        if (cart.length === 0) {
            container.innerHTML = `
                <p id="cart-empty-msg" class="text-center text-muted py-3">
                    <i class="mdi mdi-cart-remove" style="font-size: 32px;"></i><br>
                    Keranjang masih kosong
                </p>`;
            document.getElementById('label-total').textContent = '0';
            document.getElementById('btn-bayar').disabled = true;
            return;
        }

        let total = 0;
        let html = '';

        cart.forEach((item, index) => {
            total += item.subtotal;
            html += `
                <div class="d-flex align-items-start mb-2 pb-2 border-bottom">
                    <div class="flex-grow-1 mr-2">
                        <div class="font-weight-semibold" style="font-size:0.88rem;">${item.nama}</div>
                        <div class="text-muted" style="font-size:0.82rem;">Rp ${formatRupiah(item.harga)} / item</div>
                        <div class="d-flex align-items-center mt-1">
                            <button class="btn btn-outline-secondary btn-xs btn-qty-minus" data-index="${index}" style="padding:1px 7px;">-</button>
                            <span class="mx-2 font-weight-bold">${item.jumlah}</span>
                            <button class="btn btn-outline-secondary btn-xs btn-qty-plus" data-index="${index}" style="padding:1px 7px;">+</button>
                        </div>
                    </div>
                    <div class="text-right">
                        <div class="text-primary font-weight-bold" style="font-size:0.88rem;">Rp ${formatRupiah(item.subtotal)}</div>
                        <button class="btn btn-link text-danger btn-sm p-0 btn-hapus-cart" data-index="${index}" style="font-size:0.78rem;">Hapus</button>
                    </div>
                </div>
            `;
        });

        container.innerHTML = html;
        document.getElementById('label-total').textContent = formatRupiah(total);
        document.getElementById('btn-bayar').disabled = false;
    }

    // ====================================================
    // EVENT: MINUS / PLUS / HAPUS ITEM DI KERANJANG
    // ====================================================
    document.getElementById('cart-items').addEventListener('click', function(e) {
        // Kurangi jumlah
        if (e.target.classList.contains('btn-qty-minus')) {
            const idx = parseInt(e.target.dataset.index);
            if (cart[idx].jumlah > 1) {
                cart[idx].jumlah--;
                cart[idx].subtotal = cart[idx].jumlah * cart[idx].harga;
            } else {
                cart.splice(idx, 1);
            }
            renderCart();
            return;
        }

        // Tambah jumlah
        if (e.target.classList.contains('btn-qty-plus')) {
            const idx = parseInt(e.target.dataset.index);
            cart[idx].jumlah++;
            cart[idx].subtotal = cart[idx].jumlah * cart[idx].harga;
            renderCart();
            return;
        }

        // Hapus item
        if (e.target.classList.contains('btn-hapus-cart')) {
            const idx = parseInt(e.target.dataset.index);
            cart.splice(idx, 1);
            renderCart();
        }
    });

    // ====================================================
    // EVENT: TOMBOL BAYAR SEKARANG
    // ====================================================
    document.getElementById('btn-bayar').addEventListener('click', async function(e) {
        e.preventDefault();
        if (cart.length === 0) return;

        // Validasi Kritis: Tangkap ID Vendor yang sedang aktif
        const vendorId = document.getElementById('vendor-select').value;
        if (!vendorId) {
            Swal.fire('Kesalahan', 'Vendor tidak terdeteksi. Silakan pilih vendor ulang.', 'error');
            return;
        }

        const btn = this;
        const originalText = btn.innerHTML;

        // Restrukturisasi Payload: Sesuaikan dengan validasi backend
        const payload = {
            vendor_id: vendorId,
            cart: cart.map(item => ({
                menu_id: item.id_menu, // Mapping properti frontend ke key yang diminta backend
                jumlah: item.jumlah
            }))
        };

        btn.innerHTML = '<span class="spinner-border spinner-border-sm mr-1"></span> Mengamankan Transaksi...';
        btn.disabled = true;

        // try {
        //     const response = await axios.post('/canteen/checkout', payload);

        //     // Intersepsi Respons Server
        //     if (response.data.status === 'success' && response.data.snap_token) {
        //         // Panggil Popup Midtrans menggunakan Token dari Server
        //         window.snap.pay(response.data.snap_token, {
        //             onSuccess: function(result) {
        //                 Swal.fire('Pembayaran Berhasil!', 'Silakan ambil pesanan Anda.', 'success').then(() => {
        //                     window.location.reload(); // Refresh untuk membersihkan state
        //                 });
        //             },
        //             onPending: function(result) {
        //                 Swal.fire('Menunggu', 'Selesaikan pembayaran pada metode yang dipilih.', 'info');
        //             },
        //             onError: function(result) {
        //                 Swal.fire('Gagal', 'Transaksi ditolak oleh sistem pembayaran.', 'error');
        //             },
        //             onClose: function() {
        //                 Swal.fire('Dibatalkan', 'Anda menutup jendela pembayaran sebelum selesai.', 'warning');
        //             }
        //         });

        //         // Kosongkan keranjang di latar belakang saat popup Midtrans muncul
        //         cart = [];
        //         renderCart();
        //     } else {
        //         Swal.fire('Kegagalan Sistem', 'Gagal mendapatkan token transaksi.', 'error');
        //     }

        // } catch (error) {
        //     // Tampilkan pesan error spesifik dari Laravel validation jika ada
        //     const message = error.response?.data?.message || 'Gagal terhubung ke server.';
        //     Swal.fire('Transaksi Ditolak', message, 'error');
        // } finally {
        //     btn.innerHTML = originalText;
        //     btn.disabled = cart.length === 0;
        // }
        try {
            const response = await axios.post('/canteen/checkout', payload);

            if (response.data.status === 'success') {
                Swal.fire({
                    title: 'Pembayaran Berhasil!',
                    text: 'Pesanan Anda telah Lunas.',
                    icon: 'success',
                    confirmButtonText: 'Tutup'
                }).then(() => {
                    cart = []; // Kosongkan keranjang
                    renderCart();
                    window.location.reload(); // Refresh halaman
                });
            } else {
                Swal.fire('Kegagalan Sistem', 'Gagal menyimpan pesanan.', 'error');
            }

        } catch (error) {
            const message = error.response?.data?.message || 'Gagal terhubung ke server.';
            Swal.fire('Transaksi Ditolak', message, 'error');
        } finally {
            btn.innerHTML = originalText;
            btn.disabled = cart.length === 0;
        }
    });
</script>
@endpush