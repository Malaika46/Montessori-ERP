@extends('layouts.app')

@section('title', 'Student Management')

@section('content')
<!-- Hero Card -->
<div class="hero-welcome-card mb-4">
    <div class="d-flex flex-column flex-lg-row align-items-lg-center justify-content-between gap-3">
        <div>
            <div class="hero-role-pill">
                <i class="bi bi-people-fill"></i> Montessori Student Registry
            </div>
            <h1 class="hero-welcome-title fs-2 mb-1">Student Management</h1>
            <p class="hero-welcome-subtitle">
                Superadmin registry to enroll students, assign prepared classroom environments, and link parent accounts.
            </p>
        </div>

        <div>
            <button type="button" class="btn-pill-gold" data-bs-toggle="modal" data-bs-target="#addStudentModal">
                <i class="bi bi-person-plus-fill me-1"></i> Enroll New Student
            </button>
        </div>
    </div>
</div>

<!-- Stat Row -->
<div class="row g-3 mb-4">
    <div class="col-12 col-sm-6 col-xl-6">
        <div class="stat-box-card">
            <div class="stat-box-header">
                <span>Total Enrolled Students</span>
                <i class="bi bi-people-fill stat-box-icon text-primary"></i>
            </div>
            <div class="stat-box-number">{{ $totalStudents }}</div>
            <div class="stat-box-sub">Across all campus environments</div>
        </div>
    </div>

    <div class="col-12 col-sm-6 col-xl-6">
        <div class="stat-box-card">
            <div class="stat-box-header">
                <span>Active Learning Students</span>
                <i class="bi bi-check-circle-fill stat-box-icon text-success"></i>
            </div>
            <div class="stat-box-number text-success">{{ $activeStudents }}</div>
            <div class="stat-box-sub">Active attendance & LMS participation</div>
        </div>
    </div>
</div>

<!-- Main Table Card -->
<div class="m-card">
    <div class="d-flex align-items-center justify-content-between mb-4 flex-wrap gap-3">
        <div>
            <h4 class="fw-bold mb-1 text-dark">Student Directory</h4>
            <p class="text-muted small mb-0">Filtered by campus, classroom, and enrollment status</p>
        </div>

        <form action="{{ route('students.index') }}" method="GET" class="d-flex gap-2 flex-wrap">
            <input type="text" name="search" class="form-control form-control-sm" placeholder="Search number or name..." value="{{ request('search') }}" style="width: 200px;">
            <select name="classroom_id" class="form-select form-select-sm" style="width: 170px;" onchange="this.form.submit()">
                <option value="">All Classrooms</option>
                @foreach($classrooms as $c)
                    <option value="{{ $c->id }}" {{ request('classroom_id') == $c->id ? 'selected' : '' }}>{{ $c->name }}</option>
                @endforeach
            </select>
            <button type="submit" class="btn btn-sm btn-m-primary"><i class="bi bi-search"></i></button>
        </form>
    </div>

    <div class="table-responsive">
        <table class="table table-hover align-middle">
            <thead class="table-light">
                <tr>
                    <th scope="col">Student ID</th>
                    <th scope="col">Full Name</th>
                    <th scope="col">Campus</th>
                    <th scope="col">Classroom Environment</th>
                    <th scope="col">Verification & Status</th>
                    <th scope="col">Parents Linked</th>
                    <th scope="col" class="text-end">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($students as $student)
                    <tr>
                        <td>
                            <span class="badge bg-secondary text-white font-monospace">{{ $student->student_number }}</span>
                        </td>
                        <td>
                            <div class="d-flex align-items-center gap-2">
                                <div class="avatar-initials" style="width: 34px; height: 34px; font-size: 0.85rem;">
                                    {{ strtoupper(substr($student->user->first_name ?? 'S', 0, 1) . substr($student->user->last_name ?? '', 0, 1)) }}
                                </div>
                                <div>
                                    <div class="fw-bold text-dark">{{ $student->user->name }}</div>
                                    <div class="small text-muted">{{ $student->user->email }}</div>
                                </div>
                            </div>
                        </td>
                        <td>
                            <span class="small text-muted">{{ $student->campus->name ?? 'Main Campus' }}</span>
                        </td>
                        <td>
                            @if($student->classroom)
                                <span class="badge bg-success-subtle text-success border border-success-subtle">
                                    <i class="bi bi-door-open me-1"></i> {{ $student->classroom->name }}
                                </span>
                            @else
                                <span class="badge bg-light text-muted border">Unassigned</span>
                            @endif
                        </td>
                        <td>
                            @if($student->user->hasVerifiedEmail())
                                <span class="badge bg-success text-white">Active (Verified)</span>
                            @else
                                <span class="badge bg-warning text-dark">Code Sent (Unverified)</span>
                            @endif
                        </td>
                        <td>
                            <span class="small text-muted">
                                {{ $student->parents->count() > 0 ? $student->parents->pluck('user.name')->implode(', ') : 'No Parent Linked' }}
                            </span>
                        </td>
                        <td class="text-end">
                            <div class="d-flex align-items-center justify-content-end gap-1">
                                @if(!$student->user->hasVerifiedEmail())
                                    <form action="{{ route('users.verify-now', $student->user->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Manually verify email for student {{ $student->user->name }}?');">
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-outline-success border-0" title="Verify Email Now">
                                            <i class="bi bi-check-circle-fill"></i> Verify
                                        </button>
                                    </form>

                                    <form action="{{ route('users.resend-verification', $student->user->id) }}" method="POST" class="d-inline">
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-outline-warning border-0" title="Resend Verification Code & Link">
                                            <i class="bi bi-send-fill"></i> Resend Code
                                        </button>
                                    </form>
                                @endif

                                <a href="{{ route('students.show', $student->id) }}" class="btn btn-sm btn-outline-primary border-0" title="View Profile">
                                    <i class="bi bi-eye-fill"></i>
                                </a>

                                <form action="{{ route('students.destroy', $student->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Archive this student record?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger border-0" title="Archive Student">
                                        <i class="bi bi-archive-fill"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="text-center py-4 text-muted">
                            No student records found. Click <strong>Enroll New Student</strong> to create your first student!
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-3">
        {{ $students->links() }}
    </div>
</div>

<!-- Add Student Modal -->
<div class="modal fade" id="addStudentModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4">
            <div class="modal-header border-bottom p-4">
                <div>
                    <h5 class="modal-title fw-bold text-dark">
                        <i class="bi bi-person-plus-fill text-success me-2"></i> Enroll Student (Superadmin)
                    </h5>
                    <p class="text-muted small mb-0">Creates student user, generates Student Number, and sends verification code & link.</p>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <form action="{{ route('students.store') }}" method="POST">
                @csrf
                <div class="modal-body p-4">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="first_name" class="form-label fw-semibold small">First Name</label>
                            <input type="text" name="first_name" class="form-control" placeholder="e.g. Leo" required>
                        </div>
                        <div class="col-md-6">
                            <label for="last_name" class="form-label fw-semibold small">Last Name</label>
                            <input type="text" name="last_name" class="form-control" placeholder="e.g. Tolstoy" required>
                        </div>
                    </div>

                    <div class="mt-3">
                        <label for="email" class="form-label fw-semibold small">Student Email / Parent Contact Email</label>
                        <input type="email" name="email" class="form-control" placeholder="student@montessori.edu" required>
                        <div class="form-text small">6-digit verification code & link will be emailed to this address.</div>
                    </div>

                    <div class="row g-3 mt-1">
                        <div class="col-md-6">
                            <label for="campus_id" class="form-label fw-semibold small">Campus</label>
                            <select name="campus_id" class="form-select">
                                @foreach($campuses as $campus)
                                    <option value="{{ $campus->id }}">{{ $campus->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label for="classroom_id" class="form-label fw-semibold small">Classroom Environment</label>
                            <select name="classroom_id" class="form-select">
                                <option value="">-- Select Classroom --</option>
                                @foreach($classrooms as $c)
                                    <option value="{{ $c->id }}">{{ $c->name }} ({{ $c->age_group }})</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="row g-3 mt-1">
                        <div class="col-md-6">
                            <label for="date_of_birth" class="form-label fw-semibold small">Date of Birth</label>
                            <input type="date" name="date_of_birth" class="form-control">
                        </div>
                        <div class="col-md-6">
                            <label for="gender" class="form-label fw-semibold small">Gender</label>
                            <select name="gender" class="form-select">
                                <option value="Male">Male</option>
                                <option value="Female">Female</option>
                                <option value="Other">Other</option>
                            </select>
                        </div>
                    </div>

                    <div class="mt-3">
                        <label for="password" class="form-label fw-semibold small">Default Student Password</label>
                        <input type="password" name="password" class="form-control" placeholder="Minimum 8 characters" required>
                    </div>
                </div>

                <div class="modal-footer border-top p-3 bg-light rounded-bottom-4">
                    <button type="button" class="btn btn-secondary btn-sm px-3" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-m-primary btn-sm px-4">
                        <i class="bi bi-check-circle me-1"></i> Enroll Student & Send Verification Code
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
