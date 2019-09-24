@extends('layouts.master')
@section('title', 'Create New Social Cell')

@section('dashboardContent')
    <div class="row">
        <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">
            <div class="card">
                <div class="card-header">
                    <h3>Stripe Payment For {{ $cell_name }}</h3>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4">
                            <form class="form-horizontal" method="POST" id="payment-form" role="form" action="{{ url('/create_payment/'.$cell_id) }}">
                                {{ csrf_field() }}

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

                                <div class="form-row">
                                    <div class="col-xs-12 col-md-12 form-group card required">
                                        <label class="control-label">Card Number</label>
                                        <input autocomplete="off" class="form-control card-number" maxlength="16" type="text" name="card_no">
                                    </div>
                                </div>
                                <div class="form-row">
                                    <div class="col-xs-4 col-md-4 form-group cvc required">
                                        <label class="control-label">CVV</label>
                                        <input autocomplete="off" class="form-control card-cvc" placeholder="CVV" maxlength="4" type="text" name="cvvNumber">
                                    </div>
                                    <div class="col-xs-4 col-md-4 form-group expiration required">
                                        <label class="control-label">Expiration</label>
                                        <?php
                                        $monthArr = array('January','February','March','April','May','June','July','August','September','October','November','December');
                                        ?>
                                        <select name="ccExpiryMonth" class="form-control card-expiry-month">
                                            <option value="">Month</option>
                                            @foreach ($monthArr as $month_key => $month)
                                            <option value="{{ $month_key + 1 }}">{{ $month }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-xs-4 col-md-4 form-group expiration required">
                                        <label class="control-label">Year</label>
                                        <?php
                                        $year = date('Y');
                                        ?>
                                        <select name="ccExpiryYear" class="form-control card-expiry-year">
                                            <option value="">Year</option>
                                            @for($i=$year;$i>=50;$i--)
                                            <option value="{{ $i }}">{{ $i }}</option>
                                            @endfor
                                        </select>
                                    </div>
                                </div>
                                <div class="form-row">
                                    <div class="col-md-6">
                                        <div class="form-control total btn btn-primary">
                                            Total:
                                            <span class="amount">$1</span>
                                            <input type="hidden" name="amount" value="1">
                                            <input type="hidden" name="cell_id" value="{{ $cell_id }}">
                                        </div>
                                    </div>
                                    <div class="col-md-6 form-group">
                                        <button class="form-control btn btn-success submit-button" type="submit">Pay »</button>
                                    </div>
                                </div>
                                <div class="form-row">
                                </div>
                                <div class="form-row">
                                    <div class="col-md-12 error form-group d-none">
                                        <div class="alert-danger alert">
                                            Please correct the errors and try again.
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection