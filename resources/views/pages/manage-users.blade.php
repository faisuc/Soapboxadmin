@extends('layouts.master')
@section('title', 'Manage Users')

@section('dashboardContent')
    <div class="row">
        <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">
                <div class="card">
                        <div class="d-flex justify-content-between card-header">
                            <div>
                                <h5>Manage Users</h5>
                            </div>
                            <div>
                                <a href="/user/create" class="btn btn-outline-primary text-right">NEW USER</a>
                            </div>
                        </div>
                        <div class="card-body">
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
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th scope="col">Email</th>
                                        <th scope="col">First Name</th>
                                        <th scope="col">Last Name</th>
                                        <th scope="col">Company</th>
                                        <th scope="col">Timezone</th>
                                        <th scope="col">Roles</th>
                                        <th scope="col">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($users as $user)
                                        <tr>
                                            <td>{{ $user->email }}</td>
                                            <td>{{ $user->first_name }}</td>
                                            <td>{{ $user->last_name }}</td>
                                            <td>{{ $user->company_name }}</td>
                                            <td>{{ $user->timezone }}</td>
                                            <td>
                                                @foreach ($user->roles as $role)
                                                    {{ $role->name }}
                                                @endforeach
                                            </td>
                                            <td>
                                                @if (canManageClients($user->id))
                                                    <a href="/user/clients/{{ $user->id }}"><i class="fas fa-users"></i></a>
                                                @endif
                                                <a href="/user/edit/{{ $user->id }}"><i class="fas fa-edit"></i></a>
                                                <a class="confirmDeleteButton" href="/user/delete/{{ $user->id }}"><i class="far fa-trash-alt"></i></a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                </div>
        </div>
    </div>
@endsection