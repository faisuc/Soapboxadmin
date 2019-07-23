<!doctype html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <title>Social Hat - @yield('title')</title>
        <!-- Bootstrap CSS -->
        <!--<link rel="stylesheet" href="{{ asset('assets/vendor/bootstrap/css/bootstrap.min.css') }}">-->
        <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
        <link rel="stylesheet" href="{{ asset('assets/vendor/fonts/circular-std/style.css') }}">
        <link rel="stylesheet" href="{{ asset('assets/libs/css/style.css') }}">
        <link rel="stylesheet" href="{{ asset('assets/vendor/fonts/fontawesome/css/fontawesome-all.css') }}">
        <link rel="stylesheet" href="{{ asset('assets/libs/css/bootstrap-datetimepicker.min.css') }}">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.6-rc.0/css/select2.min.css">
        @if (Sentinel::check())
            <meta name="active_user" content="{{ Sentinel::getUser()->id }}">
        @endif
        @yield('styles')
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
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

        <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js" integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1" crossorigin="anonymous"></script>
        <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js" integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous"></script>
        <script src="{{ asset('assets/vendor/bootstrap/js/bootstrap.bundle.js') }}"></script>
        <script src="{{ asset('assets/vendor/slimscroll/jquery.slimscroll.js') }}"></script>
        <script src="{{ asset('assets/libs/js/main-js.js') }}"></script>
        <script src="{{ asset('assets/libs/js/script.js') }}"></script>
        <script src="{{ asset('assets/libs/js/bootstrap-datetimepicker.min.js') }}"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.6-rc.0/js/select2.min.js"></script>
        @yield('js')
    </body>
</html>