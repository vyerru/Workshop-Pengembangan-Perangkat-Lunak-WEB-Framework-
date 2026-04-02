<div class="container-fluid page-body-wrapper">
  <!-- partial:partials/_sidebar.html -->
  <nav class="sidebar sidebar-offcanvas" id="sidebar">
    <ul class="nav">
      <li class="nav-item nav-profile">
        <a href="#" class="nav-link">
          <div class="nav-profile-image">
            <img src="assets/images/faces/face1.jpg" alt="profile" />
            <span class="login-status online"></span>
            <!--change to offline or busy as needed-->
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
        <span class="menu-title">Dashboard</span> <i class="mdi mdi-home menu-icon"></i>
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
    </ul>
  </nav>
