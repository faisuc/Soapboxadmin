@extends('layouts.master')
@section('title', 'Create New Social Cell')

@section('dashboardContent')
    <div class="row">
        <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">
            <div class="section-block" id="basicform">
                <h3 class="section-title">Create New Social Cell</h3>
            </div>
            <div class="card">
                <div class="card-body">
                    <!-- <form action="{{ url('/post/store') }}" method="post" enctype="multipart/form-data"> -->
                    <form action="{{ url('/socialcell/store') }}" method="post" enctype="multipart/form-data">
                        @csrf
                        @if (Request::route('user_id'))
                            <input type="hidden" name="user_id" value="{{ Request::route('user_id') }}">
                        @endif

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
                            <label for="cellName">Cell Name</label>
                            <!-- <input id="inputTitle" type="text" placeholder="Title" value="{{ old('title') }}" name="title" class="form-control"> -->
                            <input id="cellName" type="text" placeholder="Cell Name" value="{{ old('cellname') }}" name="cellname" class="form-control" >
                        </div>

                        <div class="form-group">
                            <label for="ownerEmail">Owner Email</label>
                            <input id="ownerEmail" type="text" value="{{ old('email_owner') }}" name="email_owner" class="form-control" data-role="tagsinput">
                        </div>

                        <div class="form-group">
                            <label for="marketerEmail">Marketer Email</label>
                            <input id="marketerEmail" type="text" value="{{ old('email_marketer') }}" name="email_marketer" class="form-control" data-role="tagsinput">
                        </div>

                        <div class="form-group">
                            <label for="clientEmail">Client Email</label>
                            <input id="clientEmail" type="text" value="{{ old('email_client') }}" name="email_client" class="form-control" data-role="tagsinput">
                        </div>
                        <div class="form-group">
                            <label for="post_status" class="custom-control custom-checkbox">
                                <input class="custom-control-input" id="post_status" type="checkbox" name="post_status" value="1"><span class="custom-control-label">Post Status Set to Approve</span>
                            </label>
                        </div>

                        <div class="form-group">
                            <input type="submit" value="SAVE" class="btn btn-primary">
                            <input type="submit" name="payment" value="Generate Payment" class="btn btn-primary generate" style="display: none;">
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <script type="text/javascript">
        $(document).ready(function(){
            
            $('#ownerEmail,#marketerEmail,#clientEmail').tagsinput();

            /*$('#inputStatus').on('change', function(e){
                if($(this).val() == '2') {
                    $('.generate').show();
                }else{
                    $('.generate').hide();
                }
            });*/

            $('#ownerEmail').on('change',function() {
                if($(this).val() != '') {
                    $('.generate').show();
                }
                else {
                    $('.generate').hide();
                }
            });

        });
    </script>
@endsection