@extends('layouts.app')

@section('title', 'Admin Dashboard')

@section('content')
<div class="hero-welcome-card">
    <div class="d-flex flex-column flex-lg-row align-items-lg-center justify-content-between gap-4">
        <div>
            <div class="hero-role-pill">
                <i class="bi bi-circle-fill" style="font-size: 0.4rem;"></i> Role: ADMIN &nbsp;•&nbsp; Campus Operations
            </div>
            <h1 class="hero-welcome-title">Welcome back, {{ Auth::user()->name }}</h1>
            <p class="hero-welcome-subtitle">
                Administrative Control Panel • Admissions, attendance gate pass logs, fee challans, and campus inventory.
            </p>
        </div>

        <div class="d-flex align-items-center gap-2 flex-wrap">
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
            <div class="stat-box-sub">Active profiles</div>
        </div>
    </div>

    <div class="col-12 col-sm-6 col-xl-3">
        <div class="stat-box-card">
            <div class="stat-box-header">
                <span>Attendance Rate</span>
                <i class="bi bi-clock-history stat-box-icon"></i>
            </div>
            <div class="stat-box-number">{{ $todayAttendance ?? '0%' }}</div>
            <div class="stat-box-sub">Check-in today</div>
        </div>
    </div>

    <div class="col-12 col-sm-6 col-xl-3">
        <div class="stat-box-card">
            <div class="stat-box-header">
                <span>Pending Fee Challans</span>
                <i class="bi bi-receipt stat-box-icon"></i>
            </div>
            <div class="stat-box-number">0</div>
            <div class="stat-box-sub">Tuition vouchers</div>
        </div>
    </div>

    <div class="col-12 col-sm-6 col-xl-3">
        <div class="stat-box-card">
            <div class="stat-box-header">
                <span>Inventory Items</span>
                <i class="bi bi-box-seam stat-box-icon"></i>
            </div>
            <div class="stat-box-number">0</div>
            <div class="stat-box-sub">Cataloged materials</div>
        </div>
    </div>
</div>
@endsection
