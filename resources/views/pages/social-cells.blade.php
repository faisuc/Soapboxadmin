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
                @forelse ($socialcells as $socialcell)
                    <div class="col-md-3">
                        <div class="card" style="border: 5px solid " >
                            <div class="card-body">
                                <p class="card-text">
                                    <h3>{{ $socialcell->cell_name }}</h3>
                                    <div class="tools">
                                        <span><i class="fa fa-clock"></i> {{ $socialcell->created_at }} </span>
                                    </div>
                                    <!-- {{ $socialcell->description }} -->
                                </p>
                                <p class="card-text">Owner Mail : {{ $socialcell->email_owner }} </p>
                                <p class="card-text">Marketer Mail : {{ $socialcell->email_marketer }} </p>
                                <p class="card-text">Client Mail : {{ $socialcell->email_client }} </p>
                                <p class="card-text">Payment Status : {{ ($socialcell->payment_status == '1' ? 'Waiting Payment' : 'Done') }} </p>
                                <!-- <a href="#" class="btn btn-lg"><i class="fab fa-facebook"></i></a> -->
                                <!-- <a href="#" class="btn btn-lg"><i class="fab fa-twitter"></i></a> -->
                            </div>
                            <div class="card-footer">
                                <div class="btn-group">
                                    <!-- <a href="#" data-post-id="{{ $socialcell->id }}" class="btn" data-toggle="modal" data-target=".postnotes-modal"><i class="fas fa-sticky-note"></i></a> -->
                                    <a href="/socialcell/edit/{{ $socialcell->id }}" class="btn"><i class="fas fa-edit"></i></a>
                                    <a href="/socialcell/delete/{{ $socialcell->id }}" class="btn confirmDeleteButton"><i class="fas fa-trash-alt"></i></a>
                                    <a href="/socialcell/{{ $socialcell->id }}" class="btn btn-info float-right">View</a>
                                    <!-- <input type="button" value="Generate" class="btn btn-info generate" > -->
                                </div>
                            </div>
                        </div>
                    </div>

                    @empty
                    <div class="col-md-12">
                        No Cells Added.
                    </div>
                @endforelse    
            </div>

        </div>
    </div>

@endsection