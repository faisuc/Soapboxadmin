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
                            <input id="cellName" type="text" placeholder="Cell Name" value="{{ $socialcell->cell_name }}" name="cellname" readonly class="form-control">
                        </div>

                        <div class="form-group">
                            <label for="ownerEmail">Owner Email</label>
                            <!-- <input id="inputTitle" type="text" placeholder="Title" value="{{ old('title') }}" name="title" class="form-control"> -->
                            <input id="ownerEmail" type="text" value="{{ $socialcell->email_owner }}" name="email_owner" class="form-control"  data-role="tagsinput">
                        </div>

                        <div class="form-group">
                            <label for="marketerEmail">Marketer Email</label>
                            <!-- <input id="inputTitle" type="text" placeholder="Title" value="{{ old('title') }}" name="title" class="form-control"> -->
                            <input id="marketerEmail" type="text" value="{{ $socialcell->email_marketer }}" name="email_marketer" class="form-control"  data-role="tagsinput">
                        </div>

                        <div class="form-group">
                            <label for="clientEmail">Client Email</label>
                            <!-- <input id="inputTitle" type="text" placeholder="Title" value="{{ old('title') }}" name="title" class="form-control"> -->
                            <input id="clientEmail" type="text" value="{{ $socialcell->email_client }}" name="email_client" class="form-control"  data-role="tagsinput">
                        </div>
                        <div class="form-group">
                            <label for="inputStatus">Payment Status</label>                            
                            <select id="inputStatus" name="payment_status" class="form-control user">                            
                                <option value="1" {{ $socialcell->payment_status == 1 ? 'selected' : '' }}>Waiting Payment</option>
                                <option value="2" {{ $socialcell->payment_status == 2 ? 'selected' : '' }}>Active</option>
                                <option value="3" {{ $socialcell->payment_status == 3 ? 'selected' : '' }}>Cancelled</option>
                                <option value="4" {{ $socialcell->payment_status == 4 ? 'selected' : '' }}>On Hold</option>
                            </select>
                        </div>
                    
                        <div class="form-group">
                            <input type="submit" value="SAVE" class="btn btn-primary">
                            @if($socialcell->payment_status != 2)
                            <input type="submit" name="payment" value="Generate Payment" class="btn btn-primary generate" style="display: none;">
                            @endif
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <script type="text/javascript">
        
        $(document).ready(function(){

            $('#ownerEmail,#marketerEmail,#clientEmail').tagsinput();

            <?php
            if($socialcell->payment_status != 2) {
            ?>
            $('#inputStatus').on('change', function(e){
                if( $('#ownerEmail').val() != '' || $(this).val() == '1') {
                    $('.generate').show();
                }else{
                    $('.generate').hide();
                }
            });

            $('#ownerEmail').on('change',function() {
                if( $('#inputStatus').val() == '1' || $(this).val() != '') {
                    $('.generate').show();
                }
                else {
                    $('.generate').hide();
                }
            });

            setTimeout(function() {
                $('#inputStatus').change();
                $('#ownerEmail').change();
            },1000);
            <?php
            }
            ?>
           
        });
    </script>
@endsection