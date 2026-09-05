@extends('layouts.app')

@section('title', 'Teachers & Guides Directory')

@section('content')
<div class="container-fluid p-0">
    <!-- Header Section -->
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 gap-3">
        <div>
            <h3 class="fw-bold text-dark mb-1">
                <i class="bi bi-person-badge text-success me-2"></i> Teachers & Montessori Guides
            </h3>
            <p class="text-muted small mb-0">Manage certified guides, environment assignments, and credentials.</p>
        </div>
        <div>
            <button type="button" class="btn btn-m-primary shadow-sm" data-bs-toggle="modal" data-bs-target="#addTeacherModal">
                <i class="bi bi-person-plus-fill me-1"></i> Register New Teacher
            </button>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="row g-3 mb-4">
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm rounded-4 p-3 bg-white">
                <div class="d-flex align-items-center">
                    <div class="rounded-3 p-3 bg-success-subtle text-success me-3">
                        <i class="bi bi-person-badge fs-3"></i>
                    </div>
                    <div>
                        <div class="text-muted small fw-semibold text-uppercase">Total Teachers</div>
                        <h4 class="fw-bold text-dark mb-0">{{ $totalTeachers ?? 0 }}</h4>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm rounded-4 p-3 bg-white">
                <div class="d-flex align-items-center">
                    <div class="rounded-3 p-3 bg-primary-subtle text-primary me-3">
                        <i class="bi bi-door-open fs-3"></i>
                    </div>
                    <div>
                        <div class="text-muted small fw-semibold text-uppercase">Classrooms</div>
                        <h4 class="fw-bold text-dark mb-0">{{ $classrooms->count() ?? 0 }}</h4>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filter Bar -->
    <div class="card border-0 shadow-sm rounded-4 mb-4">
        <div class="card-body p-3">
            <form method="GET" action="{{ route('teachers.index') }}" class="row g-2 align-items-center">
                <div class="col-md-5">
                    <div class="input-group input-group-sm">
                        <span class="input-group-text bg-light border-end-0"><i class="bi bi-search text-muted"></i></span>
                        <input type="text" name="search" class="form-control border-start-0" placeholder="Search by name, email, or specialization..." value="{{ request('search') }}">
                    </div>
                </div>
                <div class="col-md-4">
                    <select name="campus_id" class="form-select form-select-sm">
                        <option value="">-- All Campuses --</option>
                        @foreach($campuses as $campus)
                            <option value="{{ $campus->id }}" {{ request('campus_id') == $campus->id ? 'selected' : '' }}>
                                {{ $campus->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3 d-flex gap-2">
                    <button type="submit" class="btn btn-m-primary btn-sm w-100">Filter</button>
                    <a href="{{ route('teachers.index') }}" class="btn btn-light btn-sm border">Reset</a>
                </div>
            </form>
        </div>
    </div>

    <!-- Teachers Data Table -->
    <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light border-bottom">
                    <tr class="text-uppercase text-muted extra-small fw-bold">
                        <th class="ps-4">Teacher Name & Account</th>
                        <th>Specialization</th>
                        <th>Campus</th>
                        <th>Assigned Classrooms</th>
                        <th>Email Verification</th>
                        <th class="text-end pe-4">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($teachers as $teacher)
                        <tr>
                            <td class="ps-4">
                                <div class="d-flex align-items-center">
                                    <div class="avatar-initials bg-success text-white rounded-circle me-3 d-flex align-items-center justify-content-center fw-bold" style="width: 40px; height: 40px;">
                                        {{ strtoupper(substr($teacher->user->name ?? 'T', 0, 2)) }}
                                    </div>
                                    <div>
                                        <div class="fw-bold text-dark">{{ $teacher->user->name ?? 'N/A' }}</div>
                                        <div class="small text-muted font-monospace">{{ $teacher->user->email ?? 'N/A' }}</div>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <span class="badge bg-light text-dark border px-2 py-1">
                                    {{ $teacher->specialization ?? 'General Montessori' }}
                                </span>
                            </td>
                            <td>
                                <span class="small fw-semibold text-secondary">
                                    <i class="bi bi-building me-1"></i> {{ $teacher->campus->name ?? 'Main Campus' }}
                                </span>
                            </td>
                            <td>
                                @forelse($teacher->classrooms as $cls)
                                    <span class="badge bg-primary-subtle text-primary border border-primary-subtle px-2 py-1 me-1 mb-1">
                                        <i class="bi bi-door-open me-1"></i>{{ $cls->name }}
                                    </span>
                                @empty
                                    <span class="text-muted small italic">Unassigned</span>
                                @endforelse
                            </td>
                            <td>
                                @if($teacher->user && $teacher->user->hasVerifiedEmail())
                                    <span class="badge bg-success-subtle text-success border border-success-subtle px-2 py-1">
                                        <i class="bi bi-patch-check-fill me-1"></i> Verified & Active
                                    </span>
                                @else
                                    <span class="badge bg-warning-subtle text-dark border border-warning-subtle px-2 py-1">
                                        <i class="bi bi-clock-history me-1"></i> Code Sent (Unverified)
                                    </span>
                                @endif
                            </td>
                            <td class="text-end pe-4">
                                <div class="d-flex align-items-center justify-content-end gap-1">
                                    <!-- Assign Classrooms Button -->
                                    <button type="button" class="btn btn-sm btn-outline-success border-0" data-bs-toggle="modal" data-bs-target="#assignClassroomsModal_{{ $teacher->id }}" title="Assign Classrooms">
                                        <i class="bi bi-door-open-fill me-1"></i> Assign Class
                                    </button>

                                    @if($teacher->user && !$teacher->user->hasVerifiedEmail())
                                        <button type="button" class="btn btn-sm btn-outline-primary border-0" data-bs-toggle="modal" data-bs-target="#enterTeacherCodeModal_{{ $teacher->user->id }}" title="Enter 6-digit Verification Code">
                                            <i class="bi bi-key-fill"></i> Enter Code
                                        </button>

                                        <form action="{{ route('users.verify-now', $teacher->user->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Manually verify email for teacher {{ $teacher->user->name }}?');">
                                            @csrf
                                            <button type="submit" class="btn btn-sm btn-outline-success border-0" title="Verify Email Now">
                                                <i class="bi bi-check-circle-fill"></i> Verify
                                            </button>
                                        </form>

                                        <form action="{{ route('users.resend-verification', $teacher->user->id) }}" method="POST" class="d-inline">
                                            @csrf
                                            <button type="submit" class="btn btn-sm btn-outline-warning border-0" title="Resend Verification Code & Link">
                                                <i class="bi bi-send-fill"></i> Resend Code
                                            </button>
                                        </form>

                                        <!-- Modal: Enter Code for Teacher -->
                                        <div class="modal fade text-start" id="enterTeacherCodeModal_{{ $teacher->user->id }}" tabindex="-1" aria-hidden="true">
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
                                                        <input type="hidden" name="email" value="{{ $teacher->user->email }}">
                                                        <div class="modal-body p-3">
                                                            <p class="small text-muted mb-2">Enter 6-digit code sent to <strong>{{ $teacher->user->email }}</strong>:</p>
                                                            <input type="text" name="code" class="form-control text-center font-monospace fw-bold fs-4" placeholder="123456" maxlength="6" required style="letter-spacing: 0.3rem;">
                                                        </div>
                                                        <div class="modal-footer border-top p-2 bg-light rounded-bottom-4">
                                                            <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Cancel</button>
                                                            <button type="submit" class="btn btn-success btn-sm">Verify Teacher</button>
                                                        </div>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    @endif

                                    <!-- Modal: Assign Classrooms -->
                                    <div class="modal fade text-start" id="assignClassroomsModal_{{ $teacher->id }}" tabindex="-1" aria-hidden="true">
                                        <div class="modal-dialog modal-dialog-centered">
                                            <div class="modal-content border-0 shadow-lg rounded-4">
                                                <div class="modal-header border-bottom p-3">
                                                    <h6 class="modal-title fw-bold text-dark mb-0">
                                                        <i class="bi bi-door-open-fill text-success me-2"></i> Assign Classrooms to {{ $teacher->user->name ?? 'Teacher' }}
                                                    </h6>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                </div>
                                                <form action="{{ route('teachers.assign-classrooms', $teacher->id) }}" method="POST">
                                                    @csrf
                                                    <div class="modal-body p-4">
                                                        <label class="form-label fw-semibold small text-dark mb-2">Select Classrooms / Environments:</label>
                                                        <div class="card border p-2 rounded-3 bg-light" style="max-height: 220px; overflow-y: auto;">
                                                            @php
                                                                $assignedIds = $teacher->classrooms->pluck('id')->toArray();
                                                            @endphp
                                                            @forelse($classrooms as $cls)
                                                                <div class="form-check p-2 rounded-2 border-bottom bg-white mb-1">
                                                                    <input class="form-check-input ms-0 me-2" type="checkbox" name="classroom_ids[]" value="{{ $cls->id }}" id="cls_chk_{{ $teacher->id }}_{{ $cls->id }}" {{ in_array($cls->id, $assignedIds) ? 'checked' : '' }}>
                                                                    <label class="form-check-label fw-semibold text-dark cursor-pointer" for="cls_chk_{{ $teacher->id }}_{{ $cls->id }}">
                                                                        {{ $cls->name }} <span class="badge bg-secondary-subtle text-secondary ms-1">{{ $cls->age_group ?? 'Environment' }}</span>
                                                                    </label>
                                                                </div>
                                                            @empty
                                                                <p class="text-muted small mb-0 p-2">No classrooms available in system.</p>
                                                            @endforelse
                                                        </div>
                                                        <div class="form-text small mt-2">Check all classrooms this teacher is assigned to guide.</div>
                                                    </div>
                                                    <div class="modal-footer border-top p-3 bg-light rounded-bottom-4">
                                                        <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Cancel</button>
                                                        <button type="submit" class="btn btn-success btn-sm px-3">Save Classroom Assignments</button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>

                                    <form action="{{ route('teachers.destroy', $teacher->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Archive this teacher record?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger border-0" title="Archive Teacher">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-4 text-muted">
                                No teacher records found. Click <strong>Register New Teacher</strong> to create one!
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="p-3 border-top">
            {{ $teachers->links() }}
        </div>
    </div>
</div>

<!-- Modal: Add New Teacher -->
<div class="modal fade" id="addTeacherModal" tabindex="-1" aria-labelledby="addTeacherModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4">
            <div class="modal-header border-bottom p-4">
                <div>
                    <h5 class="modal-title fw-bold text-dark" id="addTeacherModalLabel">
                        <i class="bi bi-person-plus-fill text-success me-2"></i> Register New Teacher (Guide)
                    </h5>
                    <p class="text-muted small mb-0">Creates guide user account & sends 6-digit email verification code.</p>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <form action="{{ route('teachers.store') }}" method="POST">
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
                        <label for="email" class="form-label fw-semibold small">Teacher Email Address</label>
                        <input type="email" name="email" class="form-control" placeholder="teacher@montessori.edu" required>
                        <div class="form-text small">Verification code & link will be emailed to teacher. Account requires verification.</div>
                    </div>

                    <div class="row g-3 mt-1">
                        <div class="col-md-6">
                            <label for="specialization" class="form-label fw-semibold small">Specialization</label>
                            <input type="text" name="specialization" class="form-control" placeholder="e.g. Sensorial & Math">
                        </div>
                        <div class="col-md-6">
                            <label for="campus_id" class="form-label fw-semibold small">Assigned Campus</label>
                            <select name="campus_id" class="form-select">
                                <option value="" selected>-- Main Campus --</option>
                                @foreach($campuses as $campus)
                                    <option value="{{ $campus->id }}">{{ $campus->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="mt-3">
                        <label class="form-label fw-semibold small">Assign Classrooms (Optional)</label>
                        <div class="card border p-2 rounded-3 bg-light" style="max-height: 150px; overflow-y: auto;">
                            @foreach($classrooms as $cls)
                                <div class="form-check p-1 ms-1">
                                    <input class="form-check-input" type="checkbox" name="classroom_ids[]" value="{{ $cls->id }}" id="new_cls_{{ $cls->id }}">
                                    <label class="form-check-label small font-semibold" for="new_cls_{{ $cls->id }}">{{ $cls->name }}</label>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <div class="mt-3">
                        <label for="password" class="form-label fw-semibold small">Temporary Password</label>
                        <input type="password" name="password" class="form-control" placeholder="Minimum 8 characters" required>
                    </div>
                </div>

                <div class="modal-footer border-top p-3 bg-light rounded-bottom-4">
                    <button type="button" class="btn btn-secondary btn-sm px-3" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-m-primary btn-sm px-4">
                        <i class="bi bi-send me-1"></i> Register Teacher & Send Verification Code
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
