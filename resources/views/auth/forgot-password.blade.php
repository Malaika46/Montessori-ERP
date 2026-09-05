@extends('layouts.auth')

@section('title', 'Forgot Password')

@section('content')
<div class="text-center mb-4">
    <div class="m-empty-icon mx-auto mb-3" style="width: 54px; height: 54px; font-size: 1.4rem;">
        <i class="bi bi-key-fill"></i>
    </div>
    <h3 class="fw-bold text-dark mb-1">Reset Password</h3>
    <p class="text-muted small">Enter your email address and we'll send you a secure link to reset your password.</p>
</div>

@if (session('status'))
    <div class="alert alert-success small py-2 mb-4" role="alert">
        <i class="bi bi-check-circle me-1"></i> {{ session('status') }}
    </div>
@endif

<form method="POST" action="{{ route('auth.forgot-password.post') }}">
    @csrf

    <div class="mb-4">
        <label for="email" class="form-label">Email Address</label>
        <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" required autofocus placeholder="name@domain.com">
        @error('email')
            <div class="text-danger small mt-1">{{ $message }}</div>
        @enderror
    </div>

    <button type="submit" class="btn btn-m-primary w-100 py-2 justify-content-center fw-semibold mb-3">
        <i class="bi bi-send"></i> Send Reset Link
    </button>

    <div class="text-center mt-3 pt-3 border-top">
        <a href="{{ route('auth.login') }}" class="small text-decoration-none text-muted">
            <i class="bi bi-arrow-left"></i> Back to Sign In
        </a>
    </div>
</form>
@endsection
