@extends('layouts.app')

@section('title', 'Superadmin Dashboard')

@section('content')
<!-- Dark Hero Welcome Card -->
<div class="hero-welcome-card">
    <div class="d-flex flex-column flex-lg-row align-items-lg-center justify-content-between gap-4">
        <div>
            <div class="hero-role-pill">
                <i class="bi bi-circle-fill text-success" style="font-size: 0.4rem;"></i> Role: SUPERADMIN &nbsp;•&nbsp; Multi-Campus Database Isolated
            </div>
            <h1 class="hero-welcome-title">Welcome back, {{ Auth::user()->name }}</h1>
            <p class="hero-welcome-subtitle">
                System Superadmin Portal • Complete oversight across all campuses, security audit streams, and administrative modules.
            </p>
        </div>

        <div class="d-flex align-items-center gap-2 flex-wrap">
            <a href="{{ route('students.index') }}" class="btn-pill-gold">
                <i class="bi bi-person-plus-fill"></i> Enroll Student
            </a>
            <a href="{{ route('teachers.index') }}" class="btn-pill-white">
                <i class="bi bi-person-badge-fill"></i> Manage Teachers
            </a>
        </div>
    </div>
</div>

<!-- Dynamic Stat Box Grid -->
<div class="row g-3 mb-4">
    <div class="col-12 col-sm-6 col-xl-3">
        <div class="stat-box-card">
            <div class="stat-box-header">
                <span>Enrolled Students</span>
                <i class="bi bi-mortarboard stat-box-icon text-success"></i>
            </div>
            <div class="stat-box-number text-success">{{ $studentCount }}</div>
            <div class="stat-box-sub">Active student accounts</div>
        </div>
    </div>

    <div class="col-12 col-sm-6 col-xl-3">
        <div class="stat-box-card">
            <div class="stat-box-header">
                <span>Teaching Guides</span>
                <i class="bi bi-person-workspace stat-box-icon text-primary"></i>
            </div>
            <div class="stat-box-number text-primary">{{ $teacherCount }}</div>
            <div class="stat-box-sub">Montessori certified guides</div>
        </div>
    </div>

    <div class="col-12 col-sm-6 col-xl-3">
        <div class="stat-box-card">
            <div class="stat-box-header">
                <span>Registered Parents</span>
                <i class="bi bi-person-heart stat-box-icon text-warning"></i>
            </div>
            <div class="stat-box-number text-warning">{{ $parentCount }}</div>
            <div class="stat-box-sub">Family linkage profiles</div>
        </div>
    </div>

    <div class="col-12 col-sm-6 col-xl-3">
        <div class="stat-box-card">
            <div class="stat-box-header">
                <span>Classrooms</span>
                <i class="bi bi-door-open stat-box-icon text-info"></i>
            </div>
            <div class="stat-box-number text-info">{{ $classroomCount }}</div>
            <div class="stat-box-sub">Prepared environments</div>
        </div>
    </div>
</div>

<!-- Lower Sections Layout -->
<div class="row g-4">
    <div class="col-lg-7 col-xl-8">
        <x-card title="Superadmin ERP Core Management" subtitle="System-wide administration, RBAC matrix, and module oversight">
            <div class="row g-3">
                <div class="col-md-6">
                    <a href="{{ route('students.index') }}" class="d-block text-decoration-none p-3 bg-light rounded-3 border h-100">
                        <div class="fw-bold text-dark mb-1"><i class="bi bi-people-fill text-success me-2"></i> Student Management</div>
                        <div class="small text-muted">Enroll students, assign classrooms, view profiles.</div>
                    </a>
                </div>
                <div class="col-md-6">
                    <a href="{{ route('teachers.index') }}" class="d-block text-decoration-none p-3 bg-light rounded-3 border h-100">
                        <div class="fw-bold text-dark mb-1"><i class="bi bi-person-badge-fill text-primary me-2"></i> Teacher Directory</div>
                        <div class="small text-muted">Guide profiles, specializations, and classroom authority.</div>
                    </a>
                </div>
                <div class="col-md-6">
                    <a href="{{ route('parents.index') }}" class="d-block text-decoration-none p-3 bg-light rounded-3 border h-100">
                        <div class="fw-bold text-dark mb-1"><i class="bi bi-person-heart text-warning me-2"></i> Parent Directory</div>
                        <div class="small text-muted">Family accounts, student linking, verification status.</div>
                    </a>
                </div>
                <div class="col-md-6">
                    <a href="{{ route('classrooms.index') }}" class="d-block text-decoration-none p-3 bg-light rounded-3 border h-100">
                        <div class="fw-bold text-dark mb-1"><i class="bi bi-door-open-fill text-info me-2"></i> Classrooms & Environments</div>
                        <div class="small text-muted">Toddler, Primary, Lower & Upper Elementary rooms.</div>
                    </a>
                </div>
                <div class="col-md-6">
                    <a href="{{ route('communication.index') }}" class="d-block text-decoration-none p-3 bg-light rounded-3 border h-100">
                        <div class="fw-bold text-dark mb-1"><i class="bi bi-chat-dots-fill text-secondary me-2"></i> School Communication</div>
                        <div class="small text-muted">Organization-wide broadcast monitoring & announcements.</div>
                    </a>
                </div>
                <div class="col-md-6">
                    <a href="{{ route('lms.index') }}" class="d-block text-decoration-none p-3 bg-light rounded-3 border h-100">
                        <div class="fw-bold text-dark mb-1"><i class="bi bi-controller text-danger me-2"></i> Gamified LMS System</div>
                        <div class="small text-muted">Learning paths, quizzes, XP rewards, teacher content.</div>
                    </a>
                </div>
            </div>
        </x-card>
    </div>

    <div class="col-lg-5 col-xl-4">
        <x-card title="System Audit Stream" subtitle="Live database audit trail">
            <div class="list-group list-group-flush border-0 small">
                @forelse($recentLogs as $log)
                    <div class="list-group-item px-0 py-2 border-bottom">
                        <div class="d-flex justify-content-between">
                            <span class="fw-bold text-dark">{{ $log->action }}</span>
                            <span class="text-muted small">{{ $log->created_at->diffForHumans() }}</span>
                        </div>
                        <div class="text-muted small">
                            By {{ $log->user->name ?? 'System' }} ({{ strtoupper($log->module) }})
                        </div>
                    </div>
                @empty
                    <div class="text-muted p-2">No administrative audit events recorded yet.</div>
                @endforelse
            </div>
            <div class="mt-3 text-end">
                <a href="{{ route('audit-logs.index') }}" class="btn btn-sm btn-outline-secondary">View All Audit Logs</a>
            </div>
        </x-card>
    </div>
</div>
@endsection
