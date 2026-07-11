<!DOCTYPE html>
<html lang="en" dir="ltr" data-startbar="dark" data-bs-theme="light">

<head>
  <meta charset="utf-8" />
  <title>Login | MAN 2 Tangerang</title>
  <meta
    name="viewport"
    content="width=device-width, initial-scale=1, shrink-to-fit=no" />
  <meta http-equiv="X-UA-Compatible" content="IE=edge" />

  <!-- App favicon -->
  <link rel="shortcut icon" href="{{ asset('assets/images/favicon.ico') }}" />

  <!-- App css -->
  <link
    href="{{ asset('assets/css/bootstrap.min.css') }}"
    rel="stylesheet"
    type="text/css" />
  <link href="{{ asset('assets/css/icons.min.css') }}" rel="stylesheet" type="text/css" />
  <link href="{{ asset('assets/css/app.min.css') }}" rel="stylesheet" type="text/css" />
  <link href="{{ asset('assets/libs/sweetalert2/sweetalert2.min.css') }}" rel="stylesheet" type="text/css">
  <link href="{{ asset('assets/libs/animate.css/animate.min.css') }}" rel="stylesheet" type="text/css">
</head>

<!-- Top Bar Start -->

<body>
  <div class="container-xxl position-relative" style="height: 100vh;">
    <div class="position-absolute top-50 start-50 translate-middle opacity-25">
      <img src="{{ asset('assets/images/logo-man.png') }}" class="w-100">
    </div>
    <div class="row vh-100 d-flex justify-content-center">
      @yield('content')
      <!--end row-->
    </div>
    <!-- container -->

    <script src="{{ asset('assets/libs/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('assets/libs/sweetalert2/sweetalert2.min.js') }}"></script>
    <script src="{{ asset('assets/js/pages/sweet-alert.init.js') }}"></script>
    <script>
      @if(session('success'))
      Swal.fire({
        toast: true,
        position: 'top',
        icon: 'success',
        title: '{{ session('
        success ') }}',
        showConfirmButton: false,
        timer: 2500,
        timerProgressBar: true,
        background: '#fff',
      });
      @endif

      @if(session('error'))
      Swal.fire({
        toast: true,
        position: 'top',
        icon: 'error',
        title: '{{ session('
        error ') }}',
        showConfirmButton: false,
        timer: 3000,
        timerProgressBar: true,
      });
      @endif

      @if(session('info'))
      Swal.fire({
        toast: true,
        position: 'top',
        icon: 'info',
        title: '{{ session('
        info ') }}',
        showConfirmButton: false,
        timer: 3000,
        timerProgressBar: true,
      });
      @endif
    </script>
</body>
<!--end body-->

</html>