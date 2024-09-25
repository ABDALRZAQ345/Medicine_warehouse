@extends('layouts.bootstrap')
@section('content')

    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card shadow-lg">
                    <div class="card-body text-center">
                        <h1 class="card-title">Hello, {{ htmlspecialchars($user->first_name, ENT_QUOTES, 'UTF-8') }}</h1>
                        <p class="card-text">Please verify that you are trying to sign up to the application_name .</p>
                        <form method="GET" action="http://127.0.0.1:8000/verify-email/{{$user->id}}/{{$str}}">
                            @csrf
                            <button type="submit" class="btn btn-primary btn-lg">Verify Email</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>


@endsection
