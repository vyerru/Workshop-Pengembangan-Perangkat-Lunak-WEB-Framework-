<div class="container-fluid page-body-wrapper">
  <nav class="sidebar sidebar-offcanvas" id="sidebar">
    <ul class="nav">
      <li class="nav-item nav-profile">
        <a href="#" class="nav-link">
          <div class="nav-profile-image">
            <img src="{{ asset('assets/images/faces/face1.jpg') }}" alt="profile" />
            <span class="login-status online"></span>
          </div>
          <div class="nav-profile-text d-flex flex-column">
            <span class="font-weight-bold mb-2">{{ auth()->user()->name ?? 'Guest' }}</span>
            <span class="text-secondary text-small">Project Manager</span>
          </div>
          <i class="mdi mdi-bookmark-check text-success nav-profile-badge"></i>
        </a>
      </li>
      <li class="nav-item {{ request()->is('home') ? 'active' : '' }}">
        <a class="nav-link" href="{{ route('home') }}">
          <span class="menu-title">Dashboard</span>
          <i class="mdi mdi-home menu-icon"></i>
        </a>
      </li>
      <li class="nav-item {{ request()->is('kategori*') ? 'active' : '' }}">
        <a class="nav-link" href="{{ route('kategori.index') }}">
          <span class="menu-title">Kategori</span>
          <i class="mdi mdi-format-list-bulleted menu-icon"></i>
        </a>
      </li>
      <li class="nav-item {{ request()->is('buku*') ? 'active' : '' }}">
        <a class="nav-link" href="{{ route('buku.index') }}">
          <span class="menu-title">Buku</span>
          <i class="mdi mdi-book-open-variant menu-icon"></i>
        </a>
      </li>
      <li class="nav-item {{ request()->is('barang*') ? 'active' : '' }}">
        <a class="nav-link" href="{{ url('barang') }}">
          <span class="menu-title">Barang</span>
          <i class="mdi mdi-package-variant menu-icon"></i>
        </a>
      </li>
      <li class="nav-item {{ request()->is('cetak/sertifikat') ? 'active' : '' }}">
        <a class="nav-link" href="{{ url('cetak/sertifikat') }}">
          <span class="menu-title">Cetak Sertifikat</span>
          <i class="mdi mdi-certificate menu-icon"></i>
        </a>
      </li>
      <li class="nav-item {{ request()->is('cetak/undangan') ? 'active' : '' }}">
        <a class="nav-link" href="{{ url('cetak/undangan') }}">
          <span class="menu-title">Cetak Undangan</span>
          <i class="mdi mdi-email menu-icon"></i>
        </a>
      </li>
      <li class="nav-item {{ request()->is('wilayah*') ? 'active' : '' }}">
        <a class="nav-link" data-bs-toggle="collapse" href="#wilayah-menu"
          aria-expanded="{{ request()->is('wilayah*') ? 'true' : 'false' }}"
          aria-controls="wilayah-menu">
          <span class="menu-title">Studi Kasus 1: Wilayah</span>
          <i class="menu-arrow"></i>
        </a>
        <div class="collapse {{ request()->is('wilayah*') ? 'show' : '' }}" id="wilayah-menu">
          <ul class="nav flex-column sub-menu">
            <li class="nav-item {{ request()->is('wilayah/data-asinkron') ? 'active' : '' }}">
              <a class="nav-link" href="{{ route('wilayah.ajax') }}">Metode AJAX</a>
            </li>
            <li class="nav-item {{ request()->is('wilayah/data-asinkron-modern') ? 'active' : '' }}">
              <a class="nav-link" href="{{ route('wilayah.axios') }}">Metode AXIOS</a>
            </li>
          </ul>
        </div>
      </li>

      <!-- Ganti dari pos* menjadi lebih spesifik -->
      <li class="nav-item {{ (request()->is('pos/ajax') || request()->is('pos/axios')) ? 'active' : '' }}">
        <a class="nav-link" data-bs-toggle="collapse" href="#pos-menu"
          aria-expanded="{{ (request()->is('pos/ajax') || request()->is('pos/axios')) ? 'true' : 'false' }}"
          aria-controls="pos-menu">
          <span class="menu-title">Studi Kasus 2: POS</span>
          <i class="menu-arrow"></i>
        </a>
        <div class="collapse {{ (request()->is('pos/ajax') || request()->is('pos/axios')) ? 'show' : '' }}" id="pos-menu">
          <ul class="nav flex-column sub-menu">
            <li class="nav-item {{ request()->is('pos/ajax') ? 'active' : '' }}">
              <a class="nav-link" href="{{ route('pos.ajax') }}">Metode AJAX</a>
            </li>
            <li class="nav-item {{ request()->is('pos/axios') ? 'active' : '' }}">
              <a class="nav-link" href="{{ route('pos.axios') }}">Metode AXIOS</a>
            </li>
          </ul>
        </div>
      </li>
      @if(Auth::check() && Auth::user()->role === 'vendor')
      <li class="nav-item nav-category">Menu Vendor</li>
      <li class="nav-item">
        <a class="nav-link" href="{{ route('vendor.dashboard') }}">
          <span class="menu-title">Dashboard Vendor</span>
          <i class="mdi mdi-television menu-icon"></i>
        </a>
      </li>
      <li class="nav-item">
        <a class="nav-link" href="{{ route('vendor.menus.index') }}">
          <span class="menu-title">Kelola Menu</span>
          <i class="mdi mdi-food menu-icon"></i>
        </a>
      </li>
      @endif

      @if(Auth::check() && Auth::user()->role === 'customer')
      <li class="nav-item {{ request()->is('canteen*') ? 'active' : '' }}">
        <a class="nav-link" data-bs-toggle="collapse" href="#canteen-menu"
          aria-expanded="{{ request()->is('canteen*') ? 'true' : 'false' }}"
          aria-controls="canteen-menu">
          <span class="menu-title">Kantin Online</span>
          <i class="menu-arrow"></i>
        </a>
        <div class="collapse {{ request()->is('canteen*') ? 'show' : '' }}" id="canteen-menu">
          <ul class="nav flex-column sub-menu">
            <li class="nav-item {{ request()->is('canteen/pesanan') ? 'active' : '' }}">
              <a class="nav-link" href="{{ route('canteen.pesanan') }}">Pesan Makanan</a>
            </li>
            <li class="nav-item {{ request()->is('canteen/customer/satu') ? 'active' : '' }}">
              <a class="nav-link" href="{{ route('canteen.customer.satu') }}">Customer Satu</a>
            </li>
            <li class="nav-item {{ request()->is('canteen/customer/dua') ? 'active' : '' }}">
              <a class="nav-link" href="{{ route('canteen.customer.dua') }}">Customer Dua</a>
            </li>
          </ul>
        </div>
      </li>
      @endif
    </ul>
  </nav>