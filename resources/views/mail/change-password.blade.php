@extends('layouts.bootstrap')
@section('content')
    <div class="container mt-5">
        <div class="card">
            <div class="card-header text-center">
                <h1>Welcome, {{ $name }}!</h1>
            </div>
            <div class="card-body">
                <h3 class="text-center">It seems that you want to change your password</h3>
                <p class="text-center">If you want to change your password, click the button below:</p>

                <div class="text-center">
                    <form action="{{ route('password.change', [$id, $hash]) }}" method="GET">
                        @csrf
                        <button type="submit" class="btn btn-primary">Change Password</button>
                    </form>
                </div>

            </div>
        </div>
    </div>


@endsection
