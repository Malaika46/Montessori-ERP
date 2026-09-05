@extends('layouts.app')

@section('title', 'Dashboard')
@section('page_title', 'School Overview')

@section('breadcrumb')
    <li class="breadcrumb-item active" aria-current="page">Dashboard</li>
@endsection

@section('content')
<!-- Metric Cards Grid (Showing Real DB Counts or 0, NO fake numbers) -->
<div class="row g-3 mb-4">
    <div class="col-12 col-sm-6 col-xl-3">
        <x-card class="mb-0">
            <div class="stat-card">
                <div class="stat-icon">
                    <i class="bi bi-people-fill"></i>
                </div>
                <div>
                    <div class="stat-value">{{ $studentCount ?? 0 }}</div>
                    <div class="stat-label">Total Enrolled Students</div>
                </div>
            </div>
        </x-card>
    </div>

    <div class="col-12 col-sm-6 col-xl-3">
        <x-card class="mb-0">
            <div class="stat-card">
                <div class="stat-icon terracotta">
                    <i class="bi bi-door-open-fill"></i>
                </div>
                <div>
                    <div class="stat-value">{{ $classroomCount ?? 0 }}</div>
                    <div class="stat-label">Active Classrooms</div>
                </div>
            </div>
        </x-card>
    </div>

    <div class="col-12 col-sm-6 col-xl-3">
        <x-card class="mb-0">
            <div class="stat-card">
                <div class="stat-icon">
                    <i class="bi bi-eye-fill"></i>
                </div>
                <div>
                    <div class="stat-value">{{ $observationCount ?? 0 }}</div>
                    <div class="stat-label">Observations Logged</div>
                </div>
            </div>
        </x-card>
    </div>

    <div class="col-12 col-sm-6 col-xl-3">
        <x-card class="mb-0">
            <div class="stat-card">
                <div class="stat-icon terracotta">
                    <i class="bi bi-clock-history"></i>
                </div>
                <div>
                    <div class="stat-value">{{ $todayAttendance ?? '0%' }}</div>
                    <div class="stat-label">Today's Attendance Rate</div>
                </div>
            </div>
        </x-card>
    </div>
</div>

<div class="row g-4">
    <!-- Main Workspace Empty State -->
    <div class="col-lg-8">
        <x-card title="System Readiness & Workspace Overview" subtitle="Initial ERP & LMS foundation initialized">
            <div class="p-3 bg-light rounded-3 mb-4 border">
                <div class="d-flex align-items-center gap-2 mb-2">
                    <x-badge variant="sage">Environment Ready</x-badge>
                    <span class="small text-muted">Laravel 9 + MySQL Database Connected</span>
                </div>
                <p class="small text-secondary mb-0">
                    Welcome to <strong>Montessori ERP</strong>. The core visual design system, navigation shell, responsive layout, and database migrations have been successfully initialized without mock data.
                </p>
            </div>

            <!-- Empty State Component Example -->
            <x-empty-state 
                icon="bi bi-mortarboard" 
                title="No Students Registered Yet" 
                description="Your database is currently clean and empty. Create your first student record or classroom to begin tracking Montessori learning progress."
                actionText="Student Module Foundation Ready"
                actionUrl="{{ route('students.index') }}"
                actionIcon="bi bi-arrow-right"
            />
        </x-card>
    </div>

    <!-- Right Quick Actions & System Info -->
    <div class="col-lg-4">
        <x-card title="Quick Navigation" subtitle="ERP & LMS Core Modules">
            <div class="list-group list-group-flush border-0">
                <a href="{{ route('students.index') }}" class="list-group-item list-group-item-action d-flex align-items-center justify-content-between px-0 py-25 border-bottom">
                    <div class="d-flex align-items-center gap-2">
                        <i class="bi bi-people text-success fs-5"></i>
                        <span class="fw-medium text-dark" style="font-size: 0.9rem;">Student Records</span>
                    </div>
                    <i class="bi bi-chevron-right text-muted small"></i>
                </a>
                <a href="{{ route('classrooms.index') }}" class="list-group-item list-group-item-action d-flex align-items-center justify-content-between px-0 py-25 border-bottom">
                    <div class="d-flex align-items-center gap-2">
                        <i class="bi bi-door-open text-primary fs-5"></i>
                        <span class="fw-medium text-dark" style="font-size: 0.9rem;">Classrooms & Environments</span>
                    </div>
                    <i class="bi bi-chevron-right text-muted small"></i>
                </a>
                <a href="{{ route('observations.index') }}" class="list-group-item list-group-item-action d-flex align-items-center justify-content-between px-0 py-25 border-bottom">
                    <div class="d-flex align-items-center gap-2">
                        <i class="bi bi-eye text-warning fs-5"></i>
                        <span class="fw-medium text-dark" style="font-size: 0.9rem;">Observation Log</span>
                    </div>
                    <i class="bi bi-chevron-right text-muted small"></i>
                </a>
                <a href="{{ route('fees.index') }}" class="list-group-item list-group-item-action d-flex align-items-center justify-content-between px-0 py-25 border-bottom">
                    <div class="d-flex align-items-center gap-2">
                        <i class="bi bi-receipt text-danger fs-5"></i>
                        <span class="fw-medium text-dark" style="font-size: 0.9rem;">Fee Collection</span>
                    </div>
                    <i class="bi bi-chevron-right text-muted small"></i>
                </a>
                <a href="{{ route('settings.index') }}" class="list-group-item list-group-item-action d-flex align-items-center justify-content-between px-0 py-25">
                    <div class="d-flex align-items-center gap-2">
                        <i class="bi bi-gear text-secondary fs-5"></i>
                        <span class="fw-medium text-dark" style="font-size: 0.9rem;">System Settings</span>
                    </div>
                    <i class="bi bi-chevron-right text-muted small"></i>
                </a>
            </div>
        </x-card>

        <x-card title="Role Architecture" subtitle="Configured user permission foundation">
            <div class="d-flex flex-wrap gap-2">
                <x-badge variant="sage">Superadmin</x-badge>
                <x-badge variant="sage">Principal</x-badge>
                <x-badge variant="sage">Admin</x-badge>
                <x-badge variant="terracotta">Teacher / Guide</x-badge>
                <x-badge variant="info">Student</x-badge>
                <x-badge variant="info">Parent</x-badge>
            </div>
        </x-card>
    </div>
</div>
@endsection
