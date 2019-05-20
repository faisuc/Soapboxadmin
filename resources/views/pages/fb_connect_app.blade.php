@extends('layouts.master')
@section('title', 'FB Connect App')

@section('dashboardContent')

    <!-- <link rel="stylesheet" href="https://unpkg.com/bootstrap-material-design@4.1.1/dist/css/bootstrap-material-design.min.css">
    <link rel="stylesheet" type="text/css" href="https://fonts.googleapis.com/css?family=Roboto:300,400,500,700|Roboto+Slab:400,700|Material+Icons">
    <link rel="stylesgeet" href="https://rawgit.com/creativetimofficial/material-kit/master/assets/css/material-kit.css"> -->

    <div class="row">
        <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">
            <div class="section-block" id="basicform">
                <h3 class="section-title">FB Connect App</h3>
            </div>
            <div class="card">
                <div class="card-body">
                    <form action="/fb_publish_post" method="post" enctype="multipart/form-data">
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
                            <label for="inputMessage">Message</label>
                            <textarea id="inputMessage" placeholder="Message For Schedule Post" name="message" class="form-control"></textarea>
                        </div>

                        <div class="row">
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label>Set Time</label>
                                    <input type="text" name="timestamp" placeholder="Set Schedule Time" class="form-control datetimepicker">
                                </div>
                            </div>
                        </div>

                        
                        <div class="form-group">
                            <input type="submit" value="Create Schedule Post" class="btn btn-primary">
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>


    <!-- <script src="https://rawgit.com/creativetimofficial/material-kit/master/assets/js/core/bootstrap-material-design.min.js"></script> -->
    <script src="https://rawgit.com/creativetimofficial/material-kit/master/assets/js/plugins/moment.min.js"></script>
    <script src="https://rawgit.com/creativetimofficial/material-kit/master/assets/js/plugins/bootstrap-datetimepicker.js"></script>
    <!-- <script src="https://rawgit.com/creativetimofficial/material-kit/master/assets/js/material-kit.js"></script> -->
    <script type="text/javascript">
    $(document).ready(function() {
        console.log('asd');
    });
    </script>

@endsection