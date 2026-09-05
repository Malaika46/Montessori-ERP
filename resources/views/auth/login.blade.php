@extends('layouts.auth')

@section('title', 'Sign In')

@section('content')
<div class="text-center mb-4">
    <h3 class="fw-bold text-dark mb-1">Welcome Back</h3>
    <p class="text-muted small">Sign in to your Montessori ERP portal</p>
</div>

@if(session('status'))
    <div class="alert alert-success small py-2 mb-4" role="alert">
        <i class="bi bi-check-circle me-1"></i> {{ session('status') }}
    </div>
@endif

@if(session('error'))
    <div class="alert alert-danger small py-2 mb-4" role="alert">
        <i class="bi bi-exclamation-triangle me-1"></i> {{ session('error') }}
    </div>
@endif

<form method="POST" action="{{ route('auth.login.post') }}">
    @csrf

    <!-- Email Address or Student Roll No -->
    <div class="mb-3">
        <label for="email" class="form-label">Email Address or Student Roll No</label>
        <div class="input-group">
            <span class="input-group-text bg-light text-muted border-end-0"><i class="bi bi-person-badge"></i></span>
            <input id="email" type="text" class="form-control border-start-0 @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" required autofocus placeholder="email@montessori.edu or STU-00001">
        </div>
        @error('email')
            <div class="text-danger small mt-1">{{ $message }}</div>
        @enderror
    </div>

    <!-- Password -->
    <div class="mb-3">
        <div class="d-flex justify-content-between align-items-center mb-1">
            <label for="password" class="form-label mb-0">Password</label>
            <a href="{{ route('auth.forgot-password') }}" class="small text-decoration-none">Forgot Password?</a>
        </div>
        <div class="input-group">
            <span class="input-group-text bg-light text-muted border-end-0"><i class="bi bi-lock"></i></span>
            <input id="password" type="password" class="form-control border-start-0 @error('password') is-invalid @enderror" name="password" required placeholder="••••••••">
        </div>
        @error('password')
            <div class="text-danger small mt-1">{{ $message }}</div>
        @enderror
    </div>

    <!-- Remember Me -->
    <div class="mb-4 form-check">
        <input type="checkbox" class="form-check-input" id="remember" name="remember" {{ old('remember') ? 'checked' : '' }}>
        <label class="form-check-label small text-muted" for="remember">Keep me signed in</label>
    </div>

    <!-- Submit Button -->
    <button type="submit" class="btn btn-m-primary w-100 py-2 justify-content-center fw-semibold mb-3">
        <i class="bi bi-box-arrow-in-right"></i> Sign In to Portal
    </button>

    <div class="text-center mt-3 pt-3 border-top">
        <p class="small text-muted mb-0">
            Don't have an account? <a href="{{ route('auth.register') }}" class="fw-semibold text-decoration-none">Create Account</a>
        </p>
    </div>
</form>
@endsection
