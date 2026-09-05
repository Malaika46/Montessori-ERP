@extends('layouts.app')

@section('title', 'Parent Portal Dashboard')

@section('content')
<style>
    .parent-tab-pill {
        color: #334155 !important;
        background-color: #f8fafc !important;
        border: 1px solid #e2e8f0 !important;
        border-radius: 12px !important;
        padding: 0.55rem 1rem !important;
        font-weight: 600 !important;
        font-size: 0.875rem !important;
        transition: all 0.2s ease-in-out !important;
        display: inline-flex !important;
        align-items: center !important;
        gap: 0.4rem !important;
    }
    .parent-tab-pill:hover {
        background-color: #f1f5f9 !important;
        color: #0f172a !important;
        border-color: #cbd5e1 !important;
    }
    .parent-tab-pill.active {
        background-color: #1c382b !important;
        color: #ffffff !important;
        border-color: #1c382b !important;
        box-shadow: 0 4px 12px rgba(28, 56, 43, 0.22) !important;
    }
    .parent-tab-pill .tab-badge {
        background-color: #e2e8f0;
        color: #475569;
        font-size: 0.75rem;
        padding: 0.15rem 0.5rem;
        border-radius: 50rem;
        font-weight: 700;
        transition: all 0.2s ease-in-out;
    }
    .parent-tab-pill.active .tab-badge {
        background-color: rgba(255, 255, 255, 0.25);
        color: #ffffff;
    }
    .parent-tab-container {
        background: #ffffff;
        border-radius: 16px;
        padding: 0.5rem;
        border: 1px solid #e2e8f0;
        box-shadow: 0 2px 8px rgba(0,0,0,0.04);
        display: flex;
        flex-wrap: wrap;
        gap: 0.5rem;
    }
</style>

<div class="container-fluid p-0">

    <!-- Page Header Title -->
    <div class="mb-4">
        <h3 class="fw-bold text-dark mb-1">Parent Portal Dashboard</h3>
        <p class="text-muted small mb-0">Monitor your child's classroom progress, daily attendance, and released milestone observations.</p>
    </div>

    @php
        $childName = $child->user->name ?? 'Malaika Muneer';
        $childId = $child->student_number ?? 'STU-3662';
        $classroomName = $child->classroom->name ?? 'Toddler Community';
        $directressName = $child->classroom->teachers->first()->user->name ?? 'Sehar';
        $dob = $child->date_of_birth ? $child->date_of_birth->format('Y-m-d') : '2004-06-28';
        $gender = strtoupper($child->gender ?? 'FEMALE');
        $status = strtoupper($child->status ?? 'ACTIVE');
    @endphp

    <!-- Dark Green Banner Card (Matching Screenshot) -->
    <div class="card border-0 shadow-sm rounded-4 text-white mb-4" style="background-color: #1c382b;">
        <div class="card-body p-4">
            <div class="d-flex align-items-center gap-2 mb-2">
                <span class="badge bg-success text-white border border-success-subtle text-uppercase extra-small fw-bold px-2 py-1" style="font-size: 0.7rem; letter-spacing: 0.05rem;">
                    <i class="bi bi-circle-fill text-success-subtle me-1" style="font-size: 0.5rem;"></i> VERIFIED PARENT ACCOUNT • Montessori System
                </span>
            </div>
            <h2 class="fw-bold text-white mb-1">
                <i class="bi bi-heart-fill text-danger me-2"></i> {{ $childName }}
            </h2>
            <div class="text-light opacity-75 small font-monospace">
                Student ID: <strong>{{ $childId }}</strong> &nbsp;•&nbsp; 
                Classroom: <strong>{{ $classroomName }}</strong> &nbsp;•&nbsp; 
                Directress: <strong>{{ $directressName }}</strong>
            </div>
        </div>
    </div>

    <!-- Navigation Tabs Bar (Montessori Theme Styled) -->
    <div class="mb-4">
        <ul class="nav nav-pills parent-tab-container" id="parentPortalTabs" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active parent-tab-pill" id="overview-tab" data-bs-toggle="pill" data-bs-target="#tab-overview" type="button" role="tab">
                    <i class="bi bi-person-bounding-box"></i> Child Overview
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link parent-tab-pill" id="attendance-tab" data-bs-toggle="pill" data-bs-target="#tab-attendance" type="button" role="tab">
                    <i class="bi bi-calendar-check"></i> Daily Attendance <span class="tab-badge">1</span>
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link parent-tab-pill" id="observations-tab" data-bs-toggle="pill" data-bs-target="#tab-observations" type="button" role="tab">
                    <i class="bi bi-eye"></i> Observations <span class="tab-badge">1</span>
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link parent-tab-pill" id="assessments-tab" data-bs-toggle="pill" data-bs-target="#tab-assessments" type="button" role="tab">
                    <i class="bi bi-file-earmark-bar-graph"></i> Assessment Reports <span class="tab-badge">2</span>
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link parent-tab-pill" id="fees-tab" data-bs-toggle="pill" data-bs-target="#tab-fees" type="button" role="tab">
                    <i class="bi bi-receipt"></i> Fee / Payments <span class="tab-badge">0</span>
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link parent-tab-pill" id="notices-tab" data-bs-toggle="pill" data-bs-target="#tab-notices" type="button" role="tab">
                    <i class="bi bi-megaphone"></i> School Notices <span class="tab-badge">{{ $notices->count() }}</span>
                </button>
            </li>
        </ul>
    </div>

    <!-- Tab Contents Container -->
    <div class="tab-content" id="parentPortalTabContent">
        
        <!-- 1. CHILD OVERVIEW TAB -->
        <div class="tab-pane fade show active" id="tab-overview" role="tabpanel">
            <div class="row g-4">
                <!-- Child Record Details Card -->
                <div class="col-12 col-md-6">
                    <div class="card border-0 shadow-sm rounded-4 h-100">
                        <div class="card-header bg-white border-bottom p-3">
                            <h6 class="fw-bold text-dark mb-0">
                                <i class="bi bi-person-lines-fill text-success me-2"></i> Child Record Details
                            </h6>
                        </div>
                        <div class="card-body p-3">
                            <div class="d-flex justify-content-between py-2 border-bottom">
                                <span class="text-muted small">Full Name:</span>
                                <span class="fw-bold text-dark small">{{ $childName }}</span>
                            </div>
                            <div class="d-flex justify-content-between py-2 border-bottom">
                                <span class="text-muted small">Student ID:</span>
                                <span class="font-monospace fw-bold text-dark small">{{ $childId }}</span>
                            </div>
                            <div class="d-flex justify-content-between py-2 border-bottom">
                                <span class="text-muted small">Date of Birth:</span>
                                <span class="fw-semibold text-dark small">{{ $dob }}</span>
                            </div>
                            <div class="d-flex justify-content-between py-2 border-bottom">
                                <span class="text-muted small">Gender:</span>
                                <span class="fw-semibold text-dark small">{{ $gender }}</span>
                            </div>
                            <div class="d-flex justify-content-between py-2">
                                <span class="text-muted small">Enrollment Status:</span>
                                <span class="badge bg-success-subtle text-success border border-success-subtle px-2 py-1">{{ $status }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Classroom Environment Card -->
                <div class="col-12 col-md-6">
                    <div class="card border-0 shadow-sm rounded-4 h-100">
                        <div class="card-header bg-white border-bottom p-3">
                            <h6 class="fw-bold text-dark mb-0">
                                <i class="bi bi-door-open-fill text-success me-2"></i> Classroom Environment
                            </h6>
                        </div>
                        <div class="card-body p-3">
                            <div class="p-3 bg-light rounded-3 mb-2 border">
                                <h6 class="fw-bold text-dark mb-1">{{ $classroomName }}</h6>
                                <div class="extra-small text-muted mb-2">Code: TODD-01</div>
                                <div class="d-flex justify-content-between text-muted small">
                                    <span>Environment:</span>
                                    <span class="fw-bold text-dark">Toddler Community (1.5-3 yrs)</span>
                                </div>
                                <div class="d-flex justify-content-between text-muted small mt-1">
                                    <span>Lead Directress:</span>
                                    <span class="fw-bold text-success">{{ $directressName }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Access Permissions & Controls Card -->
                <div class="col-12">
                    <div class="card border-0 shadow-sm rounded-4">
                        <div class="card-header bg-white border-bottom p-3">
                            <h6 class="fw-bold text-dark mb-0">
                                <i class="bi bi-shield-check text-success me-2"></i> Child Portal Access Permissions & Controls
                            </h6>
                            <p class="text-muted extra-small mb-0">Parent-controlled security permission for student report card visibility</p>
                        </div>
                        <div class="card-body p-4">
                            <div class="p-3 bg-success-subtle border border-success-subtle rounded-4 d-flex align-items-center justify-content-between">
                                <div>
                                    <div class="fw-bold text-dark mb-1">
                                        Allow child to view assessment reports 
                                        <span class="badge bg-success text-white ms-2 extra-small">ENABLED</span>
                                    </div>
                                    <div class="small text-muted">
                                        When enabled, {{ $childName }} can view released assessment report cards in their Student Portal. When disabled, report access is strictly restricted to parent accounts.
                                    </div>
                                </div>
                                <div class="form-check form-switch fs-3 ms-3">
                                    <input class="form-check-input" type="checkbox" role="switch" id="allowChildReportToggle" checked>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Recent School Notice Card -->
                <div class="col-12">
                    <div class="card border-0 shadow-sm rounded-4">
                        <div class="card-header bg-white border-bottom p-3">
                            <h6 class="fw-bold text-dark mb-0">
                                <i class="bi bi-chat-left-text-fill text-success me-2"></i> Recent School Notice
                            </h6>
                        </div>
                        <div class="card-body p-3">
                            @forelse($notices->take(1) as $notice)
                                <div class="p-3 bg-light rounded-3 border">
                                    <div class="fw-bold text-dark mb-1">Re: {{ $notice->subject }}</div>
                                    <div class="text-muted small">{{ $notice->message }}</div>
                                    <div class="extra-small text-muted mt-2 font-monospace">
                                        Posted: {{ $notice->created_at ? $notice->created_at->format('M d, Y') : 'Today' }}
                                    </div>
                                </div>
                            @empty
                                <div class="p-3 bg-light rounded-3 border">
                                    <div class="fw-bold text-dark mb-1">Re: Welcome to Montessori ERP</div>
                                    <div class="text-muted small">Important school update: Semester 1 progress reports released.</div>
                                </div>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- 2. DAILY ATTENDANCE TAB -->
        <div class="tab-pane fade" id="tab-attendance" role="tabpanel">
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-header bg-white border-bottom p-3">
                    <h6 class="fw-bold text-dark mb-0">
                        <i class="bi bi-clock-history text-success me-2"></i> Daily Check-in & Gate Pass Logs
                    </h6>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="bg-light border-bottom">
                            <tr class="text-uppercase text-muted extra-small fw-bold">
                                <th class="ps-4">Date</th>
                                <th>Check-in Time</th>
                                <th>Check-out Time</th>
                                <th>Attendance Status</th>
                                <th>Gate Pass Verification</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td class="ps-4 font-monospace fw-bold">{{ date('Y-m-d') }}</td>
                                <td class="small">08:15 AM</td>
                                <td class="small">01:30 PM</td>
                                <td>
                                    <span class="badge bg-success-subtle text-success border border-success-subtle px-2 py-1">
                                        <i class="bi bi-check-circle-fill me-1"></i> PRESENT
                                    </span>
                                </td>
                                <td>
                                    <span class="badge bg-info-subtle text-info border border-info-subtle px-2 py-1">
                                        <i class="bi bi-qr-code me-1"></i> Verified QR Pickup
                                    </span>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- 3. OBSERVATIONS TAB -->
        <div class="tab-pane fade" id="tab-observations" role="tabpanel">
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-header bg-white border-bottom p-3">
                    <h6 class="fw-bold text-dark mb-0">
                        <i class="bi bi-eye-fill text-success me-2"></i> Montessori Material Mastery Observations
                    </h6>
                </div>
                <div class="card-body p-3">
                    <div class="p-3 bg-light border rounded-4 mb-3">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span class="badge bg-success text-white text-uppercase px-2 py-1">Mastered</span>
                            <span class="extra-small text-muted font-monospace">{{ date('M d, Y') }}</span>
                        </div>
                        <h6 class="fw-bold text-dark mb-1">Practical Life & Sensorial Work</h6>
                        <p class="small text-muted mb-2">Work: <strong>Pink Tower & Cylinder Blocks</strong></p>
                        <div class="p-2 bg-white rounded border small text-secondary">
                            "{{ $childName }} demonstrated deep concentration, self-correction, and mastery over spatial ordering."
                        </div>
                        <div class="extra-small text-muted mt-2">Recorded by Directress: <strong>{{ $directressName }}</strong></div>
                    </div>
                </div>
            </div>
        </div>

        <!-- 4. ASSESSMENT REPORTS TAB -->
        <div class="tab-pane fade" id="tab-assessments" role="tabpanel">
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-header bg-white border-bottom p-3">
                    <h6 class="fw-bold text-dark mb-0">
                        <i class="bi bi-file-earmark-bar-graph-fill text-success me-2"></i> Released Narrative Progress Reports
                    </h6>
                </div>
                <div class="card-body p-3">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <div class="card border rounded-4 p-3 bg-white h-100 shadow-sm">
                                <div class="d-flex justify-content-between mb-2">
                                    <span class="badge bg-primary-subtle text-primary border border-primary-subtle px-2 py-1">RELEASED</span>
                                    <span class="extra-small text-muted">Term 1</span>
                                </div>
                                <h6 class="fw-bold text-dark mb-1">Mid-Year Narrative Progress Report</h6>
                                <p class="small text-muted mb-3">Comprehensive evaluation of Sensorial, Practical Life, and Language areas.</p>
                                <button class="btn btn-sm btn-outline-success w-100 rounded-3">
                                    <i class="bi bi-download me-1"></i> View Report Card (PDF)
                                </button>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="card border rounded-4 p-3 bg-white h-100 shadow-sm">
                                <div class="d-flex justify-content-between mb-2">
                                    <span class="badge bg-success-subtle text-success border border-success-subtle px-2 py-1">FINALIZED</span>
                                    <span class="extra-small text-muted">Term 2</span>
                                </div>
                                <h6 class="fw-bold text-dark mb-1">Developmental Rubric Summary</h6>
                                <p class="small text-muted mb-3">Social independence, motor skill refinement, and mathematical readiness.</p>
                                <button class="btn btn-sm btn-outline-success w-100 rounded-3">
                                    <i class="bi bi-download me-1"></i> View Report Card (PDF)
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- 5. FEE / PAYMENTS TAB -->
        <div class="tab-pane fade" id="tab-fees" role="tabpanel">
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-header bg-white border-bottom p-3">
                    <h6 class="fw-bold text-dark mb-0">
                        <i class="bi bi-receipt text-success me-2"></i> Student Fee Vouchers & Payment History (PKR)
                    </h6>
                </div>
                <div class="card-body p-4 text-center">
                    <div class="p-4 bg-light rounded-4 border d-inline-block text-center" style="max-width: 400px;">
                        <i class="bi bi-check-circle-fill text-success fs-1 mb-2 d-block"></i>
                        <h6 class="fw-bold text-dark">No Outstanding Fee Balance</h6>
                        <p class="small text-muted mb-3">All tuition vouchers and activity fees for PKR are fully cleared for {{ date('F Y') }}.</p>
                        <a href="{{ route('fees.index') }}" class="btn btn-m-primary btn-sm">View Fee Ledger</a>
                    </div>
                </div>
            </div>
        </div>

        <!-- 6. SCHOOL NOTICES TAB -->
        <div class="tab-pane fade" id="tab-notices" role="tabpanel">
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-header bg-white border-bottom p-3">
                    <h6 class="fw-bold text-dark mb-0">
                        <i class="bi bi-megaphone-fill text-success me-2"></i> Campus Broadcasts & School Notices
                    </h6>
                </div>
                <div class="card-body p-3">
                    @forelse($notices as $notice)
                        <div class="p-3 bg-white rounded-3 border mb-3 shadow-sm">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <span class="badge bg-info-subtle text-info border border-info-subtle px-2 py-1 text-uppercase">
                                    <i class="bi bi-broadcast me-1"></i> {{ $notice->audience_type ?? 'All Parents' }}
                                </span>
                                <span class="extra-small text-muted font-monospace">
                                    {{ $notice->created_at ? $notice->created_at->format('M d, Y h:i A') : 'Today' }}
                                </span>
                            </div>
                            <h6 class="fw-bold text-dark mb-1">{{ $notice->subject }}</h6>
                            <p class="text-secondary small mb-0">{{ $notice->message }}</p>
                        </div>
                    @empty
                        <div class="text-center py-4 text-muted">
                            <i class="bi bi-inbox fs-2 mb-2 d-block"></i>
                            No official announcements broadcasted yet.
                        </div>
                    @endforelse
                </div>
            </div>
        </div>

    </div>
</div>
@endsection
