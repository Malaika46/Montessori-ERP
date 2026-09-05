@extends('layouts.app')

@section('title', 'User Management & Roles')

@section('content')
<div class="container-fluid p-0">
    <!-- Header Section -->
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 gap-3">
        <div>
            <h3 class="fw-bold text-dark mb-1">
                <i class="bi bi-shield-lock-fill text-success me-2"></i> System Users & Access Control
            </h3>
            <p class="text-muted small mb-0">Manage user accounts, roles, access permissions, and email verification states.</p>
        </div>
        <div>
            <button type="button" class="btn btn-m-primary shadow-sm" data-bs-toggle="modal" data-bs-target="#addUserModal">
                <i class="bi bi-person-plus-fill me-1"></i> Add System User
            </button>
        </div>
    </div>

    <!-- Stats Row -->
    <div class="row g-3 mb-4">
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm rounded-4 p-3 bg-white">
                <div class="d-flex align-items-center">
                    <div class="rounded-3 p-3 bg-success-subtle text-success me-3">
                        <i class="bi bi-people-fill fs-3"></i>
                    </div>
                    <div>
                        <div class="text-muted small fw-semibold text-uppercase">Total Users</div>
                        <h4 class="fw-bold text-dark mb-0">{{ $totalUsers ?? 0 }}</h4>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm rounded-4 p-3 bg-white">
                <div class="d-flex align-items-center">
                    <div class="rounded-3 p-3 bg-primary-subtle text-primary me-3">
                        <i class="bi bi-patch-check-fill fs-3"></i>
                    </div>
                    <div>
                        <div class="text-muted small fw-semibold text-uppercase">Active Users</div>
                        <h4 class="fw-bold text-dark mb-0">{{ $activeUsers ?? 0 }}</h4>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm rounded-4 p-3 bg-white">
                <div class="d-flex align-items-center">
                    <div class="rounded-3 p-3 bg-warning-subtle text-warning me-3">
                        <i class="bi bi-clock-history fs-3"></i>
                    </div>
                    <div>
                        <div class="text-muted small fw-semibold text-uppercase">Unverified Email</div>
                        <h4 class="fw-bold text-dark mb-0">{{ $unverifiedUsers ?? 0 }}</h4>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm rounded-4 p-3 bg-white">
                <div class="d-flex align-items-center">
                    <div class="rounded-3 p-3 bg-info-subtle text-info me-3">
                        <i class="bi bi-shield-check fs-3"></i>
                    </div>
                    <div>
                        <div class="text-muted small fw-semibold text-uppercase">System Roles</div>
                        <h4 class="fw-bold text-dark mb-0">{{ $roles->count() ?? 6 }}</h4>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- User Table -->
    <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light border-bottom">
                    <tr class="text-uppercase text-muted extra-small fw-bold">
                        <th class="ps-4">User Name</th>
                        <th>Role</th>
                        <th>Email Address</th>
                        <th>Verification Status</th>
                        <th>Created Date</th>
                        <th class="text-end pe-4">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($users as $user)
                        <tr>
                            <td class="ps-4">
                                <div class="d-flex align-items-center">
                                    <div class="avatar-initials bg-success text-white rounded-circle me-3 d-flex align-items-center justify-content-center fw-bold" style="width: 38px; height: 38px;">
                                        {{ strtoupper(substr($user->name, 0, 2)) }}
                                    </div>
                                    <div>
                                        <div class="fw-bold text-dark">{{ $user->name }}</div>
                                        <div class="extra-small text-muted">ID: #{{ $user->id }}</div>
                                    </div>
                                </div>
                            </td>
                            <td>
                                @php
                                    $roleName = strtolower($user->role->name ?? 'user');
                                    $badgeClass = match($roleName) {
                                        'superadmin' => 'bg-danger-subtle text-danger border-danger-subtle',
                                        'principal'  => 'bg-primary-subtle text-primary border-primary-subtle',
                                        'admin'      => 'bg-info-subtle text-info border-info-subtle',
                                        'teacher'    => 'bg-success-subtle text-success border-success-subtle',
                                        'student'    => 'bg-secondary-subtle text-secondary border-secondary-subtle',
                                        'parent'     => 'bg-warning-subtle text-dark border-warning-subtle',
                                        default      => 'bg-light text-dark border',
                                    };
                                @endphp
                                <span class="badge {{ $badgeClass }} border px-2 py-1 text-uppercase">
                                    {{ $user->role->display_name ?? strtoupper($roleName) }}
                                </span>
                            </td>
                            <td>
                                <span class="small font-monospace text-dark">{{ $user->email }}</span>
                            </td>
                            <td>
                                @if($user->hasVerifiedEmail())
                                    <span class="badge bg-success-subtle text-success border border-success-subtle px-2 py-1">
                                        <i class="bi bi-patch-check-fill me-1"></i> Verified & Active
                                    </span>
                                @else
                                    <span class="badge bg-warning-subtle text-dark border border-warning-subtle px-2 py-1">
                                        <i class="bi bi-clock-history me-1"></i> Code Sent (Unverified)
                                    </span>
                                @endif
                            </td>
                            <td class="small text-muted">
                                {{ $user->created_at ? $user->created_at->format('M d, Y') : '-' }}
                            </td>
                            <td class="text-end pe-4">
                                <div class="d-flex align-items-center justify-content-end gap-1">
                                    @if(!$user->hasVerifiedEmail())
                                        <button type="button" class="btn btn-sm btn-outline-primary border-0" data-bs-toggle="modal" data-bs-target="#enterCodeModal_{{ $user->id }}" title="Enter 6-digit Verification Code">
                                            <i class="bi bi-key-fill"></i> Enter Code
                                        </button>

                                        <form action="{{ route('users.verify-now', $user->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Manually verify email for {{ $user->name }}?');">
                                            @csrf
                                            <button type="submit" class="btn btn-sm btn-outline-success border-0" title="Manually Verify Email Now">
                                                <i class="bi bi-check-circle-fill"></i> Verify Now
                                            </button>
                                        </form>

                                        <form action="{{ route('users.resend-verification', $user->id) }}" method="POST" class="d-inline">
                                            @csrf
                                            <button type="submit" class="btn btn-sm btn-outline-warning border-0" title="Resend Verification Code & Link">
                                                <i class="bi bi-send-fill"></i> Resend Email
                                            </button>
                                        </form>

                                        <!-- Modal: Enter 6-digit Code for User -->
                                        <div class="modal fade text-start" id="enterCodeModal_{{ $user->id }}" tabindex="-1" aria-hidden="true">
                                            <div class="modal-dialog modal-dialog-centered modal-sm">
                                                <div class="modal-content border-0 shadow-lg rounded-4">
                                                    <div class="modal-header border-bottom p-3">
                                                        <h6 class="modal-title fw-bold text-dark mb-0">
                                                            <i class="bi bi-shield-lock-fill text-success me-1"></i> Enter Verification Code
                                                        </h6>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                    </div>
                                                    <form action="{{ route('verification.code') }}" method="POST">
                                                        @csrf
                                                        <input type="hidden" name="email" value="{{ $user->email }}">
                                                        <div class="modal-body p-3">
                                                            <p class="small text-muted mb-2">Enter 6-digit code for <strong>{{ $user->email }}</strong>:</p>
                                                            <input type="text" name="code" class="form-control text-center font-monospace fw-bold fs-4" placeholder="123456" maxlength="6" required style="letter-spacing: 0.3rem;">
                                                        </div>
                                                        <div class="modal-footer border-top p-2 bg-light rounded-bottom-4">
                                                            <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Cancel</button>
                                                            <button type="submit" class="btn btn-success btn-sm">Verify User</button>
                                                        </div>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    @endif

                                    @if($user->id !== auth()->id())
                                        <form action="{{ route('users.destroy', $user->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to remove this user?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-danger border-0">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </form>
                                    @else
                                        <span class="badge bg-light text-muted border px-2 py-1">You</span>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-4 text-muted">
                                No user accounts found. Click <strong>Add System User</strong> to create one!
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="p-3 border-top">
            {{ $users->links() }}
        </div>
    </div>
</div>

<!-- Modal: Add User -->
<div class="modal fade" id="addUserModal" tabindex="-1" aria-labelledby="addUserModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4">
            <div class="modal-header border-bottom p-4">
                <div>
                    <h5 class="modal-title fw-bold text-dark" id="addUserModalLabel">
                        <i class="bi bi-person-plus-fill text-success me-2"></i> Add System User
                    </h5>
                    <p class="text-muted small mb-0">Dispatches 6-digit code & link to user's email upon creation</p>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <form action="{{ route('users.store') }}" method="POST">
                @csrf
                <div class="modal-body p-4">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="first_name" class="form-label fw-semibold small">First Name</label>
                            <input type="text" name="first_name" class="form-control" placeholder="e.g. Maria" required>
                        </div>
                        <div class="col-md-6">
                            <label for="last_name" class="form-label fw-semibold small">Last Name</label>
                            <input type="text" name="last_name" class="form-control" placeholder="e.g. Montessori" required>
                        </div>
                    </div>

                    <div class="mt-3">
                        <label for="email" class="form-label fw-semibold small">Email Address</label>
                        <input type="email" name="email" class="form-control" placeholder="user@domain.com" required>
                        <div class="form-text small">Verification code & link will be sent to this email address.</div>
                    </div>

                    <div class="mt-3">
                        <label for="role_id" class="form-label fw-semibold small">Assign System Role</label>
                        <select name="role_id" class="form-select" required>
                            <option value="" disabled selected>-- Select Role --</option>
                            @foreach($roles as $role)
                                <option value="{{ $role->id }}">{{ $role->display_name }} ({{ strtoupper($role->name) }})</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mt-3">
                        <label for="password" class="form-label fw-semibold small">Temporary Password</label>
                        <input type="password" name="password" class="form-control" placeholder="Minimum 8 characters" required>
                    </div>
                </div>

                <div class="modal-footer border-top p-3 bg-light rounded-bottom-4">
                    <button type="button" class="btn btn-secondary btn-sm px-3" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-m-primary btn-sm px-4">
                        <i class="bi bi-send me-1"></i> Create User & Send Verification Email
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
