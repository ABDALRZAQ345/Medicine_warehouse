@extends('layouts.bootstrap')
@section('content')
    <div class="container mt-5">
        <div class="card">
            <div class="card-header text-center">
                <h1>Change Your Password</h1>
            </div>
            <div class="card-body">
                <!-- Display any validation errors -->
                @if ($errors->any())
                    <div class="alert alert-danger">
                        <ul>
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form action="{{ route('password.update') }}" method="POST">
                    @csrf

                    <div class="mb-3">
                        <label for="new_password" class="form-label">New Password:</label>
                        <input type="password" name="new_password" class="form-control" required>
                    </div>

                    <div class="mb-3">
                        <label for="new_password_confirmation" class="form-label">Confirm New Password:</label>
                        <input type="password" name="new_password_confirmation" class="form-control" required>
                    </div>

                    <button type="submit" class="btn btn-primary">Update Password</button>
                </form>
            </div>
        </div>
    </div>


@endsection
