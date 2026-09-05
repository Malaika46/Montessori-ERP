@extends('layouts.app')

@section('title', 'Principal Dashboard')

@section('content')
<div class="hero-welcome-card">
    <div class="d-flex flex-column flex-lg-row align-items-lg-center justify-content-between gap-4">
        <div>
            <div class="hero-role-pill">
                <i class="bi bi-circle-fill" style="font-size: 0.4rem;"></i> Role: PRINCIPAL &nbsp;•&nbsp; Academic Operations
            </div>
            <h1 class="hero-welcome-title">Welcome back, {{ Auth::user()->name }}</h1>
            <p class="hero-welcome-subtitle">
                Principal Executive Portal • Comprehensive leadership oversight across classrooms, Guides, and progress report releases.
            </p>
        </div>

        <div class="d-flex align-items-center gap-2 flex-wrap">
            <a href="{{ route('curriculum.index') }}" class="btn-pill-gold">
                <i class="bi bi-journal-bookmark-fill"></i> Curriculum Engine
            </a>
            <a href="{{ route('students.index') }}" class="btn-pill-white">
                <i class="bi bi-plus-lg"></i> Manage Students
            </a>
        </div>
    </div>
</div>

<div class="row g-3 mb-4">
    <div class="col-12 col-sm-6 col-xl-3">
        <div class="stat-box-card">
            <div class="stat-box-header">
                <span>Enrolled Students</span>
                <i class="bi bi-mortarboard stat-box-icon"></i>
            </div>
            <div class="stat-box-number">{{ $studentCount ?? 0 }}</div>
            <div class="stat-box-sub">Active student profiles</div>
        </div>
    </div>

    <div class="col-12 col-sm-6 col-xl-3">
        <div class="stat-box-card">
            <div class="stat-box-header">
                <span>Teaching Staff</span>
                <i class="bi bi-person-badge stat-box-icon"></i>
            </div>
            <div class="stat-box-number">0</div>
            <div class="stat-box-sub">Montessori certified Guides</div>
        </div>
    </div>

    <div class="col-12 col-sm-6 col-xl-3">
        <div class="stat-box-card">
            <div class="stat-box-header">
                <span>Classroom Environments</span>
                <i class="bi bi-building stat-box-icon"></i>
            </div>
            <div class="stat-box-number">{{ $classroomCount ?? 0 }}</div>
            <div class="stat-box-sub">Toddler, Casa, & Elementary</div>
        </div>
    </div>

    <div class="col-12 col-sm-6 col-xl-3">
        <div class="stat-box-card">
            <div class="stat-box-header">
                <span>Daily Attendance</span>
                <i class="bi bi-clock-history stat-box-icon"></i>
            </div>
            <div class="stat-box-number">{{ $todayAttendance ?? '0%' }}</div>
            <div class="stat-box-sub">Campus check-in rate</div>
        </div>
    </div>
</div>
@endsection
