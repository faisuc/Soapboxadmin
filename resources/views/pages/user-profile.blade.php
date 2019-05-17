@extends('layouts.master')
@section('title', 'My Profile')

@section('dashboardContent')
    <div class="row">
        <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">
            <div class="section-block" id="basicform">
                <h3 class="section-title">My Profile</h3>
            </div>
            <div class="card">
                <div class="card-body">
                    <form action="/profile/update" method="post" enctype="multipart/form-data">
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
                            <label for="inputEmail">Email address</label>
                            <input id="inputEmail" readonly type="email" placeholder="name@example.com" value="{{ $activeUser->email }}" name="email" class="form-control">
                            <p>We'll never share your email with anyone else.</p>
                        </div>
                        <div class="form-group">
                            <label for="inputFirstName">First Name</label>
                            <input id="inputFirstName" type="text" placeholder="First Name" value="{{ old('first_name') ? old('first_name') : $activeUser->first_name }}" name="first_name" class="form-control">
                        </div>
                        <div class="form-group">
                            <label for="inputLastName">Last Name</label>
                            <input id="inputLastName" type="text" placeholder="Last Name" value="{{ old('last_name') ? old('last_name') : $activeUser->last_name }}" name="last_name" class="form-control">
                        </div>
                        <div class="form-group">
                            <label for="inputCompanyName">Company Name</label>
                            <input id="inputCompanyName" type="text" placeholder="Company Name" value="{{ old('company_name') ? old('company_name') : $activeUser->company_name }}" name="company_name" class="form-control">
                        </div>
                        <div class="form-group">
                            <label for="inputSelectTimezone">Timezone</label>
                            <select class="form-control" id="inputSelectTimezone" name="timezone">
                                <option value="">Select Timezone</option>
                                @foreach ($timezones as $timezone)
                                    <option value="{{ $timezone }}" {{ (old('timezone') ? old('timezone') : $activeUser->timezone) == $timezone ? "selected" : "" }}>{{ $timezone }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="inputPassword">Password</label>
                            <input id="inputPassword" type="password" placeholder="Password" name="password" class="form-control">
                            <p>Leave the password field blank if you don't want to update it.</p>
                        </div>
                        <div class="form-group">
                            <label for="inputConfirmPassword">Confirm Password</label>
                            <input id="inputConfirmPassword" type="password" placeholder="Confirm Password" name="password_confirmation" class="form-control">
                        </div>
                        <div class="custom-file mb-3">
                            <label for="inputProfilePhoto">Profile Photo</label>
                            <input type="file" id="inputProfilePhoto" name="profilephoto">
                        </div>
                        <div class="form-group">
                            <img src="{{ $activeUser->profilephoto }}" class="img-responsive" style="width: 200px;">
                        </div>
                        <div class="form-group">
                            <input type="submit" value="SAVE" class="btn btn-primary">
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection