@extends('layouts.master')
@section('content')
    <!--start page wrapper -->
    <div class="page-wrapper">

        <div class="page-content">
            <!-- PAGE-HEADER -->
            @include('layouts/pageContentHeader/store')

            <div class="card p-sm-4 p-md-5">
                <div class="card-body">
                    <h2 class="text-primary">{{ $announcement->subject }}</h2>
                    <br>
                    <div id="content">
                        {!! $announcement->content !!}
                        <hr>
                        <span class="d-flex">
                            Created by: <b class="ms-2">{{ $announcement->user->employee->firstname }} {{ $announcement->user->employee->lastname }}</b>
                            <i class="ms-auto">Created: {{ $announcement->created_at }}</i>
                        </span>
                    </div>
                </div>
            </div>
        </div>

    </div>
	<!--end page wrapper -->

@stop
@section('pages_specific_scripts')  
@stop
