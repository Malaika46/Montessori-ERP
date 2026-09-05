@extends('layouts.auth')

@section('title', 'Verify Email')

@section('content')
<div class="text-center mb-4">
    <div class="m-empty-icon mx-auto mb-3" style="width: 54px; height: 54px; font-size: 1.4rem;">
        <i class="bi bi-shield-lock-fill"></i>
    </div>
    <h3 class="fw-bold text-dark mb-1">Verify Email Address</h3>
    <p class="text-muted small">We sent a <strong>6-digit verification code</strong> and a <strong>verification link</strong> to <strong>{{ Auth::user()->email ?? 'your email' }}</strong>.</p>
</div>

@if (session('status'))
    <div class="alert alert-success small py-2 mb-4" role="alert">
        <i class="bi bi-check-circle me-1"></i> {{ session('status') }}
    </div>
@endif

@if (session('error'))
    <div class="alert alert-danger small py-2 mb-4" role="alert">
        <i class="bi bi-exclamation-circle me-1"></i> {{ session('error') }}
    </div>
@endif

<!-- Option 1: Enter 6-Digit Verification Code -->
<form method="POST" action="{{ route('verification.code') }}" class="mb-4">
    @csrf
    @if(Auth::check())
        <input type="hidden" name="email" value="{{ Auth::user()->email }}">
    @else
        <div class="mb-3 text-start">
            <label for="email" class="form-label fw-semibold small">Email Address</label>
            <input type="email" name="email" id="email" class="form-control" placeholder="user@domain.com" required>
        </div>
    @endif
    <div class="mb-3">
        <label for="code" class="form-label fw-semibold">Enter 6-Digit Verification Code</label>
        <input 
            id="code" 
            type="text" 
            name="code" 
            class="form-control text-center fw-bold fs-4 tracking-widest @error('code') is-invalid @enderror" 
            placeholder="123456" 
            maxlength="6" 
            required 
            autofocus
            style="letter-spacing: 0.35rem;"
        >
        @error('code')
            <div class="text-danger small mt-1 text-center">{{ $message }}</div>
        @enderror
    </div>

    <button type="submit" class="btn btn-m-primary w-100 py-2 justify-content-center fw-semibold">
        <i class="bi bi-check-circle"></i> Verify Code & Proceed
    </button>
</form>

<div class="text-center my-3 position-relative">
    <hr class="text-muted opacity-25">
    <span class="position-absolute top-50 start-50 translate-middle bg-white px-2 text-muted small">OR</span>
</div>

<p class="text-muted small text-center mb-3">
    Click the verification link directly inside the email sent to your inbox.
</p>

<div class="d-flex flex-column gap-2">
    <form method="POST" action="{{ route('verification.send') }}">
        @csrf
        <button type="submit" class="btn btn-m-secondary w-100 py-2 justify-content-center fw-semibold">
            <i class="bi bi-arrow-repeat"></i> Resend Verification Code & Link
        </button>
    </form>

    <form method="POST" action="{{ route('auth.logout') }}">
        @csrf
        <button type="submit" class="btn btn-m-outline w-100 py-2 justify-content-center fw-semibold text-muted">
            <i class="bi bi-box-arrow-right"></i> Sign Out
        </button>
    </form>
</div>
@endsection
