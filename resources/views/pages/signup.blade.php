<!doctype html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <title>Social Hat - Signup</title>
        <!-- Bootstrap CSS -->
        <!--<link rel="stylesheet" href="{{ asset('assets/vendor/bootstrap/css/bootstrap.min.css') }}">-->
        <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
        <link rel="stylesheet" href="{{ asset('assets/vendor/fonts/circular-std/style.css') }}">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-tagsinput/0.8.0/bootstrap-tagsinput.css">
        <link rel="stylesheet" href="{{ asset('assets/libs/css/style.css') }}">
        <link rel="stylesheet" href="{{ asset('assets/vendor/fonts/fontawesome/css/fontawesome-all.css') }}">
        <link rel="stylesheet" href="{{ asset('assets/libs/css/bootstrap-datetimepicker.min.css') }}">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.6-rc.0/css/select2.min.css">
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
    </head>
    <body>

        
        <div class="dashboard-main-wrapper">

            <div class="container">

                <div class="row">
                    <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">
                        
                        <div class="logo text-center pb-100">
                            <img class="logo-img" src="{{ asset('assets/images/logo_hat.png') }}" alt="logo" width="250px">
                        </div>

                        <div class="card">
                            <div class="card-header">
                                <h1>Sign Up</h1>
                            </div>

                            
                            <div class="card-body">
                                <form action="/guestuser/create" method="post" enctype="multipart/form-data">
                                    
                                    @csrf
                                    @if ($errors->any())
                                        @foreach ($errors->all() as $error)
                                            <div class="alert alert-danger">
                                                {{ $error }}
                                            </div>
                                        @endforeach
                                    @endif

                                    @if (session()->has('flash_message'))
                                        <div class="alert alert-success">
                                            {{ session()->get('flash_message') }}
                                        </div>
                                    @endif
                                    
                                    <div class="form-group">
                                        <label for="inputFirstName">First Name</label>
                                        <input id="inputFirstName" type="text" placeholder="First Name" name="first_name" class="form-control">
                                    </div>
                                    <div class="form-group">
                                        <label for="inputLastName">Last Name</label>
                                        <input id="inputLastName" type="text" placeholder="Last Name" name="last_name" class="form-control">
                                    </div>
                                    <div class="form-group">
                                        <label for="inputEmail">Email address</label>
                                        <input id="inputEmail" type="email" placeholder="name@example.com" name="email" class="form-control">
                                    </div>
                                    <div class="form-group">
                                        <label for="inputPhone">Phone Number</label>
                                        <input id="inputPhone" type="text" placeholder="Phone Number" name="phone" class="form-control">
                                    </div>
                                    <div class="form-group">
                                        <label for="inputPassword">Password</label>
                                        <input id="inputPassword" type="password" placeholder="Password" name="password" class="form-control">
                                    </div>
                                    <div class="form-group">
                                        <input type="hidden" name="roles" value="client">
                                        <input type="submit" value="SAVE" class="btn btn-primary">
                                    </div>

                                </form>
                            </div>
                        </div>

                    </div>
                </div>

            </div>

        </div>

        <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js" integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1" crossorigin="anonymous"></script>
        <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js" integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous"></script>
        <script src="{{ asset('assets/vendor/bootstrap/js/bootstrap.bundle.js') }}"></script>
        <script src="{{ asset('assets/vendor/slimscroll/jquery.slimscroll.js') }}"></script>
        <script src="{{ asset('assets/libs/js/main-js.js') }}"></script>
        <script src="{{ asset('assets/libs/js/script.js') }}"></script>
        <script src="{{ asset('assets/libs/js/bootstrap-datetimepicker.min.js') }}"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.6-rc.0/js/select2.min.js"></script>
        <!-- <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-tagsinput/0.8.0/bootstrap-tagsinput.js"></script> -->
        <script src="https://cdnjs.cloudflare.com/ajax/libs/typeahead.js/0.11.1/typeahead.bundle.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-tagsinput/0.8.0/bootstrap-tagsinput.min.js"></script>
        <!-- <script src="https://cdnjs.cloudflare.com/ajax/libs/typeahead.js/0.11.1/typeahead.bundle.min.js"></script> -->
        <script src="https://cdnjs.cloudflare.com/ajax/libs/angular.js/1.2.20/angular.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-tagsinput/0.8.0/bootstrap-tagsinput-angular.min.js"></script>
        @yield('js')
    </body>
</html>