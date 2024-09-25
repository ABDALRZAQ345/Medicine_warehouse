@extends('layouts.bootstrap')
@section('content')

    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card text-center shadow-lg">
                    <div class="card-body">
                        <h1 class="card-title text-danger">Email Verification Failed</h1>
                        <p class="card-text">That verification link is not available any more </p>
                        {{--                        <a href="http://127.0.0.1:8000/login" class="btn btn-primary btn-lg">Go to Login</a>--}}
                    </div>
                </div>
            </div>
        </div>
    </div>


@endsection
