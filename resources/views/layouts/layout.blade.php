@php
$auth = currentUser();
$user = $auth['user'];
$nama = $user->nama_lengkap ?? '';
$role = $auth['role'];
@endphp
<!DOCTYPE html>
<html lang="en" dir="ltr" data-startbar="dark" data-bs-theme="light">

<head>
  <meta charset="utf-8" />
  <title>MAN 2 Tangerang</title>
  <meta
    name="viewport"
    content="width=device-width, initial-scale=1, shrink-to-fit=no" />
  <meta http-equiv="X-UA-Compatible" content="IE=edge" />

  <!-- App favicon -->
  <link rel="shortcut icon" href="{{ asset('assets/images/favicon.ico') }}" />

  <!-- App css -->
  <link href="{{ asset('assets/libs/simple-datatables/style.css') }}" rel="stylesheet" type="text/css" />
  <link href="{{ asset('assets/libs/sweetalert2/sweetalert2.min.css') }}" rel="stylesheet" type="text/css">
  <link href="{{ asset('assets/libs/animate.css/animate.min.css') }}" rel="stylesheet" type="text/css">
  <link
    href="{{ asset('assets/css/bootstrap.min.css') }}"
    rel="stylesheet"
    type="text/css" />
  <link href="{{ asset('assets/css/icons.min.css') }}" rel="stylesheet" type="text/css" />
  <link href="{{ asset('assets/css/app.min.css') }}" rel="stylesheet" type="text/css" />
</head>

<body>
  <!-- Top Bar Start -->
  <div class="topbar d-print-none">
    <div class="container-fluid">
      <nav
        class="topbar-custom d-flex justify-content-between"
        id="topbar-custom">
        <ul
          class="topbar-item list-unstyled d-inline-flex align-items-center mb-0">
          <li>
            <button class="nav-link mobile-menu-btn nav-icon" id="togglemenu">
              <i class="iconoir-menu"></i>
            </button>
          </li>
        </ul>
        <ul
          class="topbar-item list-unstyled d-inline-flex align-items-center mb-0">

          <li class="topbar-item">
            <a
              class="nav-link nav-icon"
              href="javascript:void(0);"
              id="light-dark-mode">
              <i class="iconoir-half-moon dark-mode"></i>
              <i class="iconoir-sun-light light-mode"></i>
            </a>
          </li>

          <li class="dropdown topbar-item">
            <a
              class="nav-link dropdown-toggle arrow-none nav-icon"
              data-bs-toggle="dropdown"
              href="#"
              role="button"
              aria-haspopup="false"
              aria-expanded="false"
              data-bs-offset="0,19">
              <img
                src="{{ asset('assets/images/users/avatar-5.jpg') }}"
                alt=""
                class="thumb-md rounded-circle" />
            </a>
            <div class="dropdown-menu dropdown-menu-end py-0">
              <div
                class="d-flex align-items-center dropdown-item py-2 bg-secondary-subtle">
                <div class="flex-shrink-0">
                  <img
                    src="{{ asset('assets/images/users/avatar-5.jpg') }}"
                    alt=""
                    class="thumb-md rounded-circle" />
                </div>
                <div class="flex-grow-1 ms-2 text-truncate align-self-center">
                  <h6 class="my-0 fw-medium text-dark fs-13">
                    {{ $nama }}
                  </h6>
                  <small class="text-muted mb-0">{{ $role }}</small>
                </div>
                <!--end media-body-->
              </div>
              <a class="dropdown-item text-danger" href="#" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                <i class="las la-power-off fs-18 me-1 align-text-bottom"></i>
                Logout
              </a>
              <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                @csrf
              </form>
            </div>
          </li>
        </ul>
        <!--end topbar-nav-->
      </nav>
      <!-- end navbar-->
    </div>
  </div>
  <!-- Top Bar End -->
  <!-- leftbar-tab-menu -->
  <div class="startbar d-print-none">
    <!--start brand-->
    <div class="w-full">
      <a href="index.html" class="d-flex align-items-center text-decoration-none">
        <img
          src="{{ asset('assets/images/logo-man.png') }}"
          alt="logo-large"
          style="height: 60px"
          class="mx-3 my-2" />
          <p class="text-uppercase text-white">MAN 2 Tangerang</p>
      </a>
    </div>
    <!--end brand-->
    <!--start startbar-menu-->
    <div class="startbar-menu">
      <div class="startbar-collapse" id="startbarCollapse" data-simplebar>
        <div class="d-flex align-items-start flex-column w-100">
          <!-- Navigation -->
          <ul class="navbar-nav mb-auto w-100">
            <li class="menu-label mt-2">
              <span>Main</span>
            </li>

            <!-- navigasi admin -->
            @auth('admin')
            <li class="nav-item">
              <a class="nav-link" href="{{ route('admin.index') }}">
                <i class="iconoir-report-columns menu-icon"></i>
                <span>Dashboard</span>
              </a>
            </li>
            <li class="nav-item">
              <a class="nav-link" href="{{ route('admin.kelas.index') }}">
                <i class="iconoir-paste-clipboard menu-icon"></i>
                <span>Manajemen Kelas</span>
              </a>
            </li>
            <li class="nav-item">
              <a class="nav-link" href="{{ route('admin.mata-pelajaran.index') }}">
                <i class="iconoir-book menu-icon"></i>
                <span>Manajemen Mata Pelajaran</span>
              </a>
            </li>
            <li class="nav-item">
              <a class="nav-link" href="{{ route('admin.jadwal-pelajaran.index') }}">
                <i class="iconoir-book-stack menu-icon"></i>
                <span>Manajemen Jadwal Pelajaran</span>
              </a>
            </li>
            <li class="nav-item">
              <a
                class="nav-link"
                href="#sidebarTransactions"
                data-bs-toggle="collapse"
                role="button"
                aria-expanded="false"
                aria-controls="sidebarTransactions">
                <i class="iconoir-community menu-icon"></i>
                <span>Manajemen Pengguna</span>
              </a>
              <div class="collapse" id="sidebarTransactions">
                <ul class="nav flex-column">
                  <li class="nav-item">
                    <a class="nav-link" href="{{ route('admin.user.admin.index') }}">Admin</a>
                  </li>
                  <li class="nav-item">
                    <a class="nav-link" href="{{ route('admin.user.guru.index') }}">Guru</a>
                  </li>
                  <li class="nav-item">
                    <a class="nav-link" href="{{ route('admin.user.siswa.index') }}">Siswa</a>
                  </li>
                </ul>
              </div>
            </li>
            @endauth

            <!-- navigasi guru -->
            @auth('guru')
            <li class="nav-item">
              <a class="nav-link" href="{{ route('guru.index') }}">
                <i class="iconoir-report-columns menu-icon"></i>
                <span>Dashboard</span>
              </a>
            </li>
            <li class="nav-item">
              <a class="nav-link" href="{{ route('guru.jadwal.index') }}">
                <i class="iconoir-calendar menu-icon"></i>
                <span>Jadwal Guru</span>
              </a>
            </li>
            @endauth

            <!-- navigasi siswa -->
            @auth('siswa')
            <li class="nav-item">
              <a class="nav-link" href="{{ route('siswa.index') }}">
                <i class="iconoir-report-columns menu-icon"></i>
                <span>Dashboard</span>
              </a>
            </li>
            <li class="nav-item">
              <a class="nav-link" href="{{ route('siswa.scan') }}">
                <i class="iconoir-scan-barcode menu-icon"></i>
                <span>Scan Barcode Absen</span>
              </a>
            </li>
            <li class="nav-item">
              <a class="nav-link" href="{{ route('siswa.jadwal.index') }}">
                <i class="iconoir-calendar menu-icon"></i>
                <span>Jadwal Siswa</span>
              </a>
            </li>
            @endauth
          </ul>
          <!--end navbar-nav--->
        </div>
      </div>
      <!--end startbar-collapse-->
    </div>
    <!--end startbar-menu-->
  </div>
  <!--end startbar-->
  <div class="startbar-overlay d-print-none"></div>
  <!-- end leftbar-tab-menu-->

  <div class="position-relative" style="min-height: 100vh;">
    <!-- Page Content-->
    <div class="position-absolute top-50 translate-middle opacity-25"
      style="left: calc(50% + 120px); z-index: 0; pointer-events: none;">
      <img src="{{ asset('assets/images/logo-man.png') }}">
    </div>
    <div class="page-wrapper" style="position: relative;">
      @yield('content')
    </div>
    <!-- end page content -->
  </div>
  <!-- end page-wrapper -->

  <!-- Javascript  -->
  <!-- vendor js -->

  @stack('scripts')

  <script src="{{ asset('assets/libs/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
  <script src="{{ asset('assets/libs/simplebar/simplebar.min.js') }}"></script>

  <script src="{{ asset('assets/libs/apexcharts/apexcharts.min.js') }}"></script>
  <script src="https://apexcharts.com/samples/assets/stock-prices.js"></script>
  <script src="{{ asset('assets/js/pages/index.init.js') }}"></script>
  <script src="{{ asset('assets/js/DynamicSelect.js') }}"></script>
  <script src="{{ asset('assets/js/app.js') }}"></script>
  <script src="{{ asset('assets/libs/simple-datatables/umd/simple-datatables.js') }}"></script>
  <script src="{{ asset('assets/libs/sweetalert2/sweetalert2.min.js') }}"></script>
  <script src="{{ asset('assets/js/pages/sweet-alert.init.js') }}"></script>
  <script src="{{ asset('assets/js/pages/datatable.init.js') }}"></script>
  <script src="{{ asset('assets/js/swal-delete.js') }}"></script>

  <!-- toast -->
  <script>
    @if(session('success'))
    Swal.fire({
      toast: true,
      position: 'top',
      icon: 'success',
      title: "<span style='color: black'>{{ session('success') }}</span>",
      showConfirmButton: false,
      timer: 2500,
      timerProgressBar: true,
      background: '#fff'
    });
    @endif

    @if(session('error'))
    Swal.fire({
      toast: true,
      position: 'top',
      icon: 'error',
      title: "<span style='color: black'>{{ session('error') }}</span>",
      showConfirmButton: false,
      timer: 3000,
      timerProgressBar: true,
      background: '#fff',
      color: '#000'
    });
    @endif

    @if(session('info'))
    Swal.fire({
      toast: true,
      position: 'top',
      icon: 'info',
      title: "<span style='color: black'>{{ session('info') }}</span>",
      showConfirmButton: false,
      timer: 3000,
      timerProgressBar: true,
      background: '#fff',
      color: '#000'
    });
    @endif
  </script>
</body>
<!--end body-->

</html>