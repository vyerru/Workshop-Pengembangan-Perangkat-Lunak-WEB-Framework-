<!DOCTYPE html>
<html lang="en">
<head>
    @include('partials.header') @stack('style-page')
</head>
<body>
    <div class="container-scroller">
        <div class="container-fluid page-body-wrapper full-page-wrapper">
            <div class="content-wrapper d-flex align-items-center auth">
                @yield('content') </div>
        </div>
    </div>
    @stack('page-scripts')
</body>
</html>
