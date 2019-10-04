@extends('layouts.master')
@section('title', 'Social Cells')

@section('dashboardContent')
    <div class="row">
        <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">
            <div class="row">
                <div class="col-md-3">
                    <a href="{{ url('/socialcell/add') }}" class="btn btn-primary">Create Social Cell</a>
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
                        <div class="card" style="border: 5px solid;" >
                            <div class="card-body" style="height: 400px; overflow: auto;">
                                <p class="card-text">
                                    <h3>{{ $socialcell->cell_name }}</h3>
                                    <div class="tools">
                                        <span><i class="fa fa-clock"></i> {{ $socialcell->created_at }} </span>
                                    </div>
                                </p>
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
@endsection