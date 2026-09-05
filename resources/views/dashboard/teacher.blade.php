@extends('layouts.app')

@section('title', 'Teacher Dashboard')

@section('content')
<style>
    .teacher-banner {
        background: linear-gradient(135deg, #1c382b 0%, #2a4e3d 100%);
        border-radius: 16px;
        color: #ffffff;
        padding: 2rem;
        box-shadow: 0 10px 25px rgba(28, 56, 43, 0.15);
    }
    .teacher-stat-card {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 14px;
        padding: 1.25rem;
        transition: all 0.2s ease-in-out;
        height: 100%;
    }
    .teacher-stat-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 20px rgba(0,0,0,0.05);
    }
    .quick-action-card {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 12px;
        padding: 1rem 1.25rem;
        text-decoration: none;
        color: #1e293b;
        display: flex;
        align-items: center;
        justify-content: space-between;
        transition: all 0.2s ease;
    }
    .quick-action-card:hover {
        border-color: #1c382b;
        background: #fcfdfc;
        transform: translateX(3px);
        color: #1c382b;
    }
    .alert-concept-box {
        background: #fefce8;
        border: 1px solid #fef08a;
        border-radius: 12px;
        padding: 1rem;
    }
</style>

<div class="d-flex flex-column gap-4">

    <!-- Top Banner Header -->
    <div class="teacher-banner d-flex flex-column flex-lg-row align-items-lg-center justify-content-between gap-3">
        <div>
            <div class="d-flex align-items-center gap-2 mb-2">
                <span class="badge bg-warning text-dark fw-bold rounded-pill px-3 py-1 fs-7">
                    <i class="bi bi-circle-fill text-dark me-1" style="font-size: 0.4rem;"></i> Role: TEACHER
                </span>
                <span class="badge bg-white bg-opacity-20 text-white border border-white border-opacity-25 rounded-pill px-3 py-1 fs-7">
                    Multi-Campus PostgreSQL Isolated
                </span>
            </div>
            <h2 class="fw-bold mb-1 fs-3 text-white">Welcome back, {{ $user->name }}</h2>
            <p class="text-white-50 mb-0 small">
                Montessori Guide Dashboard • Classroom environment schedules, student development paths, and SRS reviews.
            </p>
        </div>

        <div class="d-flex align-items-center gap-2 flex-wrap">
            <button type="button" class="btn btn-warning text-dark rounded-pill px-4 fw-bold shadow-sm" data-bs-toggle="modal" data-bs-target="#teacherAttendanceModal">
                <i class="bi bi-clock-check me-1"></i> Mark Attendance
            </button>
            <a href="{{ route('curriculum.index') }}" class="btn btn-outline-light rounded-pill px-4 fw-bold">
                <i class="bi bi-journal-bookmark me-1"></i> Curriculum Engine
            </a>
            <a href="{{ route('students.index') }}" class="btn btn-light text-dark rounded-pill px-4 fw-bold shadow-sm">
                <i class="bi bi-plus-lg me-1"></i> Manage Students
            </a>
        </div>
    </div>

    <!-- 4 Top Stat Cards -->
    <div class="row g-3">
        <!-- Assigned Classroom / Classrooms Dropdown -->
        <div class="col-md-3">
            <div class="teacher-stat-card">
                <div class="d-flex align-items-center justify-content-between text-muted small fw-bold mb-2">
                    <span>Assigned Environments ({{ $assignedEnvironmentsCount }})</span>
                    <i class="bi bi-door-open text-success fs-5"></i>
                </div>

                @if($assignedClassrooms->count() > 1)
                    <select class="form-select form-select-sm fw-bold border-success-subtle text-dark" id="activeClassroomSelect">
                        @foreach($assignedClassrooms as $cls)
                            <option value="{{ $cls->id }}" {{ $loop->first ? 'selected' : '' }}>
                                {{ $cls->name }} ({{ $cls->age_group }})
                            </option>
                        @endforeach
                    </select>
                @else
                    <h4 class="fw-bold text-dark mb-1">{{ $primaryClassroom ? $primaryClassroom->name : 'Casa dei Bambini' }}</h4>
                    <span class="text-muted fs-8">{{ $primaryClassroom ? $primaryClassroom->age_group : 'Primary (3 - 6 Years)' }}</span>
                @endif
            </div>
        </div>

        <!-- Struggling Students Alert -->
        <div class="col-md-3">
            <div class="teacher-stat-card">
                <div class="d-flex align-items-center justify-content-between text-muted small fw-bold mb-2">
                    <span>Struggling Students Alert</span>
                    <i class="bi bi-exclamation-triangle text-warning fs-5"></i>
                </div>
                <h4 class="fw-bold text-warning mb-1">1 Student</h4>
                <span class="text-muted fs-8">Targeted review recommended</span>
            </div>
        </div>

        <!-- SRS Reviews Scheduled -->
        <div class="col-md-3">
            <div class="teacher-stat-card">
                <div class="d-flex align-items-center justify-content-between text-muted small fw-bold mb-2">
                    <span>SRS Reviews Scheduled</span>
                    <i class="bi bi-clock-history text-info fs-5"></i>
                </div>
                <h4 class="fw-bold text-dark mb-1">4 Cards</h4>
                <span class="text-muted fs-8">Due today across 5 avenues</span>
            </div>
        </div>

        <!-- Observations Logged -->
        <div class="col-md-3">
            <div class="teacher-stat-card">
                <div class="d-flex align-items-center justify-content-between text-muted small fw-bold mb-2">
                    <span>Observations Logged</span>
                    <i class="bi bi-eye text-success fs-5"></i>
                </div>
                <h4 class="fw-bold text-dark mb-1">{{ $observationsCount }}</h4>
                <span class="text-muted fs-8">Guide observation notes</span>
            </div>
        </div>
    </div>

    <!-- Main Section: Quick Actions & Alerts -->
    <div class="row g-4">
        <!-- Quick Actions Grid -->
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm rounded-4 p-4 bg-white">
                <div class="d-flex align-items-center gap-2 mb-3">
                    <i class="bi bi-journal-check text-success fs-4"></i>
                    <div>
                        <h5 class="fw-bold text-dark mb-0">Montessori Guide Quick Actions</h5>
                        <span class="text-muted small">Classroom management and lesson planning tools</span>
                    </div>
                </div>

                <div class="row g-3">
                    <div class="col-md-6">
                        <a href="{{ route('lessons.index') }}" class="quick-action-card">
                            <div>
                                <div class="fw-bold text-dark">Lesson Planning</div>
                                <div class="text-muted fs-8">5 Avenues presentation plans</div>
                            </div>
                            <i class="bi bi-chevron-right text-muted"></i>
                        </a>
                    </div>

                    <div class="col-md-6">
                        <a href="{{ route('observations.index') }}" class="quick-action-card">
                            <div>
                                <div class="fw-bold text-dark">Guide Observations</div>
                                <div class="text-muted fs-8">Log child activity notes</div>
                            </div>
                            <i class="bi bi-chevron-right text-muted"></i>
                        </a>
                    </div>

                    <div class="col-md-6">
                        <a href="{{ route('lms.index') }}" class="quick-action-card">
                            <div>
                                <div class="fw-bold text-dark">Micro-Learning SRS</div>
                                <div class="text-muted fs-8">Monitor student skill tree</div>
                            </div>
                            <i class="bi bi-chevron-right text-muted"></i>
                        </a>
                    </div>

                    <div class="col-md-6">
                        <a href="{{ route('students.index') }}" class="quick-action-card">
                            <div>
                                <div class="fw-bold text-dark">Classroom Roster</div>
                                <div class="text-muted fs-8">Student development paths</div>
                            </div>
                            <i class="bi bi-chevron-right text-muted"></i>
                        </a>
                    </div>

                    <div class="col-md-6">
                        <a href="{{ route('attendance.index') }}" class="quick-action-card">
                            <div>
                                <div class="fw-bold text-dark">Daily Attendance</div>
                                <div class="text-muted fs-8">Mark gate passes</div>
                            </div>
                            <i class="bi bi-chevron-right text-muted"></i>
                        </a>
                    </div>

                    <div class="col-md-6">
                        <a href="{{ route('inventory.index') }}" class="quick-action-card">
                            <div>
                                <div class="fw-bold text-dark">Material Inventory</div>
                                <div class="text-muted fs-8">Check material readiness</div>
                            </div>
                            <i class="bi bi-chevron-right text-muted"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Struggling Concepts Alert Column -->
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm rounded-4 p-4 bg-white h-100">
                <div class="d-flex align-items-center gap-2 mb-3">
                    <i class="bi bi-bullseye text-warning fs-4"></i>
                    <div>
                        <h5 class="fw-bold text-dark mb-0">Struggling Concepts Alert</h5>
                        <span class="text-muted fs-8">Automated SRS performance notifications</span>
                    </div>
                </div>

                <div class="alert-concept-box">
                    <h6 class="fw-bold text-dark mb-1">Pink Tower Size Discrimination</h6>
                    <p class="text-muted fs-8 mb-2">Student: <strong>Maham Mir</strong> • 3 Attempts Failed</p>
                    <span class="badge bg-warning bg-opacity-20 text-dark fw-bold fs-8 rounded-pill px-3 py-1">
                        Targeted physical presentation needed
                    </span>
                </div>
            </div>
        </div>
    <!-- Assigned Classroom Students & Parents Roster -->
    <div class="card border-0 shadow-sm rounded-4 p-4 bg-white mt-4">
        <div class="d-flex align-items-center justify-content-between mb-3 flex-wrap gap-2">
            <div class="d-flex align-items-center gap-2">
                <i class="bi bi-people-fill text-success fs-4"></i>
                <div>
                    <h5 class="fw-bold text-dark mb-0">Enrolled Students & Linked Parents Roster</h5>
                    <span class="text-muted small">Real-time database records for your assigned environments</span>
                </div>
            </div>
            <a href="{{ route('students.index') }}" class="btn btn-outline-success btn-sm rounded-pill px-3 fw-bold">
                <i class="bi bi-arrow-right-circle me-1"></i> Full Student Directory
            </a>
        </div>

        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light">
                    <tr class="text-uppercase text-muted fs-8">
                        <th>Student Name</th>
                        <th>Student ID</th>
                        <th>Classroom / Environment</th>
                        <th>Linked Parent(s)</th>
                        <th>Status</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($students as $st)
                        <tr>
                            <td>
                                <div class="d-flex align-items-center gap-2">
                                    <div class="avatar-circle bg-success text-white fw-bold d-flex align-items-center justify-content-center rounded-circle" style="width:36px; height:36px; font-size: 0.85rem;">
                                        {{ strtoupper(substr($st->user->name ?? 'S', 0, 2)) }}
                                    </div>
                                    <div>
                                        <div class="fw-bold text-dark">{{ $st->user->name ?? 'Student' }}</div>
                                        <span class="text-muted fs-8">{{ $st->user->email ?? 'No email' }}</span>
                                    </div>
                                </div>
                            </td>
                            <td><span class="badge bg-light text-dark font-monospace fs-8">{{ $st->student_number ?? 'STU-'.$st->id }}</span></td>
                            <td>
                                <span class="badge bg-success bg-opacity-10 text-success fw-bold fs-8 rounded-pill px-3 py-1">
                                    <i class="bi bi-door-open me-1"></i>{{ $st->classroom->name ?? 'Primary Casa' }}
                                </span>
                            </td>
                            <td>
                                @if($st->parents->count() > 0)
                                    @foreach($st->parents as $pr)
                                        <div class="d-flex align-items-center gap-1 mb-1">
                                            <i class="bi bi-person-heart text-danger fs-7"></i>
                                            <span class="fw-bold text-dark fs-8">{{ $pr->user->name ?? 'Parent' }}</span>
                                            <span class="text-muted fs-8">({{ $pr->phone ?? 'Contact Linked' }})</span>
                                        </div>
                                    @endforeach
                                @else
                                    <span class="text-muted fs-8 italic">Parent linked via profile</span>
                                @endif
                            </td>
                            <td>
                                <span class="badge bg-success border border-success-subtle text-white rounded-pill px-3 py-1 fs-8">
                                    ACTIVE
                                </span>
                            </td>
                            <td class="text-end">
                                <div class="btn-group btn-group-sm">
                                    <a href="{{ route('students.show', $st->id) }}" class="btn btn-light text-dark rounded-pill px-3 fw-bold shadow-xs">
                                        <i class="bi bi-person-lines-fill me-1"></i> Profile
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-4 text-muted">
                                <i class="bi bi-inbox fs-2 d-block mb-2 text-muted"></i>
                                No active students found in your assigned classrooms.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>

<!-- Modal for Teacher Self Attendance -->
<div class="modal fade" id="teacherAttendanceModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4">
            <div class="modal-header bg-dark text-white rounded-top-4">
                <h5 class="modal-title fw-bold"><i class="bi bi-clock-check me-2 text-warning"></i> Teacher Attendance Mark</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4 text-center">
                <div class="p-3 bg-light rounded-circle d-inline-block mb-3" style="width: 80px; height: 80px;">
                    <i class="bi bi-person-badge text-success fs-1"></i>
                </div>
                <h5 class="fw-bold text-dark mb-1">{{ $user->name }}</h5>
                <p class="text-muted small mb-3">Role: Lead Directress • Campus Primary</p>

                <div class="card bg-light border-0 p-3 rounded-3 mb-3 text-start">
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted small">Current Date:</span>
                        <strong class="text-dark small">{{ date('F d, Y') }}</strong>
                    </div>
                    <div class="d-flex justify-content-between">
                        <span class="text-muted small">Check-In Status:</span>
                        <span class="badge bg-success-subtle text-success border border-success-subtle px-3 py-1 rounded-pill fw-bold">PRESENT - ON TIME</span>
                    </div>
                </div>

                <div class="alert alert-success border-0 rounded-3 small text-start mb-0">
                    <i class="bi bi-check-circle-fill me-1"></i> Attendance registered for today at {{ date('h:i A') }}. Have a great teaching session!
                </div>
            </div>
            <div class="modal-footer border-0 bg-light rounded-bottom-4">
                <button type="button" class="btn btn-dark rounded-pill px-4 fw-bold" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
@endsection
