@extends('layouts.master')
@section('title', 'Social Cells')

@section('dashboardContent')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/css/bootstrap-datepicker.css" />
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/js/bootstrap-datepicker.min.js"></script>

    <div class="row">
        <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">
            <div class="row">
                <div class="col-md-3">
                    <a href="{{ url('/socialcell/add') }}" class="btn btn-primary">Create Social Cell</a>
                </div>
                <div class="col-md-2"></div>
                <div class="col-md-1 text-right">
                    <b>Select Date: </b>
                </div>
                <div class="col-md-3">
                    <input type="text" placehoder="Start Date" id="startdate" value="{{ (isset($start_date)) ? $start_date : '' }}" />
                    <input type="text" placehoder="End Date" id="enddate" value="{{ (isset($end_date)) ? $end_date : '' }}" />
                </div>
                <div class="col-md-1 text-right">
                    <b>Select Status: </b>
                </div>
                <div class="col-md-2">
                    <select class="form-control" name="socialcell" onchange="(window.location = '/socialcell/status/' + this.options[this.selectedIndex].value);">
                        <option value="all">Active & Waiting Payment</option>
                        @foreach ($statuses as $status_key => $status)
                            <option {{ (isset($status_id) && $status_id == $status_key) ? 'selected' : '' }} value="{{ $status_key }}">{{ $status }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <hr />
            <div class="row">
                <div class="col-md-12">
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
                </div>
            </div>

            <div class="row">
                @forelse ($socialcells as $socialcell)
                    <div class="col-md-3">
                        <div class="card" style="border: 2px solid #ccc;" >
                            <div class="card-body">
                                <h3>{{ $socialcell->cell_name }}</h3>
                                <div class="tools">
                                    <span><i class="fa fa-clock"></i> {{ $socialcell->created_at }} </span>
                                </div>
                                <p class="card-text">Owner Mail : {{ $socialcell->email_owner ? $socialcell->email_owner: 'N/A'}} </p>
                                <p class="card-text">Marketer Mail : {{ $socialcell->email_marketer ? $socialcell->email_marketer : 'N/A' }} </p>
                                <p class="card-text">Client Mail : {{ $socialcell->email_client ? $socialcell->email_client : 'N/A' }} </p>
                                <p class="card-text">Payment Status : {{ paymentStatus($socialcell->payment_status) }}</p>
                                <?php
                                $owner_emails = explode(',', $socialcell->email_owner);
                                ?>
                                @if(in_array($user_email, $owner_emails))
                                    @if($socialcell->payment_status != '3')
                                    <a href="{{ url('socialcell/cancel_payment/'.$socialcell->id) }}" class="btn btn-info">Cancel Payment</a>
                                    @endif
                                    @if($socialcell->payment_status != '4' && $socialcell->payment_status != '3')
                                    <a href="{{ url('socialcell/onhold_payment/'.$socialcell->id) }}" class="btn btn-info">On Hold Payment</a>
                                    @endif
                                    @if($socialcell->payment_status == '4')
                                    <a href="{{ url('socialcell/active_payment/'.$socialcell->id) }}" class="btn btn-info">Active Payment</a>
                                    @endif
                                @endif
                            </div>
                            <div class="card-footer">
                                <div class="btn-group">
                                    <a href="/socialcell/edit/{{ $socialcell->id }}" class="btn"><i class="fas fa-edit"></i></a>
                                    <a href="/socialcell/delete/{{ $socialcell->id }}" class="btn confirmDeleteButton"><i class="fas fa-trash-alt"></i></a>
                                    <a href="/socialcell/{{ $socialcell->id }}" class="btn btn-info float-right">View</a>
                                    <!-- <input type="button" value="Generate" class="btn btn-info generate" > -->
                                </div>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-md-12">
                        <h3>No Social Cells.</h3>
                    </div>
                @endforelse    
            </div>
        </div>
    </div>

<script type="text/javascript">
$(document).ready(function(){
    var date = new Date();
    var today = new Date(date.getFullYear(), date.getMonth(), date.getDate());
    $("#enddate").datepicker({
        format: 'yyyy-mm-dd',
        autoclose: true,
        endDate: today
    });
    $("#startdate").datepicker({
        todayBtn:  1,
        format: 'yyyy-mm-dd',
        autoclose: true,
        endDate: today
    }).on('changeDate', function (selected) {
        var minDate = new Date(selected.date.valueOf());
        $('#enddate').datepicker('setStartDate', minDate);
    });
    $("#startdate,#enddate").on('change',function() {
        let startdate = $('#startdate').val();
        let enddate = $('#enddate').val();
        if(startdate != '' && enddate != '') {
            window.location.href = '/socialcell/date/'+startdate+'/'+enddate;
        }
    });
});
</script>
@endsection