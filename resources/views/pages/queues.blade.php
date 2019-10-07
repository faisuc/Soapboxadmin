@extends('layouts.master')
@section('title', 'Queues')

@section('dashboardContent')
    <div class="row">
        <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">
            <div class="row">
                <div class="col-md-3">
                    <a href="/post/add{{ (isset($cell_id)) ? '/' . $cell_id : '' }}" class="btn btn-primary">NEW POST</a>
                </div>
                <?php /* @if (is_admin() || is_accountManager()) */ ?>
                    <div class="col-md-5 text-right">
                        <b>Select Social Cell: </b>
                    </div>
                    <div class="col-md-4">
                        @if(count($socialCells) > 0)
                        <select class="form-control" name="socialcell" onchange="(window.location = '/queues/' + this.options[this.selectedIndex].value);">
                            <option value="">All Social Cells</option>
                            @foreach ($socialCells as $socialCell)
                                <option {{ (isset($cell_id) && $cell_id == $socialCell->id) ? 'selected' : '' }} value="{{ $socialCell->id }}">{{ $socialCell->cell_name }}</option>
                            @endforeach
                        </select>
                        @else
                        <span class="text-danger">Please Create Social Cell for Create Post</span>
                        @endif
                    </div>
                <?php /* @endif */ ?>
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
                <?php
                $postCnt = 1;
                ?>
                @forelse ($posts as $post)
                    @if($postCnt > 0)
                    <div class="col-md-3 queue-{{ $post->id }}">
                        <div class="card post_box" style="border: 5px solid
                            @if ($post->status == 0)
                                #f44336;
                            @elseif ($post->status == 1)
                                #4caf50;
                            @elseif ($post->status == 2)
                                #f44336;
                            @elseif ($post->status == 3)
                                #FFFF00;
                            @elseif ($post->status == 4)
                                #516bf0;
                            @endif
                        ">
                            <img height="200px" class="card-img-top" src="{{ $post->featured_image }}" alt="Card image cap">
                            <div class="card-body">
                                <h3>{{ (strlen($post->title) > 90) ? substr($post->title,0,90).'..' : $post->title }}</h3>
                                <p class="description">
                                    <?php
                                    $view_link = '<a href="/post/edit/'.$post->id.'">Read More</a>';
                                    ?>
                                    @if(strlen($post->description) > 300)
                                        <?php echo substr($post->description,0,300).'..'.$view_link; ?>
                                    @else
                                        {{ $post->description }}
                                    @endif
                                </p>
                                <p class="attach_url">Attached URL: {{ $post->link }}</p>
                                <div class="row">
                                    <div class="col-md-12 social_icons">
                                        <div class="">
                                            @if($post->facebook || $post->twitter)
                                                @if($post->facebook)
                                                <a href="javascript:void(0);" class="btn btn-lg"><i class="fab fa-facebook"></i></a>
                                                @endif
                                                @if($post->twitter)
                                                <a href="javascript:void(0);" class="btn btn-lg"><i class="fab fa-twitter"></i></a>
                                                @endif
                                            @else
                                                <a href="javascript:void(0);" class="btn btn-lg">N/A</a>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                                <div class="tools">
                                    <span><i class="fa fa-clock-o"></i> {{ $post->schedule_to_post_date }}
                                </div>
                            </div>
                            <div class="action_icons">
                                <div class="btn-group">
                                    <a href="javascript:void(0);" data-post-id="{{ $post->id }}" class="btn" data-toggle="modal" data-target=".postnotes-modal"><i class="fas fa-sticky-note"></i></a>
                                    <a href="/post/edit/{{ $post->id }}" class="btn"><i class="fas fa-edit"></i></a>
                                    <a href="/post/delete/{{ $post->id }}" class="btn confirmDeleteButton"><i class="fas fa-trash-alt"></i></a>
                                </div>
                            </div>
                            <div class="stripe_status">
                                {{ paymentStatus($post->payment_status) }}
                            </div>
                            <div class="card-footer">
                                @if($post->facebook || $post->twitter)
                                <ul>
                                    @if($post->facebook)
                                    <li>
                                        {{ (isset($post->fb_like_share)) ? $post->fb_like_share['likes'] : 0 }}<br>
                                        FB Likes
                                    </li>
                                    <li>
                                        {{ (isset($post->fb_like_share)) ? $post->fb_like_share['shares'] : 0 }}<br>
                                        FB Shares
                                    </li>
                                    @endif
                                    @if($post->twitter)
                                    <li>
                                        {{ (isset($post->twt_like_share)) ? $post->twt_like_share['likes'] : 0 }}<br>
                                        Twitter Likes
                                    </li>
                                    <li>
                                        {{ (isset($post->twt_like_share)) ? $post->twt_like_share['shares'] : 0 }}<br>
                                        Twitter Shares
                                    </li>
                                    @endif
                                </ul>
                                @else
                                <p>N/A</p>
                                @endif
                            </div>
                        </div>
                    </div>
                    @endif
                <?php
                $postCnt++;
                ?>
                @empty
                    <div class="col-md-12">
                        <h3>No Posts.</h3>
                    </div>
                @endforelse
            </div>
        </div>
    </div>

    @if (isset($fb_manage_pages))
        @include('modals.fb_pages')
        <script type="text/javascript">
        $(document).ready(function() {
            $('.fbpages-modal').modal();
        });
        </script>
    @endif

    @include('modals.post_notes')

@endsection