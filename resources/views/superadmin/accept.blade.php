<!DOCTYPE html>
<html>
<head>
    <title>Accept Invitation</title>
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
</head>
<body class="container mt-5">

<h2>Accept Invitation</h2>

<p>
    You are invited to join
    <strong>{{ $invitation->company->name }}</strong>
    as <strong>{{ $invitation->role }}</strong>
</p>

@if($errors->any())
    <div class="alert alert-danger">
        <ul>
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<form method="POST" action="{{ route('invitations.register', $invitation->token) }}">
    @csrf

    <div class="mb-3">
        <label>Name</label>
        <input type="text" name="name" class="form-control" required>
    </div>

    <div class="mb-3">
        <label>Email</label>
        <input type="email" value="{{ $invitation->email }}" class="form-control" disabled>
    </div>

    <div class="mb-3">
        <label>Password</label>
        <input type="password" name="password" class="form-control" required>
    </div>

    <div class="mb-3">
        <label>Confirm Password</label>
        <input type="password" name="password_confirmation" class="form-control" required>
    </div>

    <button class="btn btn-success">Create Account</button>
</form>

</body>
</html>
