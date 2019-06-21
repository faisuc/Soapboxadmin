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
                    <?php $temp = 1; ?>
                    @if(isset($facebook) || isset($twitter) || isset($instagram))
                        @if(isset($facebook))
                        <?php $temp = 0 ?>
                        @endif
                        @if(isset($twitter))
                        <?php $temp = 0 ?>
                        @endif
                        @if(isset($instagram))
                        <?php $temp = 0 ?>
                        @endif
                    @else
                        <?php $temp++; ?>
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
                        @if(isset($facebook))
                            @if(!empty($pages))
                                <label for="facebook_post">Facebook Pages</label>
                                <label class="custom-control custom-checkbox">
                                    <input class="custom-control-input" id="facebook_post" type="checkbox" name="facebook_post"><span class="custom-control-label">Post to Facebook</span>
                                </label>
                                <div id="facebook-pages" style="display: none;">
                                    @foreach ($pages as $page_key => $page)
                                    <label class="custom-control custom-radio">
                                        <input class="custom-control-input" type="radio" name="fb_page" value="{{ $page['id'] }}" {{ ($page_key == 0) ? 'checked' : '' }}><span class="custom-control-label">{{ $page['name'] }}</span>
                                    </label>
                                    @endforeach
                                </div>
                            @endif
                        @endif
                        @if(isset($twitter))
                        <hr>
                        <div class="form-group">
                            <label for="twitter_post">Twitter Post</label>
                            <label class="custom-control custom-checkbox">
                                <input class="custom-control-input" id="twitter_post" type="checkbox" name="twitter_post"><span class="custom-control-label">Post to Twitter</span>
                            </label>
                        </div>
                        @endif
                        @if(isset($instagram))
                        <hr>
                        <div class="form-group">
                            <label for="instagram_post">Instagram Pages</label>
                            <label class="custom-control custom-checkbox">
                                <input class="custom-control-input" id="instagram_post" type="checkbox" name="instagram_post"><span class="custom-control-label">Post to instagram</span>
                            </label>
                        </div>
                        <div id="instagram_user_pass" style="display: none;">
                            <div class="form-group">
                                <label for="inputInstaUser">Instagram User</label>
                                <input id="inputInstaUser" type="text" placeholder="Instagram Username/Email" name="insta_username" class="form-control">
                            </div>
                            <div class="form-group">
                                <label for="inputInstaPassword">Instagram Password</label>
                                <input id="inputInstaPassword" type="password" placeholder="Instagram Password" name="insta_password" class="form-control">
                            </div>
                        </div>
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