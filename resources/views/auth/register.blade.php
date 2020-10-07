<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Workout Tracker - Registration</title>

    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
</head>
<body>
    <div class="vh-100 d-flex justify-content-center align-items-center bg-secondary">
        <div class="card text-white bg-dark" style="width: 350px">
            <div class="card-body">
                <h3 class="card-title text-center">Workout Tracker</h3>
                @if ($errors->any())
                    <div class="alert alert-danger">
                        <ul>
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif
                <form method="POST" class="mt-3">
                    @csrf
                    <div class="form-group">
                        <label for="name">@lang('Name')</label>
                        <input class="form-control" type="text" name="name" id="name">
                    </div>
                    <div class="form-group">
                        <label for="email">@lang('Email')</label>
                        <input class="form-control" type="email" name="email" id="email">
                    </div>
                    <div class="form-group">
                        <label for="password">@lang('Password')</label>
                        <input class="form-control" type="password" name="password" id="password">
                    </div>
                    <div class="form-group">
                        <label for="password_confirmation">@lang('Password Confirmation')</label>
                        <input class="form-control" type="password" name="password_confirmation" id="password_confirmation">
                    </div>
                    <div class="text-center mt-4">
                        <input class="btn btn-light" type="submit" value="@lang('Registration')">
                    </div>
                </form>
            </div>
        </div>
    </div>
</body>
</html>
