@extends('layouts.auth')

@section('title', 'New Password')

@section('content')
<div class="text-center mb-4">
    <h3 class="fw-bold text-dark mb-1">Set New Password</h3>
    <p class="text-muted small">Enter your new secure password below</p>
</div>

<form method="POST" action="{{ route('auth.reset-password.post') }}">
    @csrf
    <input type="hidden" name="token" value="{{ $token }}">

    <div class="mb-3">
        <label for="email" class="form-label">Email Address</label>
        <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ $email ?? old('email') }}" required placeholder="name@domain.com">
        @error('email')
            <div class="text-danger small mt-1">{{ $message }}</div>
        @enderror
    </div>

    <div class="mb-3">
        <label for="password" class="form-label">New Password</label>
        <input id="password" type="password" class="form-control @error('password') is-invalid @enderror" name="password" required placeholder="Minimum 8 characters">
        @error('password')
            <div class="text-danger small mt-1">{{ $message }}</div>
        @enderror
    </div>

    <div class="mb-4">
        <label for="password_confirmation" class="form-label">Confirm New Password</label>
        <input id="password_confirmation" type="password" class="form-control" name="password_confirmation" required placeholder="Re-enter new password">
    </div>

    <button type="submit" class="btn btn-m-primary w-100 py-2 justify-content-center fw-semibold mb-3">
        <i class="bi bi-shield-check"></i> Reset Password
    </button>
</form>
@endsection
