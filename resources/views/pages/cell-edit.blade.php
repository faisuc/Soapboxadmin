@extends('layouts.master')
@section('title', 'Update Social Cell')

@section('dashboardContent')
    <div class="row">
        <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">
            <div class="section-block" id="basicform">
                <h3 class="section-title">Update Social Cell</h3>
            </div>
            <div class="card">
                <div class="card-body">
                    <!-- <form action="{{ url('/post/store') }}" method="post" enctype="multipart/form-data"> -->
                    <form action="{{ url('/socialcell/update/'.$socialcell->id) }}" method="post" enctype="multipart/form-data">
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
                            <input id="cellName" type="text" placeholder="Cell Name" value="{{ $socialcell->cell_name }}" name="cellname" class="form-control">
                        </div>

                        <div class="form-group">
                            <label for="ownerEmail">Owner Email</label>
                            <!-- <input id="inputTitle" type="text" placeholder="Title" value="{{ old('title') }}" name="title" class="form-control"> -->
                            <input id="ownerEmail" type="text" placeholder="Owner Email" value="{{ $socialcell->email_owner }}" name="email_owner" class="form-control">
                        </div>

                        <div class="form-group">
                            <label for="marketerEmail">Marketer Email</label>
                            <!-- <input id="inputTitle" type="text" placeholder="Title" value="{{ old('title') }}" name="title" class="form-control"> -->
                            <input id="marketerEmail" type="text" placeholder="Marketer Email" value="{{ $socialcell->email_marketer }}" name="email_marketer" class="form-control">
                        </div>

                        <div class="form-group">
                            <label for="clientEmail">Client Email</label>
                            <!-- <input id="inputTitle" type="text" placeholder="Title" value="{{ old('title') }}" name="title" class="form-control"> -->
                            <input id="clientEmail" type="text" placeholder="Client Email" value="{{ $socialcell->email_client }}" name="email_client" class="form-control">
                        </div>
                    

                        <div class="form-group">
                            <input type="submit" value="SAVE" class="btn btn-primary">
                            <input type="button" value="Generate" class="btn btn-info generate" style="display: none">&nbsp;
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <script type="text/javascript">
        
        $(document).ready(function(){

            $('.user').on('change', function(e){
                // $(this).closest('form').submit();
                if($(this).val() == '1') {
                    $('.generate').show();
                }else{
                    $('.generate').hide();
                }
            });
        });
    </script>
@endsection