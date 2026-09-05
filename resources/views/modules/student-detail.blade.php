@extends('layouts.app')

@section('title', 'Student Profile - ' . $student->user->name)

@section('content')
<div class="mb-3">
    <a href="{{ route('students.index') }}" class="btn btn-sm btn-outline-secondary">
        <i class="bi bi-arrow-left me-1"></i> Back to Student Directory
    </a>
</div>

<div class="row g-4">
    <!-- Left Column: Student Bio Card -->
    <div class="col-12 col-lg-4">
        <div class="m-card text-center p-4">
            <div class="avatar-initials mx-auto mb-3" style="width: 72px; height: 72px; font-size: 1.8rem; background: var(--m-forest); color: var(--m-cream);">
                {{ strtoupper(substr($student->user->first_name ?? 'S', 0, 1) . substr($student->user->last_name ?? '', 0, 1)) }}
            </div>
            <h4 class="fw-bold text-dark mb-1">{{ $student->user->name }}</h4>
            <span class="badge bg-secondary font-monospace px-3 py-1 mb-2">{{ $student->student_number }}</span>

            <div class="mt-2">
                @if($student->user->hasVerifiedEmail())
                    <span class="badge bg-success text-white px-3 py-1">Active Verified Account</span>
                @else
                    <span class="badge bg-warning text-dark px-3 py-1">Email Unverified (Code Sent)</span>
                @endif
            </div>

            <hr class="my-3">

            <div class="text-start small">
                <div class="mb-2"><strong>Email:</strong> {{ $student->user->email }}</div>
                <div class="mb-2"><strong>Campus:</strong> {{ $student->campus->name ?? 'Main Campus' }}</div>
                <div class="mb-2"><strong>Classroom:</strong> {{ $student->classroom->name ?? 'Unassigned' }}</div>
                <div class="mb-2"><strong>Age Group:</strong> {{ $student->classroom->age_group ?? '-' }}</div>
                <div class="mb-2"><strong>Gender:</strong> {{ $student->gender ?? '-' }}</div>
                <div class="mb-2"><strong>Date of Birth:</strong> {{ $student->date_of_birth ? $student->date_of_birth->format('M d, Y') : '-' }}</div>
            </div>
        </div>
    </div>

    <!-- Right Column: Relationships, Progress & Reports -->
    <div class="col-12 col-lg-8">
        <!-- Linked Parents Card -->
        <div class="m-card mb-4">
            <h5 class="fw-bold text-dark mb-3">
                <i class="bi bi-person-heart text-danger me-2"></i> Linked Parents & Guardians
            </h5>

            @if($student->parents->count() > 0)
                <div class="list-group list-group-flush border rounded-3">
                    @foreach($student->parents as $parent)
                        <div class="list-group-item d-flex align-items-center justify-content-between py-3">
                            <div>
                                <div class="fw-bold text-dark">{{ $parent->user->name }}</div>
                                <div class="small text-muted">{{ $parent->user->email }} | {{ $parent->phone ?? 'No Phone' }}</div>
                            </div>
                            <span class="badge bg-primary-subtle text-primary border border-primary-subtle px-3 py-1">
                                {{ $parent->pivot->relationship_type ?? 'Parent' }}
                            </span>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="alert alert-light border small text-muted">
                    No parents linked to this student yet. Link parents from the <strong>Parent Directory</strong>.
                </div>
            @endif
        </div>

        <!-- Gamified LMS Progress & Rewards -->
        <div class="m-card mb-4">
            <h5 class="fw-bold text-dark mb-3">
                <i class="bi bi-controller text-primary me-2"></i> Gamified LMS Progress & XP Badges
            </h5>

            <div class="row g-3">
                <div class="col-md-6">
                    <div class="p-3 bg-light rounded-3 border">
                        <div class="small text-muted mb-1">Total Activities Completed</div>
                        <div class="fs-4 fw-bold text-dark">{{ $student->lmsProgress->where('status', 'completed')->count() }}</div>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="p-3 bg-light rounded-3 border">
                        <div class="small text-muted mb-1">Total XP Points Earned</div>
                        <div class="fs-4 fw-bold text-success">{{ $student->lmsProgress->sum('xp_earned') }} XP</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
