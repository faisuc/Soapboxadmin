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
                            <label for="inputStatus">Payment Status</label>                            
                            <select id="inputStatus" name="payment_status" class="form-control user">                            
                                <option value="1">Waiting Payment</option>
                                <option value="2">Active</option>
                                <option value="3">Cancelled</option>
                                <option value="4">On Hold</option>
                            </select>
                        </div>

                        <?php /*
                        Active, Waiting Payment, Cancelled, On Hold
                        <div class="row">
                            <div class="col-xl-6 col-lg-6 col-md-6 col-sm-6">
                                <label for="cellemail">Email</label>
                                <div class="form-group">
                                    <input id="cellemail" type="text" placeholder="Email" value="{{ old('title') }}" name="cellemail1" class="form-control">
                                </div>
                            </div>                            
                            <div class="col-xl-6 col-lg-6 col-md-6 col-sm-6">
                                <label for="cllemail">&nbsp;</label>
                                <div class="form-group">
                                    <select id="inputStatus" name="ownerstatus" class="form-control user">
                                        <option value=''>select user</option>
                                        <option value="1">Owner</option>
                                        <!-- <option value="2">Marketer</option>
                                        <option value="3">Clients</option> -->
                                    </select>
                                </div>
                            </div>
                        </div> */?>
                                                   
                        <div class="form-group">
                            <input type="submit" value="SAVE" class="btn btn-primary">                            
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <script type="text/javascript">
        
        $(document).ready(function(){
            
            $('#ownerEmail,#marketerEmail,#clientEmail').tagsinput();

            $('.user').on('change', function(e){
                if($(this).val() == '1') {
                    $('.generate').show();
                }else{
                    $('.generate').hide();
                }
            });
        });
    </script>
@endsection