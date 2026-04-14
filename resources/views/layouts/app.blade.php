<!DOCTYPE html>
<html lang="en">

<head>
  @include('partials.header')
  @stack('styles')
</head>

<body>
  <div class="container-scroller">
    @include('partials.navbar')

    <div class="container-fluid page-body-wrapper">
      @include('partials.sidebar')

      <div class="main-panel">
        <div class="content-wrapper">
          @yield('content')
        </div>

        @include('partials.footer')
      </div>
    </div>
  </div>

  <script src="{{ asset('assets/vendors/js/vendor.bundle.base.js') }}"></script>
  <script src="{{ asset('assets/js/off-canvas.js') }}"></script>
  <script src="{{ asset('assets/js/misc.js') }}"></script>
  <script>
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });
</script>

  @stack('page-scripts')
</body>

</html>