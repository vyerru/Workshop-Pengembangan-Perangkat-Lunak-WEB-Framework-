@extends('layouts.app')

@push('styles')
<style>
    .queue-card {
        transition: all 0.2s ease;
    }
    .queue-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0,0,0,0.1);
    }
    .badge-pending {
        background: #ffc107;
        color: #212529;
    }
    .badge-diproses {
        background: #17a2b8;
        color: #fff;
    }
    .badge-siap_dipanggil {
        background: #28a745;
        color: #fff;
        animation: pulse 1.5s infinite;
    }
    .badge-selesai {
        background: #6c757d;
        color: #fff;
    }
    @keyframes pulse {
        0% { opacity: 1; }
        50% { opacity: 0.7; }
        100% { opacity: 1; }
    }
    #antrian-container {
        min-height: 300px;
    }
</style>
@endpush

@section('content')
<div class="page-header d-flex justify-content-between align-items-center">
    <h3 class="page-title">Antrian Digital</h3>
    <span class="text-muted">{{ now()->format('d M Y') }}</span>
</div>

<div class="row mb-3">
    <div class="col-md-12">
        <div class="card">
            <div class="card-body">
                <div class="d-flex gap-2 flex-wrap" id="filter-status">
                    <button class="btn btn-sm btn-outline-secondary filter-btn active" data-filter="all">Semua</button>
                    <button class="btn btn-sm btn-outline-warning filter-btn" data-filter="pending">Pending</button>
                    <button class="btn btn-sm btn-outline-info filter-btn" data-filter="diproses">Diproses</button>
                    <button class="btn btn-sm btn-outline-success filter-btn" data-filter="siap_dipanggil">Siap Dipanggil</button>
                    <button class="btn btn-sm btn-outline-secondary filter-btn" data-filter="selesai">Selesai</button>
                </div>
            </div>
        </div>
    </div>
</div>

<div id="antrian-container">
    @include('vendor.antrian-list', ['antrians' => $antrians])
</div>
@endsection

@push('page-scripts')
<script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
<script>
    axios.defaults.headers.common['X-CSRF-TOKEN'] = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    axios.defaults.headers.common['Accept'] = 'application/json';

    let activeFilter = 'all';

    document.querySelectorAll('.filter-btn').forEach(btn => {
        btn.addEventListener('click', function () {
            document.querySelectorAll('.filter-btn').forEach(b => b.classList.remove('active'));
            this.classList.add('active');
            activeFilter = this.dataset.filter;
            applyFilter();
        });
    });

    function applyFilter() {
        document.querySelectorAll('.queue-card').forEach(card => {
            if (activeFilter === 'all' || card.dataset.status === activeFilter) {
                card.style.display = '';
            } else {
                card.style.display = 'none';
            }
        });
    }

    function panggilUlang(pesananId) {
        const url = `{{ url('vendor/pesanan') }}/${pesananId}/panggil-ulang`;
        axios.post(url)
            .then(function (response) {
                if (response.data.status === 'success') {
                    refreshQueue();
                }
            })
            .catch(function (error) {
                const msg = error.response?.data?.message || 'Gagal memanggil ulang.';
                alert(msg);
            });
    }

    function updateStatus(pesananId, status) {
        const url = `{{ url('vendor/pesanan') }}/${pesananId}/status`;
        axios.patch(url, { status })
            .then(function (response) {
                if (response.data.status === 'success') {
                    const card = document.querySelector(`.queue-card[data-id="${pesananId}"]`);
                    if (card) {
                        card.dataset.status = status;
                        card.querySelector('.badge-status').className = `badge badge-status badge-${status}`;
                        const labels = {
                            pending: 'Pending',
                            diproses: 'Diproses',
                            siap_dipanggil: 'Siap Dipanggil',
                            selesai: 'Selesai',
                        };
                        card.querySelector('.badge-status').textContent = labels[status] || status;
                        applyFilter();
                    }
                }
            })
            .catch(function (error) {
                const msg = error.response?.data?.message || 'Gagal memperbarui status.';
                alert(msg);
            });
    }

    function refreshQueue() {
        axios.get('{{ route("vendor.antrian") }}', {
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        })
        .then(function (response) {
            const parser = new DOMParser();
            const html = parser.parseFromString(response.data, 'text/html');
            const newContainer = html.getElementById('antrian-container');
            if (newContainer) {
                document.getElementById('antrian-container').innerHTML = newContainer.innerHTML;
                applyFilter();
            }
        })
        .catch(function () {});
    }

    setInterval(refreshQueue, 15000);
</script>
@endpush
