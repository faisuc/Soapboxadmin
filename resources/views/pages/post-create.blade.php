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
                <?php $temp = 1; ?>
                @if(isset($facebook) || isset($twitter) || isset($instagram) || isset($pinterest))
                    @if(isset($facebook))
                    <?php $temp = 0 ?>
                    @endif
                    @if(isset($twitter))
                    <?php $temp = 0 ?>
                    @endif
                    @if(isset($instagram))
                    <?php $temp = 0 ?>
                    @endif
                    @if(isset($pinterest))
                    <?php $temp = 0 ?>
                    @endif
                @else
                    <?php $temp++; ?>
                @endif
                @if($temp > 0)
                    <?php
                    if(isset($cell_id)) {
                    ?>
                    <div class="alert alert-danger">
                        <!-- <p>Please Connect Social Account By Clicking <a href="/socialaccounts">Here</a></p> -->
                        <p>Please Connect Social Account By Clicking <a href="/socialcell/{{ $cell_id }}">Here</a></p>
                    </div>
                    <?php
                    }
                    ?>
                @endif
                <!-- <form action="/post/store" method="post" enctype="multipart/form-data"> -->
                <form action="{{ url('/post/store') }}" method="post" enctype="multipart/form-data">
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
                        <label for="inputCell">Social Cell</label>
                        <select id="inputCell" name="cell_id" class="form-control" onchange="(window.location = '/post/add/' + this.options[this.selectedIndex].value);" required>
                            <option value="">All Cells</option>
                            @foreach ($socialCells as $socialCell)
                                <option value="{{ $socialCell->id }}" {{ (isset($cell_id) && $cell_id == $socialCell->id) ? 'selected' : '' }} >{{ $socialCell->cell_name }}</option>
                            @endforeach
                        </select>
                    </div>

                    @if(isset($cell_id))
                        <?php $disabled = ''; ?>
                    @else
                        <?php $disabled = 'disabled'; ?>
                    @endif
                    <div class="form-group">
                        <label for="inputTitle">Title</label>
                        <input id="inputTitle" type="text" placeholder="Title" value="{{ old('title') }}" name="title" required class="form-control" <?php echo $disabled; ?>>
                    </div>
                    <div class="form-group">
                        <label for="inputTextContent">Text Content</label>
                        <textarea id="inputTextContent" type="text" placeholder="Text Content" name="description" class="form-control" required <?php echo $disabled; ?>>{{ old('description') }}</textarea>
                    </div>
                    <div class="form-group">
                        <label for="inputURL">URL/Link</label>
                        <input id="inputURL" type="text" placeholder="URL/Link" value="{{ old('link') }}" name="link" required class="form-control" <?php echo $disabled; ?>>
                    </div>
                    <div class="form-group">
                        <label for="inputScheduleDate">Schedule Post</label>
                        <input id="inputScheduleDate" readonly type="text" placeholder="Date & Time" value="{{ old('schedule_date') }}" name="schedule_date" class="form-control datetimepicker" required <?php echo $disabled; ?>>
                    </div>
                    <div class="form-group">
                        <label for="inputStatus">Status</label>
                        <select id="inputStatus" name="status" class="form-control" <?php echo $disabled; ?>>
                            <option value="">Status</option>
                            @foreach ($post_statuses as $key => $status)
                                <option value="{{ $key }}">{{ $status }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="inputPhoto">Photo</label>
                        <input id="inputPhoto" type="file" placeholder="Photo" name="photo" class="form-control" <?php echo $disabled; ?>>
                    </div>
                    @if(isset($facebook))
                        @if(!empty($pages))
                            <label for="facebook_post">Facebook Pages</label>
                            <label class="custom-control custom-checkbox">
                                <input class="custom-control-input" id="facebook_post" type="checkbox" name="facebook_post"><span class="custom-control-label" <?php echo $disabled; ?>>Post to Facebook</span>
                            </label>
                            <div id="facebook-pages" style="display: none;">
                                @foreach ($pages as $page_key => $page)
                                <label class="custom-control custom-radio">
                                    <input class="custom-control-input" type="radio" name="fb_page" data-page-name="{{ $page['name'] }}" data-page-picture="{{ (isset($page['picture'])) ? $page['picture']['data']['url'] : '' }}" value="{{ $page['id'] }}" {{ ($page_key == 0) ? 'checked' : '' }}><span class="custom-control-label">{{ $page['name'] }}</span>
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
                            <input class="custom-control-input" id="twitter_post" type="checkbox" data-profile-name="{{ $twitter_profile_name }}" data-username="{{ $twitter_username }}" data-profile-pic="{{ $twitter_profile_pic }}" name="twitter_post" <?php echo $disabled; ?>><span class="custom-control-label">Post to Twitter</span>
                        </label>
                    </div>
                    @endif
                    @if(isset($instagram))
                    <hr>
                    <div class="form-group">
                        <label for="instagram_post">Instagram Pages</label>
                        <label class="custom-control custom-checkbox">
                            <input class="custom-control-input" id="instagram_post" type="checkbox" name="instagram_post" data-username="{{ $insta_username }}" data-profile-pic="{{ $insta_profile_pic }}" <?php echo $disabled; ?>><span class="custom-control-label">Post to instagram</span>
                        </label>
                    </div>
                    <?php /* ?><div id="instagram_user_pass" style="display: none;">
                        <div class="form-group">
                            <label for="inputInstaUser">Instagram User</label>
                            <input id="inputInstaUser" type="text" placeholder="Instagram Username/Email" name="insta_username" class="form-control">
                        </div>
                        <div class="form-group">
                            <label for="inputInstaPassword">Instagram Password</label>
                            <input id="inputInstaPassword" type="password" placeholder="Instagram Password" name="insta_password" class="form-control">
                        </div>
                    </div><?php */ ?>
                    @endif
                    @if(isset($pinterest))
                        @if(!empty($boards))
                            <hr>
                            <label for="pinterest_post">Pinterest</label>
                            <label class="custom-control custom-checkbox">
                                <input class="custom-control-input" id="pinterest_post" type="checkbox" name="pinterest_post" <?php echo $disabled; ?>><span class="custom-control-label">Post to Pinterest</span>
                            </label>
                            <div id="pinterest-boards" style="display: none;">
                                <?php $i = 0; ?>
                                @foreach ($boards as $page_key => $page)
                                <label class="custom-control custom-radio">
                                    <input class="custom-control-input" type="radio" name="pint_board" value="{{ $page_key }}" {{ ($i == 0) ? 'checked' : '' }}><span class="custom-control-label">{{ $page }}</span>
                                </label>
                                <?php $i++ ?>
                                @endforeach
                            </div>
                        @endif
                    @endif
                    <div class="form-group">
                        <input type="submit" value="SAVE" class="btn btn-primary" <?php echo $disabled; ?>>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@include('modals.fb_preview_post')

@endsection