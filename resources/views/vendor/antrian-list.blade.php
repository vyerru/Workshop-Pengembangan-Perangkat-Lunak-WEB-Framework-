@forelse($antrians as $antrian)
<div class="card mb-2 queue-card" data-id="{{ $antrian->id }}" data-status="{{ $antrian->status_antrian }}">
    <div class="card-body py-3">
        <div class="row align-items-center">
            <div class="col-auto text-center" style="min-width: 80px;">
                <div class="h3 mb-0 font-weight-bold text-primary">{{ $antrian->nomor_antrian }}</div>
                <small class="text-muted">{{ $antrian->created_at->format('H:i') }}</small>
            </div>
            <div class="col">
                <div class="font-weight-bold">{{ $antrian->nama }}</div>
                <small class="text-muted">{{ $antrian->kode_pesanan }}</small>
                <ul class="mb-0 pl-3 mt-1" style="font-size: 0.82rem;">
                    @foreach($antrian->detailPesanans as $detail)
                    <li>{{ $detail->jumlah }}x {{ $detail->menu->nama_menu }}</li>
                    @endforeach
                </ul>
            </div>
            <div class="col-auto text-right">
                <span class="badge badge-status badge-{{ $antrian->status_antrian }}">
                    @switch($antrian->status_antrian)
                        @case('pending') Pending @break
                        @case('diproses') Diproses @break
                        @case('siap_dipanggil') Siap Dipanggil @break
                        @case('selesai') Selesai @break
                    @endswitch
                </span>
                <div class="mt-2">
                    @if($antrian->status_antrian === 'pending')
                    <button class="btn btn-sm btn-info" onclick="updateStatus({{ $antrian->id }}, 'diproses')">Proses</button>
                    @elseif($antrian->status_antrian === 'diproses')
                    <button class="btn btn-sm btn-success" onclick="updateStatus({{ $antrian->id }}, 'siap_dipanggil')">Siap Panggil</button>
                    @elseif($antrian->status_antrian === 'siap_dipanggil')
                    <button class="btn btn-sm btn-outline-warning" onclick="panggilUlang({{ $antrian->id }})">Panggil Ulang</button>
                    <button class="btn btn-sm btn-secondary" onclick="updateStatus({{ $antrian->id }}, 'selesai')">Selesai</button>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@empty
<div class="card">
    <div class="card-body text-center py-5">
        <i class="mdi mdi-format-list-numbers" style="font-size: 48px; color: #c4c4c4;"></i>
        <h5 class="mt-3 text-muted">Belum Ada Antrian Hari Ini</h5>
        <p class="text-muted">Pesanan yang masuk akan muncul di sini.</p>
    </div>
</div>
@endforelse
