@extends('layouts.master')
@section('title', 'Make Change')

@section('dashboardContent')
    <div class="row">
        <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">
            <div class="section-block" id="basicform">
                <h3 class="section-title">New Note</h3>
            </div>
            <div class="card">
                <div class="card-body">
                    <form action="{{ url('/post/submit_make_change/'.$post_id) }}" method="post" enctype="multipart/form-data">
                        @csrf
                        <div class="form-group">
                            <label for="message-text" class="col-form-label">Note:</label>
                            <textarea class="form-control" name="content"></textarea>
                            <input type="hidden" name="post_id" value="{{ $post_id }}">
                        </div>
                        <div class="form-group">
                            <button class="btn btn-primary">Add Note</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection