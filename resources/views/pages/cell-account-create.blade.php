@extends('layouts.master')
@section('title', 'Create New Social Cell')

@section('dashboardContent')
<div class="row">
    <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">
        <div class="section-block" id="basicform">
            <h3 class="section-title">{{ ucwords($social_cell->cell_name) }} Accounts</h3>
        </div>

        <div class="row">
            <div class="col-md-3">
                <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#socialAccountModal">Connect Social Account</button>
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
            <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">
                <div class="card">
                    <h5 class="card-header">Social Accounts</h5>
                    <div class="card-body">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th scope="col">#</th>
                                    <th scope="col">Social Network</th>
                                    <th scope="col">Name</th>
                                    <th scope="col">URL</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($social_accounts as $ac_key => $social)
                                    <tr>
                                        <th scope="row">{{ $social->id }}</th>
                                        <td>{{ convertSocialType($social->type_id) }}</td>
                                        <td>{{ $social->url }}</td>
                                        <td><a href="{{ url('/socialaccount/delete/'.$social->id.' ') }}" role="button" class="btn btn-danger">Unlink</a></td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4">No Accounts</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        
        
    </div>
</div>

<!-- Social Account Modal -->
<div class="modal fade" id="socialAccountModal" tabindex="-1" role="dialog" aria-labelledby="socialAccountModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="socialAccountModalLabel">Social Account</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="{{ url('/socialcellaccount/add') }}" method="post">
                @csrf
                @if (Request::route('user_id'))
                    <input type="hidden" name="user_id" value="{{ Request::route('user_id') }}">
                @endif
                @if (isset($social_cell))
                    <input type="hidden" name="social_cell_id" value="{{ $social_cell->id }}">
                @endif
                <div class="modal-body">
                    <div class="form-group">
                        <label for="inputSocialAccount" class="col-form-label">Social Account:</label>
                        <select class="form-control" name="social_account" style="width: 100%;">
                            <option value="1">Facebook Page</option>
                            <option value="2">Facebook Group</option>
                            <option value="3">Twitter</option>
                            <option value="4">Google Business</option>
                            <option value="5">Instagram</option>
                            <option value="6">Pinterest</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="inputName" class="col-form-label">Name:</label>
                        <input type="text" class="form-control" id="inputName" name="name">
                    </div>
                    <div class="form-group">
                        <label for="inputURL" class="col-form-label">URL:</label>
                        <input type="text" class="form-control" id="inputURL" name="url">
                    </div>
                    <div id="instagram-acc" style="display: none;">
                        <div class="form-group">
                            <label for="inputInstaUsername" class="col-form-label">Instagram Username:</label>
                            <input type="text" class="form-control" id="inputInstaUsername" name="insta_username">
                        </div>
                        <div class="form-group">
                            <label for="inputInstaUser" class="col-form-label">Instagram Email:</label>
                            <input type="text" class="form-control" id="inputInstaUser" name="insta_user">
                        </div>
                        <div class="form-group">
                            <label for="inputInstaPass" class="col-form-label">Instagram Password:</label>
                            <!-- <input type="text" class="form-control" id="inputInstaPass" name="insta_pass" required> -->
                            <div class="input-group">
                                <input type="password" class="form-control" id="inputInstaPass" name="insta_pass">
                                <span class="input-group-btn">
                                    <button class="btn btn-default reveal" type="button"><i class="glyphicon glyphicon-eye-open"></i></button>
                                </span>          
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Save</button>
                </div>
            </form>
        </div>
    </div>
</div>
<!-- Social Account Modal -->

<script type="text/javascript">    
$(document).ready(function(){
});
</script>
@endsection