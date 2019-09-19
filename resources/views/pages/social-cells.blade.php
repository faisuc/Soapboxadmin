@extends('layouts.master')
@section('title', 'Social Cells')

@section('dashboardContent')
    <div class="row">
        <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">
            <div class="row">
                <div class="col-md-3">
                    <a href="{{ url('/socialcell/add') }}" class="btn btn-primary">Create Social Cell</a>
                </div>
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
                <div class="col-m-12">
                    @forelse ($socialcells as $socialcell)
                        {{ $socialcell->cell_name }}
                    @empty
                        No Post
                    @endforelse    
                </div>
            </div>

        </div>
    </div>

@endsection