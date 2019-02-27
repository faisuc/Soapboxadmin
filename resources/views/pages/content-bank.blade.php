@extends('layouts.master')
@section('title', 'Content Bank')

@section('dashboardContent')
    <div class="row">
        <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">
            <div class="row">
                <div class="col-md-3">
                    
                </div>
                @if (is_admin() || is_accountManager())
                    <div class="col-md-5 text-right">
                        <b>Select Client: </b>
                    </div>
                    <div class="col-md-4">
                        <select class="form-control" name="client" onchange="(window.location = '/contentbank/' + this.options[this.selectedIndex].value);">
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
                    <div class="col-3">
                      <div class="nav flex-column nav-pills" id="v-pills-tab" role="tablist" aria-orientation="vertical">
                        <a class="nav-link active" id="v-pills-imagerepo-tab" data-toggle="pill" href="#v-pills-imagerepo" role="tab" aria-controls="v-pills-imagerepo" aria-selected="true">Image Repository</a>
                        <a class="nav-link" id="v-pills-textrepo-tab" data-toggle="pill" href="#v-pills-textrepo" role="tab" aria-controls="v-pills-textrepo" aria-selected="false">Text Repository</a>
                      </div>
                    </div>
                    <div class="col-9">
                      <div class="tab-content" id="v-pills-tabContent">
                        <div class="tab-pane fade show active" id="v-pills-imagerepo" role="tabpanel" aria-labelledby="v-pills-imagerepo-tab">
                                @if (((Request::route('user_id')) && Sentinel::getUser()->id == Request::route('user_id')) || !Request::route('user_id'))
                            <div class="form">
                                    <button type="button" class="btn btn-secondary" data-toggle="modal" data-target="#uploadImageModal">
                                        UPLOAD NEW IMAGE
                                    </button>

                                <div class="modal fade" id="uploadImageModal" tabindex="-1" role="dialog" aria-labelledby="uploadImageModal" aria-hidden="true">
                                        <form action="/content/upload/image" method="post" enctype="multipart/form-data">
                                            @csrf
                                            <div class="modal-dialog" role="document">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                <h5 class="modal-title" id="uploadImageModal">UPLOAD NEW IMAGE</h5>
                                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                    <span aria-hidden="true">&times;</span>
                                                </button>
                                                </div>
                                                <div class="modal-body">
                                                    <input type="file" class="form-control" name="image">
                                                </div>
                                                <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                                                <button type="submit" class="btn btn-primary">Upload</button>
                                                </div>
                                            </div>
                                            </div>
                                        </form>
                                      </div>
                            </div>
                            <hr />
                            @endif
                            @if (count($images) > 0)
                                <div class="row">
                                    @foreach ($images as $image)
                                        <div class="col-md-3">
                                                <div class="card">
                                                    <img class="card-img-top" src="{{ $image->imageurl }}" alt="Card image cap">
                                                    <div class="card-body">
                                                    <div class="card-footer">
                                                        <div class="btn-group">
                                                            <a href="/content/image/delete/{{ $image->id }}" class="btn confirmDeleteButton"><i class="fas fa-trash-alt"></i></a>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                        <div class="tab-pane fade" id="v-pills-textrepo" role="tabpanel" aria-labelledby="v-pills-textrepo-tab">

                                @if (((Request::route('user_id')) && Sentinel::getUser()->id == Request::route('user_id')) || !Request::route('user_id'))
                            <div class="form">
                                <button type="button" class="btn btn-secondary" data-toggle="modal" data-target="#textModal">
                                    ADD NEW POST
                                </button>
                                <div class="modal fade" id="textModal" tabindex="-1" role="dialog" aria-labelledby="uploadImageModal" aria-hidden="true">
                                        <form action="/content/text/add" method="post" enctype="multipart/form-data">
                                            @csrf
                                            <div class="modal-dialog modal-lg" role="document">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                <h5 class="modal-title" id="textModal">ADD NEW POST</h5>
                                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                    <span aria-hidden="true">&times;</span>
                                                </button>
                                                </div>
                                                <div class="modal-body">
                                                    <div class="form-group">
                                                        <label for="exampleInputEmail1">Title</label>
                                                        <input type="text" class="form-control" id="inputTitle" name="title" placeholder="Title">
                                                      </div>
                                                      <div class="form-group">
                                                        <label for="inputContent">Content</label>
                                                        <textarea class="form-control" id="inputContent" name="content" placeholder="Content"></textarea>
                                                      </div>
                                                </div>
                                                <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                                                <button type="submit" class="btn btn-primary">Save</button>
                                                </div>
                                            </div>
                                            </div>
                                        </form>
                                      </div>
                            </div>
                            <hr />
                            @endif
                            @if (count($texts) > 0)
                                <div class="row">
                                    @foreach ($texts as $text)
                                        <div class="col-md-3">
                                                <div class="card">
                                                    <div class="card-body">
                                                        <p><b>{{ $text->title }}</b>
                                                            <br />
                                                            {{ $text->content }}
                                                        </p>
                                                    <div class="card-footer">
                                                        <div class="btn-group">
                                                            <!--<a href="/content/text/edit/{{ $text->id }}" class="btn"><i class="fas fa-edit"></i></a>-->
                                                            <a href="/content/text/delete/{{ $text->id }}" class="btn confirmDeleteButton"><i class="fas fa-trash-alt"></i></a>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @endif

                        </div>
                      </div>
                    </div>
                  </div>
        </div>
    </div>

    @include('modals.post_notes')

@endsection