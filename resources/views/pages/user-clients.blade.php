@extends('layouts.master')
@section('title', 'Manage Clients')

@section('dashboardContent')
    <div class="row">
        <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">
                <div class="card">
                        <div class="d-flex justify-content-between card-header">
                            <div>
                                <h5>Clients of {{ $routeUser->fullname }}</h5>
                            </div>
                            <div>
                                <a href="/user/create" class="btn btn-outline-primary text-right">NEW CLIENT</a>
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
                                    @foreach ($managedClients as $client)
                                        <tr>
                                            <td>{{ $client->email }}</td>
                                            <td>{{ $client->first_name }}</td>
                                            <td>{{ $client->last_name }}</td>
                                            <td>{{ $client->company_name }}</td>
                                            <td>{{ $client->timezone }}</td>
                                            <td>
                                                @foreach ($client->roles as $role)
                                                    {{ $role->name }}
                                                @endforeach
                                            </td>
                                            <td>
                                                <a href="/user/edit/{{ $client->id }}"><i class="fas fa-edit"></i></a>
                                            <a class="confirmDeleteButton" href="/user/{{ $routeUser->id }}/delete/client/{{ $client->id }}"><i class="far fa-trash-alt"></i></a>
                                            </td>
                                        </tr>
                                    @endforeach
                                    @if (count($managedClients) == 0)
                                        <tr>
                                            <td colspan="7">No clients found.</td>
                                        </tr>
                                    @endif
                                </tbody>
                            </table>
                        </div>
                </div>
        </div>
    </div>
@endsection