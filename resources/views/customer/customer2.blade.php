@extends('layouts.app') {{-- Sesuaikan dengan layoutmu --}}

@section('content')
<div class="container mt-4">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">Tambah Data Customer</div>
                
                <div class="card-body">
                    
                    <div id="kamera-container" class="mb-3 text-center">
                        <p class="text-muted small">Alat Tangkap Biometrik (Kamera)</p>
                        
                        <video id="kamera-video" width="400" height="300" autoplay playsinline style="background:#000; border-radius:8px; border:2px solid #ccc; transform: scaleX(-1);"></video>
                        
                        <canvas id="kamera-canvas" width="400" height="300" style="display:none; background:#ccc; border-radius:8px; border:2px solid #28a745; margin: 0 auto;"></canvas>
                    </div>

                    <div class="text-center mb-4">
                        <button type="button" id="btn-snapshot" class="btn btn-warning">Ambil Foto</button>
                        <button type="button" id="btn-retake" class="btn btn-secondary" style="display:none;">Ulangi Foto</button>
                    </div>
                    <hr>

                    <form action="{{ route('canteen.customer.store') }}" method="POST" id="form-customer">
                        @csrf
                        
                        <div class="form-group mb-3">
                            <label>Nama Customer</label>
                            <input type="text" name="nama" class="form-control" required>
                        </div>
                        <div class="form-group mb-3">
                            <label>Alamat Lengkap</label>
                            <textarea name="alamat" class="form-control" rows="3" required></textarea>
                        </div>
                        <input type="hidden" name="foto_base64" id="input-foto" required>

                        <input type="hidden" name="skenario" value="path"> 

                        <button type="submit" class="btn btn-primary" id="btn-submit" disabled>Simpan Data Customer</button>
                    </form>

                </div>
            </div>
        </div>
    </div>
</div>

<script>
    // Inisialisasi DOM Elements
    const video = document.getElementById('kamera-video');
    const canvas = document.getElementById('kamera-canvas');
    const btnSnapshot = document.getElementById('btn-snapshot');
    const btnRetake = document.getElementById('btn-retake');
    const inputFoto = document.getElementById('input-foto');
    const btnSubmit = document.getElementById('btn-submit');

    // 1. Mesin Aliran Kamera (Stream)
    async function initCamera() {
        try {
            // Meminta izin browser ke hardware video
            const stream = await navigator.mediaDevices.getUserMedia({ video: { facingMode: "user" } });
            video.srcObject = stream;
        } catch (error) {
            console.error('Diagnosis Akses Kamera:', error);
            alert('FATAL: Tidak dapat mengakses kamera. Pastikan browser mengizinkan akses kamera dan Anda berada di Secure Context (Localhost/HTTPS).');
        }
    }

    // Jalankan kamera saat halaman dimuat
    initCamera();

    // 2. Mesin Penangkap Frame (I/O)
    btnSnapshot.addEventListener('click', function() {
        // Gambar pixel dari tag <video> ke tag <canvas>
        const context = canvas.getContext('2d');
        context.drawImage(video, 0, 0, canvas.width, canvas.height);
        const imageDataURL = canvas.toDataURL('image/jpeg', 0.8);

        video.style.display = 'none';      // Sembunyikan live preview
        canvas.style.display = 'block';    // Tampilkan hasil jepretan
        btnSnapshot.style.display = 'none';
        btnRetake.style.display = 'inline-block';

        // Injeksi Payload ke Form
        inputFoto.value = imageDataURL;
        
        // Buka kunci tombol simpan
        btnSubmit.disabled = false;
    });

    // 3. Mesin Reset Alur
    btnRetake.addEventListener('click', function() {
        video.style.display = 'block';
        canvas.style.display = 'none';
        btnSnapshot.style.display = 'inline-block';
        btnRetake.style.display = 'none';

        // Hapus payload dari memori input
        inputFoto.value = '';
        btnSubmit.disabled = true;
    });
</script>
@endsection