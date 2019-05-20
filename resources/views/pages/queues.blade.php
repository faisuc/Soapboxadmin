@extends('layouts.master')
@section('title', 'Queues')

@section('dashboardContent')
    <div class="row">
        <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">
            <div class="row">
                <div class="col-md-3">
                    <a href="/post/add{{ Request::route('user_id') ? '/' . Request::route('user_id') : '' }}" class="btn btn-primary">NEW POST</a>
                </div>
                @if (is_admin() || is_accountManager())
                    <div class="col-md-5 text-right">
                        <b>Select Client: </b>
                    </div>
                    <div class="col-md-4">
                        <select class="form-control" name="client" onchange="(window.location = '/queues/' + this.options[this.selectedIndex].value);">
                            <option value="">My Posts</option>
                            @foreach ($managedClients as $client)
                                <option {{ Request::route('user_id') && Request::route('user_id') == $client->id ? 'selected' : '' }} value="{{ $client->id }}">{{ $client->fullname }}</option>
                            @endforeach
                        </select>
                    </div>
                @endif
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
                @forelse ($posts as $post)
                    <div class="col-md-3 queue-{{ $post->id }}">
                        <div class="card" style="border: 5px solid
                            @if ($post->status == 0)
                                #f44336;
                            @elseif ($post->status == 1)
                                #ff9800;
                            @elseif ($post->status == 2)
                                #4caf50;
                            @elseif ($post->status == 3)
                                #FFFF00;
                            @elseif ($post->status == 4)
                                #0000FF;
                            @endif
                        ">
                            <img height="300px" class="card-img-top" src="{{ $post->featured_image }}" alt="Card image cap">
                            <div class="card-body">
                                <p class="card-text">
                                    <h3>{{ $post->title }}</h3>
                                    <div class="tools">
                                        <span><i class="fa fa-clock-o"></i> {{ $post->schedule_to_post_date }}
                                    </div>
                                    {{ $post->description }}
                                </p>
                                <a href="/fb_publish_post/{{ $post->id }}" class="btn btn-lg"><i class="fab fa-facebook"></i></a>
                                <a href="#" class="btn btn-lg"><i class="fab fa-twitter"></i></a>
                            </div>
                            <div class="card-footer">
                                <div class="btn-group">
                                    <a href="#" data-post-id="{{ $post->id }}" class="btn" data-toggle="modal" data-target=".postnotes-modal"><i class="fas fa-sticky-note"></i></a>
                                    <a href="/post/edit/{{ $post->id }}" class="btn"><i class="fas fa-edit"></i></a>
                                    <a href="/post/delete/{{ $post->id }}" class="btn confirmDeleteButton"><i class="fas fa-trash-alt"></i></a>
                                </div>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-md-12">
                        <h3>No Posts.</h3>
                    </div>
                @endforelse
            </div>
        </div>
    </div>

    @include('modals.post_notes')

@endsection