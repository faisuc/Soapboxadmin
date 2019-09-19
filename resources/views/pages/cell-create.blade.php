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
                            <input id="cellName" type="text" placeholder="Cell Name" value="{{ old('title') }}" name="cellname" class="form-control">
                        </div>

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
                        </div>

                        <div class="row">
                            <div class="col-xl-6 col-lg-6 col-md-6 col-sm-6">                                
                                <div class="form-group">
                                    <input id="cellemail" type="text" placeholder="Email" value="{{ old('title') }}" name="cellemail2" class="form-control">
                                </div>
                            </div>                            
                            <div class="col-xl-6 col-lg-6 col-md-6 col-sm-6">                                
                                <div class="form-group">
                                    <select id="inputStatus" name="marketerstatus" class="form-control user">
                                        <option value=''>select user</option>
                                        <option value="2">Marketer</option>
                                        <!-- <option value="1">Owner</option>
                                        <option value="3">Clients</option>                                 -->
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-xl-6 col-lg-6 col-md-6 col-sm-6">
                                <div class="form-group">
                                    <input id="cellemail" type="text" placeholder="Email" value="{{ old('title') }}" name="cellemail3" class="form-control">
                                </div>
                            </div>                            
                            <div class="col-xl-6 col-lg-6 col-md-6 col-sm-6">
                                <div class="form-group">
                                    <select id="inputStatus" name="clientstatus" class="form-control user">
                                        <option value=''>select user</option>
                                        <!-- <option value="1">Owner</option>
                                        <option value="2">Marketer</option> -->
                                        <option value="3">Clients</option>                                
                                    </select>
                                </div>
                            </div>
                        </div>

                        <!-- <div class="form-group">
                            <label for="inputTextContent">Text Content</label>
                            <textarea id="inputTextContent" type="text" placeholder="Text Content" name="description" class="form-control">{{ old('description') }}</textarea>
                        </div>
                        <div class="form-group">
                            <label for="inputURL">URL/Link</label>
                            <input id="inputURL" type="text" placeholder="URL/Link" value="{{ old('link') }}" name="link" class="form-control">
                        </div>
                        <div class="form-group">
                            <label for="inputScheduleDate">Schedule Post</label>
                            <input id="inputScheduleDate" readonly type="text" placeholder="Date & Time" value="{{ old('schedule_date') }}" name="schedule_date" class="form-control datetimepicker" required>
                        </div> -->
                            
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