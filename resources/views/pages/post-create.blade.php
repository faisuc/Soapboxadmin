@extends('layouts.master')
@section('title', 'Create New Post')

@section('dashboardContent')
    <div class="row">
        <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">
            <div class="section-block" id="basicform">
                <h3 class="section-title">Create New Post</h3>
            </div>
            <div class="card">
                <div class="card-body">
                    @if(session()->get('fb_access_token') == '')
                    <div class="alert alert-danger">
                        <p>Please Connect Social Account By Clicking <a href="/socialaccounts">Here</a></p>
                    </div>
                    @endif
                    <form action="/post/store" method="post" enctype="multipart/form-data">
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
                            <label for="inputTitle">Title</label>
                            <input id="inputTitle" type="text" placeholder="Title" value="{{ old('title') }}" name="title" class="form-control">
                        </div>
                        <div class="form-group">
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
                        </div>
                        <div class="form-group">
                            <label for="inputStatus">Status</label>
                            <select id="inputStatus" name="status" class="form-control">
                                @foreach ($post_statuses as $key => $status)
                                    <option value="{{ $key }}">{{ $status }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="inputPhoto">Photo</label>
                            <input id="inputPhoto" type="file" placeholder="Photo" name="photo" class="form-control">
                        </div>
                        @if(isset($pages))
                            <label>Facebook Pages</label>
                            @foreach ($pages as $page_key => $page)
                            <label class="custom-control custom-radio">
                                <input class="custom-control-input" type="radio" name="fb_page" value="{{ $page['id'] }}" {{ ($page_key == 0) ? 'checked' : '' }}><span class="custom-control-label">{{ $page['name'] }}</span>
                            </label>
                            @endforeach
                        @endif
                        <div class="form-group">
                            <input type="submit" value="SAVE" class="btn btn-primary">
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection