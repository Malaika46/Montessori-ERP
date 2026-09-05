@extends('layouts.auth')

@section('title', 'Sign Up')

@section('content')
<div class="text-center mb-4">
    <h3 class="fw-bold text-dark mb-1">Create Account</h3>
    <p class="text-muted small">Register for Montessori ERP Portal access</p>
</div>

<form method="POST" action="{{ route('auth.register.post') }}">
    @csrf

    <div class="row g-2 mb-3">
        <!-- First Name -->
        <div class="col-6">
            <label for="first_name" class="form-label">First Name</label>
            <input id="first_name" type="text" class="form-control @error('first_name') is-invalid @enderror" name="first_name" value="{{ old('first_name') }}" required autofocus placeholder="First name">
            @error('first_name')
                <div class="text-danger small mt-1">{{ $message }}</div>
            @enderror
        </div>

        <!-- Last Name -->
        <div class="col-6">
            <label for="last_name" class="form-label">Last Name</label>
            <input id="last_name" type="text" class="form-control @error('last_name') is-invalid @enderror" name="last_name" value="{{ old('last_name') }}" required placeholder="Last name">
            @error('last_name')
                <div class="text-danger small mt-1">{{ $message }}</div>
            @enderror
        </div>
    </div>

    <!-- Email Address -->
    <div class="mb-3">
        <label for="email" class="form-label">Email Address</label>
        <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" required placeholder="name@domain.com">
        @error('email')
            <div class="text-danger small mt-1">{{ $message }}</div>
        @enderror
    </div>

    <!-- Role Selection -->
    <div class="mb-3">
        <label for="role" class="form-label">System Access Role</label>
        <select id="role" name="role" class="form-select @error('role') is-invalid @enderror" required>
            <option value="" disabled selected>Select your portal role...</option>
            @foreach($roles as $roleOption)
                <option value="{{ $roleOption->name }}" {{ old('role') === $roleOption->name ? 'selected' : '' }}>
                    {{ $roleOption->display_name }} ({{ $roleOption->name }})
                </option>
            @endforeach
        </select>
        @error('role')
            <div class="text-danger small mt-1">{{ $message }}</div>
        @enderror
    </div>

    <!-- Password -->
    <div class="mb-3">
        <label for="password" class="form-label">Password</label>
        <input id="password" type="password" class="form-control @error('password') is-invalid @enderror" name="password" required placeholder="Minimum 8 characters">
        @error('password')
            <div class="text-danger small mt-1">{{ $message }}</div>
        @enderror
    </div>

    <!-- Confirm Password -->
    <div class="mb-4">
        <label for="password_confirmation" class="form-label">Confirm Password</label>
        <input id="password_confirmation" type="password" class="form-control" name="password_confirmation" required placeholder="Re-enter password">
    </div>

    <!-- Submit Button -->
    <button type="submit" class="btn btn-m-primary w-100 py-2 justify-content-center fw-semibold mb-3">
        <i class="bi bi-person-plus"></i> Create Account
    </button>

    <div class="text-center mt-3 pt-3 border-top">
        <p class="small text-muted mb-0">
            Already have an account? <a href="{{ route('auth.login') }}" class="fw-semibold text-decoration-none">Sign In</a>
        </p>
    </div>
</form>
@endsection
