@extends('layouts.master')
@section('title', 'Update Post')

@section('dashboardContent')
    <div class="row">
        <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">
            <div class="section-block" id="basicform">
                <h3 class="section-title">Update Post</h3>
            </div>
            <div class="card">
                <div class="card-body">
                    <?php $temp = 0; ?>
                    @if(session()->get('fb_access_token') == '' || session()->get('twitter_logged_in') == '' || session()->get('instagram') == '')
                        @if(session()->get('fb_access_token') != '')
                        <?php $temp = 0 ?>
                        @elseif(session()->get('twitter_logged_in') != '')
                        <?php $temp = 0 ?>
                        @elseif(session()->get('instagram') != '')
                        <?php $temp = 0 ?>
                        @else
                        <?php $temp++; ?>
                        @endif
                    @endif
                    @if($temp > 0)
                    <div class="alert alert-danger">
                        <p>Please Connect Social Account By Clicking <a href="/socialaccounts">Here</a></p>
                    </div>
                    @endif
                    <!-- <form action="/post/update/{{ $post->id }}" method="post" enctype="multipart/form-data"> -->
                    <form action="{{ url('/post/update/'.$post->id) }}" method="post" enctype="multipart/form-data">
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
                            <label for="inputTitle">Title</label>
                            <input id="inputTitle" type="text" placeholder="Title" value="{{ $post->title }}" name="title" class="form-control">
                        </div>
                        <div class="form-group">
                            <label for="inputTextContent">Text Content</label>
                            <textarea id="inputTextContent" type="text" placeholder="Text Content" name="description" class="form-control">{{ $post->description }}</textarea>
                        </div>
                        <div class="form-group">
                            <label for="inputURL">URL/Link</label>
                            <input id="inputURL" type="text" placeholder="URL/Link" value="{{ $post->link }}" name="link" class="form-control">
                        </div>
                        <div class="form-group">
                            <label for="inputScheduleDate">Schedule Post</label>
                            <input id="inputScheduleDate" readonly type="text" placeholder="Date & Time" value="{{ $post->schedule_to_post_date }}" name="schedule_date" class="form-control datetimepicker" required>
                        </div>
                        <div class="form-group">
                            <label for="inputStatus">Status</label>
                            <select id="inputStatus" name="status" class="form-control">
                                @foreach ($post_statuses as $key => $status)
                                    <option {{ $key == $post->status ? 'selected' : '' }} value="{{ $key }}">{{ $status }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="inputPhoto">Photo</label>
                            <input id="inputPhoto" type="file" placeholder="Photo" name="photo" class="form-control">
                        </div>
                        <div class="form-group">
                            <img src="{{ url($post->featuredimage) }}" class="img-responsive" style="width: 200px;">
                        </div>
                        @if(isset($pages))
                            <label>Facebook Pages</label>
                            @foreach ($pages as $page_key => $page)
                            <label class="custom-control custom-radio">
                                <input class="custom-control-input" type="radio" name="fb_page" value="{{ $page['id'] }}" {{ ($page_key == 0) ? 'checked' : '' }}><span class="custom-control-label">{{ $page['name'] }}</span>
                            </label>
                            @endforeach
                        @endif
                        @if(isset($twitter))
                        <div class="form-group">
                            <label class="custom-control custom-checkbox">
                                <input class="custom-control-input" type="checkbox" name="twitter_post"><span class="custom-control-label">Post to Twitter</span>
                            </label>
                        </div>
                        @endif
                        @if(isset($instagram))
                        <hr>
                        <div class="form-group">
                            <label for="inputInstaUser">Instagram User</label>
                            <input id="inputInstaUser" type="text" placeholder="Instagram Username/Email" name="insta_username" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label for="inputInstaPassword">Instagram Password</label>
                            <input id="inputInstaPassword" type="password" placeholder="Instagram Password" name="insta_password" class="form-control" required>
                        </div>
                        <hr>
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