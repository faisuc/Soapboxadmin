<!doctype html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <title>Soapbox - @yield('title')</title>
        <!-- Bootstrap CSS -->
        <link rel="stylesheet" href="{{ asset('assets/vendor/bootstrap/css/bootstrap.min.css') }}">
        <link rel="stylesheet" href="{{ asset('assets/vendor/fonts/circular-std/style.css') }}">
        <link rel="stylesheet" href="{{ asset('assets/libs/css/style.css') }}">
        <link rel="stylesheet" href="{{ asset('assets/vendor/fonts/fontawesome/css/fontawesome-all.css') }}">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.6-rc.0/css/select2.min.css">
        @yield('styles')
    </head>
    <body>

        @hasSection('dashboardContent')

            <div class="dashboard-main-wrapper">

                @include('layouts.navbar')
                @include('layouts.left-sidebar')

                <div class="dashboard-wrapper">

                    <div class="container-fluid dashboard-content">
                        @yield('dashboardContent')
                    </div>

                    @include('layouts.footer')

                </div>

            </div>

        @endif

        @hasSection('authContent')

            @yield('authContent')

        @endif

        <script src="{{ asset('assets/vendor/jquery/jquery-3.3.1.min.js') }}"></script>
        <script src="{{ asset('assets/vendor/bootstrap/js/bootstrap.bundle.js') }}"></script>
        <script src="{{ asset('assets/vendor/slimscroll/jquery.slimscroll.js') }}"></script>
        <script src="{{ asset('assets/libs/js/main-js.js') }}"></script>
        <script src="{{ asset('assets/libs/js/script.js') }}"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.6-rc.0/js/select2.min.js"></script>
    </body>
</html>