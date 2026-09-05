@extends('layouts.app')

@section('title', 'Student Portal')

@section('content')
<style>
    /* Custom Student Dashboard Styling matching screenshot */
    .student-header-banner {
        background: linear-gradient(135deg, #1b3223 0%, #12281d 100%);
        border-radius: 16px;
        padding: 28px;
        color: #ffffff;
        border: 1px solid #2a4c36;
        box-shadow: 0 8px 24px rgba(0, 0, 0, 0.12);
        margin-bottom: 24px;
    }
    .student-badge-pill {
        background: rgba(212, 175, 55, 0.15);
        color: #d4af37;
        border: 1px solid rgba(212, 175, 55, 0.3);
        font-weight: 700;
        font-size: 0.75rem;
        padding: 4px 12px;
        border-radius: 20px;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        display: inline-block;
        margin-bottom: 10px;
    }
    .stat-pill-dark {
        background: rgba(0, 0, 0, 0.35);
        border: 1px solid rgba(255, 255, 255, 0.15);
        border-radius: 12px;
        padding: 8px 18px;
        text-align: center;
        min-width: 90px;
    }
    .stat-pill-dark .val {
        font-size: 1.25rem;
        font-weight: 800;
        color: #d4af37;
        line-height: 1.2;
    }
    .stat-pill-dark .lbl {
        font-size: 0.65rem;
        text-transform: uppercase;
        color: rgba(255, 255, 255, 0.7);
        font-weight: 600;
    }
    .student-nav-tabs .nav-link {
        border-radius: 30px;
        padding: 8px 20px;
        font-weight: 600;
        font-size: 0.88rem;
        color: #4a5568;
        border: 1px solid transparent;
        background-color: #ffffff;
        box-shadow: 0 2px 6px rgba(0,0,0,0.03);
        transition: all 0.2s ease;
    }
    .student-nav-tabs .nav-link:hover {
        background-color: #f1f5f9;
        color: #1b3223;
    }
    .student-nav-tabs .nav-link.active {
        background-color: #1b3223 !important;
        color: #ffffff !important;
        border-color: #1b3223 !important;
        box-shadow: 0 4px 12px rgba(27, 50, 35, 0.25);
    }

    /* Attendance Calendar Grid */
    .calendar-grid {
        display: grid;
        grid-template-columns: repeat(7, 1fr);
        gap: 8px;
    }
    .calendar-day-header {
        text-align: center;
        font-weight: 700;
        font-size: 0.75rem;
        color: #718096;
        text-transform: uppercase;
        padding-bottom: 6px;
    }
    .calendar-day-cell {
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        border-radius: 8px;
        padding: 10px;
        min-height: 65px;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        position: relative;
    }
    .calendar-day-cell.present {
        background: #f0fdf4;
        border-color: #bbf7d0;
    }
    .calendar-day-cell.absent {
        background: #fef2f2;
        border-color: #fecaca;
    }
    .calendar-day-cell.late {
        background: #fffbe6;
        border-color: #ffe58f;
    }
    .calendar-day-cell .day-num {
        font-weight: 700;
        font-size: 0.85rem;
        color: #2d3748;
    }
    .calendar-day-cell .status-badge {
        font-size: 0.68rem;
        font-weight: 700;
        padding: 2px 6px;
        border-radius: 4px;
        text-align: center;
    }

    /* Memory Match Cards */
    .memory-card {
        aspect-ratio: 1;
        background-color: #1b3223;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #d4af37;
        font-size: 1.5rem;
        cursor: pointer;
        user-select: none;
        transition: transform 0.3s ease, background-color 0.3s;
        box-shadow: 0 4px 10px rgba(0,0,0,0.1);
    }
    .memory-card.flipped {
        background-color: #ffffff;
        color: #1b3223;
        border: 2px solid #d4af37;
        transform: rotateY(180deg);
    }
    .memory-card.matched {
        background-color: #dcfce7;
        color: #15803d;
        border: 2px solid #22c55e;
        cursor: default;
    }
</style>

<!-- Top Header Banner -->
<div class="student-header-banner">
    <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3">
        <div>
            <div class="student-badge-pill">
                ● STUDENT PORTAL • {{ $student->campus ? $student->campus->name : 'Montessori Campus' }}
            </div>
            <h2 class="fw-bold text-white mb-1">
                <i class="bi bi-mortarboard-fill text-warning me-2"></i> {{ $studentName }}
            </h2>
            <div class="small text-white-50">
                Student ID: <strong class="text-warning">{{ $student->student_number }}</strong> &nbsp;•&nbsp; 
                Environment: <strong class="text-white">{{ $assignedClassroom ? $assignedClassroom->name : 'Unassigned' }}</strong>
            </div>
        </div>

        <div class="d-flex align-items-center gap-2">
            <div class="stat-pill-dark">
                <div class="lbl">LEVEL</div>
                <div class="val">1</div>
            </div>
            <div class="stat-pill-dark">
                <div class="lbl">XP POINTS</div>
                <div class="val" id="userXpPoints">250</div>
            </div>
            <div class="stat-pill-dark">
                <div class="lbl">STREAK 🔥</div>
                <div class="val">5 Days</div>
            </div>
        </div>
    </div>
</div>

<!-- Tab Navigation Bar -->
<ul class="nav nav-pills student-nav-tabs gap-2 mb-4 flex-wrap" id="studentTab" role="tablist">
    <li class="nav-item" role="presentation">
        <button class="nav-link active" id="overview-tab" data-bs-toggle="tab" data-bs-target="#tab-overview" type="button" role="tab">
            <i class="bi bi-person-badge me-1"></i> Overview
        </button>
    </li>
    <li class="nav-item" role="presentation">
        <button class="nav-link" id="classroom-tab" data-bs-toggle="tab" data-bs-target="#tab-classroom" type="button" role="tab">
            <i class="bi bi-door-open me-1"></i> My Classroom
        </button>
    </li>
    <li class="nav-item" role="presentation">
        <button class="nav-link" id="lessons-tab" data-bs-toggle="tab" data-bs-target="#tab-lessons" type="button" role="tab">
            <i class="bi bi-journal-bookmark me-1"></i> Assigned Lessons <span class="badge bg-secondary ms-1">{{ $assignedLessons->count() }}</span>
        </button>
    </li>
    <li class="nav-item" role="presentation">
        <button class="nav-link" id="observations-tab" data-bs-toggle="tab" data-bs-target="#tab-observations" type="button" role="tab">
            <i class="bi bi-eye me-1"></i> Released Observations <span class="badge bg-secondary ms-1">{{ $observations->count() }}</span>
        </button>
    </li>
    <li class="nav-item" role="presentation">
        <button class="nav-link" id="lms-tab" data-bs-toggle="tab" data-bs-target="#tab-lms" type="button" role="tab">
            <i class="bi bi-controller me-1"></i> Gamified LMS
        </button>
    </li>
    <li class="nav-item" role="presentation">
        <button class="nav-link" id="reports-tab" data-bs-toggle="tab" data-bs-target="#tab-reports" type="button" role="tab">
            <i class="bi bi-file-earmark-check me-1"></i> Released Reports <span class="badge bg-secondary ms-1">{{ $assessments->count() }}</span>
        </button>
    </li>
</ul>

<!-- Tab Contents -->
<div class="tab-content" id="studentTabContent">

    <!-- TAB 1: OVERVIEW -->
    <div class="tab-pane fade show active" id="tab-overview" role="tabpanel">
        <div class="row g-4">
            <!-- Student Profile Information -->
            <div class="col-12 col-lg-4">
                <div class="card border-0 shadow-sm rounded-4 h-100 bg-white p-4">
                    <h5 class="fw-bold text-dark mb-3"><i class="bi bi-person-fill text-success me-2"></i> Student Profile Information</h5>
                    <table class="table table-borderless table-sm mb-0">
                        <tbody>
                            <tr>
                                <td class="text-muted small">Full Name:</td>
                                <td class="fw-bold text-dark text-end">{{ $studentName }}</td>
                            </tr>
                            <tr>
                                <td class="text-muted small">Student Number:</td>
                                <td class="fw-bold text-dark text-end">{{ $student->student_number }}</td>
                            </tr>
                            <tr>
                                <td class="text-muted small">Account Email:</td>
                                <td class="fw-semibold text-primary text-end small">{{ $user->email }}</td>
                            </tr>
                            <tr>
                                <td class="text-muted small">Date of Birth:</td>
                                <td class="fw-bold text-dark text-end">{{ $student->date_of_birth ? $student->date_of_birth->format('Y-m-d') : 'N/A' }}</td>
                            </tr>
                            <tr>
                                <td class="text-muted small">Gender:</td>
                                <td class="fw-bold text-dark text-end">{{ $student->gender ?? 'N/A' }}</td>
                            </tr>
                            <tr>
                                <td class="text-muted small">Academic Status:</td>
                                <td class="text-end">
                                    <span class="badge bg-success-subtle text-success border border-success-subtle px-2 py-1">ACTIVE</span>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                    <div class="alert alert-light border small text-muted mt-3 mb-0">
                        <i class="bi bi-info-circle me-1"></i> Restricted academic fields are read-only and managed by administrative staff.
                    </div>
                </div>
            </div>

            <!-- Assigned Environment -->
            <div class="col-12 col-lg-4">
                <div class="card border-0 shadow-sm rounded-4 h-100 bg-white p-4">
                    <h5 class="fw-bold text-dark mb-3"><i class="bi bi-door-open-fill text-primary me-2"></i> Assigned Environment</h5>
                    @if($assignedClassroom)
                        <div class="p-3 bg-light rounded-3 border mb-3">
                            <h6 class="fw-bold text-dark mb-1">{{ $assignedClassroom->name }}</h6>
                            <div class="small text-muted mb-2">Age Group: {{ $assignedClassroom->age_group ?? '3 - 6 Years' }}</div>
                            <div class="d-flex align-items-center gap-2">
                                <span class="badge bg-primary-subtle text-primary border border-primary-subtle">
                                    Capacity: {{ $assignedClassroom->capacity ?? 25 }} Students
                                </span>
                                <span class="badge bg-secondary-subtle text-dark border">
                                    Room {{ $assignedClassroom->room_number ?? '101' }}
                                </span>
                            </div>
                        </div>

                        <div class="small fw-semibold text-muted mb-2">Lead Directress:</div>
                        <div class="d-flex align-items-center gap-2 p-2 border rounded-3 bg-white mb-3">
                            <div class="rounded-circle bg-success text-white d-flex align-items-center justify-content-center fw-bold" style="width: 36px; height: 36px;">
                                {{ substr($assignedClassroom->leadTeacher ? $assignedClassroom->leadTeacher->name : 'Staff', 0, 1) }}
                            </div>
                            <div>
                                <div class="fw-bold text-dark small">{{ $assignedClassroom->leadTeacher ? $assignedClassroom->leadTeacher->name : 'Assigned Lead Directress' }}</div>
                                <div class="text-muted" style="font-size: 0.72rem;">Montessori Certified Guide</div>
                            </div>
                        </div>

                        <div class="d-flex justify-content-between align-items-center small text-muted">
                            <span>Classroom Peers:</span>
                            <span class="fw-bold text-dark">{{ $classPeers->count() + 1 }} Enrolled</span>
                        </div>
                    @else
                        <div class="text-center py-4 text-muted">
                            <i class="bi bi-door-closed fs-1 mb-2 d-block text-secondary"></i>
                            <h6 class="fw-bold text-dark">No Classroom Assigned</h6>
                            <p class="small">You are not currently assigned to a classroom environment.</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- School Announcements -->
            <div class="col-12 col-lg-4">
                <div class="card border-0 shadow-sm rounded-4 h-100 bg-white p-4">
                    <h5 class="fw-bold text-dark mb-3"><i class="bi bi-megaphone-fill text-warning me-2"></i> School Announcements</h5>
                    <div class="d-flex flex-column gap-2">
                        @forelse($notices as $n)
                            <div class="p-3 bg-light rounded-3 border-start border-3 border-warning">
                                <div class="fw-bold text-dark small">{{ $n->title }}</div>
                                <div class="text-muted small line-clamp-2 my-1" style="font-size: 0.8rem;">{{ $n->content ?? $n->message }}</div>
                                <div class="text-muted" style="font-size: 0.7rem;">{{ $n->created_at ? $n->created_at->format('Y-m-d') : date('Y-m-d') }}</div>
                            </div>
                        @empty
                            <div class="text-center py-3 text-muted small">No announcements posted today.</div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- TAB 2: MY CLASSROOM & ATTENDANCE CALENDAR -->
    <div class="tab-pane fade" id="tab-classroom" role="tabpanel">
        <!-- Attendance Stats Row -->
        <div class="row g-3 mb-4">
            <div class="col-6 col-md-3">
                <div class="card border-0 shadow-sm rounded-3 p-3 bg-white border-start border-4 border-success">
                    <div class="small text-muted fw-semibold">Present Days</div>
                    <div class="fs-3 fw-bold text-success my-1">{{ $presentCount }} Days</div>
                    <div class="small text-muted"><i class="bi bi-check-circle-fill text-success me-1"></i> Recorded On Time</div>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="card border-0 shadow-sm rounded-3 p-3 bg-white border-start border-4 border-danger">
                    <div class="small text-muted fw-semibold">Absent Days</div>
                    <div class="fs-3 fw-bold text-danger my-1">{{ $absentCount }} Days</div>
                    <div class="small text-muted"><i class="bi bi-x-circle-fill text-danger me-1"></i> Notified Absences</div>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="card border-0 shadow-sm rounded-3 p-3 bg-white border-start border-4 border-warning">
                    <div class="small text-muted fw-semibold">Late / Tardy</div>
                    <div class="fs-3 fw-bold text-warning my-1">{{ $lateCount }} Days</div>
                    <div class="small text-muted"><i class="bi bi-clock-history text-warning me-1"></i> Tardy Check-in</div>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="card border-0 shadow-sm rounded-3 p-3 bg-white border-start border-4 border-primary">
                    <div class="small text-muted fw-semibold">Attendance Rate</div>
                    <div class="fs-3 fw-bold text-primary my-1">{{ $attendanceRate }}%</div>
                    <div class="small text-muted"><i class="bi bi-graph-up-arrow text-primary me-1"></i> Cumulative Rate</div>
                </div>
            </div>
        </div>

        <!-- Attendance Calendar Section -->
        <div class="card border-0 shadow-sm rounded-4 bg-white p-4 mb-4">
            <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
                <div>
                    <h5 class="fw-bold text-dark mb-0"><i class="bi bi-calendar-check-fill text-success me-2"></i> Attendance Record Calendar</h5>
                    <div class="text-muted small">Visual attendance history for {{ now()->format('F Y') }}</div>
                </div>
                <div class="d-flex align-items-center gap-2">
                    <span class="badge bg-success-subtle text-success border border-success-subtle">● Present</span>
                    <span class="badge bg-danger-subtle text-danger border border-danger-subtle">● Absent</span>
                    <span class="badge bg-warning-subtle text-warning border border-warning-subtle">● Late</span>
                </div>
            </div>

            <!-- Calendar Grid -->
            <div class="calendar-grid">
                <div class="calendar-day-header">Mon</div>
                <div class="calendar-day-header">Tue</div>
                <div class="calendar-day-header">Wed</div>
                <div class="calendar-day-header">Thu</div>
                <div class="calendar-day-header">Fri</div>
                <div class="calendar-day-header">Sat</div>
                <div class="calendar-day-header">Sun</div>

                @php
                    $daysInMonth = now()->daysInMonth;
                    $startOfMonthDay = now()->startOfMonth()->dayOfWeekIso; // 1 (Mon) - 7 (Sun)
                    $attendancesByDay = $attendances->keyBy(function($att) {
                        return \Carbon\Carbon::parse($att->date)->day;
                    });
                @endphp

                <!-- Empty cells before day 1 -->
                @for($i = 1; $i < $startOfMonthDay; $i++)
                    <div class="calendar-day-cell bg-light opacity-50"></div>
                @endfor

                <!-- Days of Month -->
                @for($d = 1; $d <= $daysInMonth; $d++)
                    @php
                        $att = $attendancesByDay->get($d);
                        $statusClass = $att ? $att->status : '';
                    @endphp
                    <div class="calendar-day-cell {{ $statusClass }}">
                        <div class="day-num">{{ $d }}</div>
                        @if($att)
                            @if($att->status === 'present')
                                <span class="status-badge bg-success text-white"><i class="bi bi-check-lg"></i> Present</span>
                            @elseif($att->status === 'absent')
                                <span class="status-badge bg-danger text-white"><i class="bi bi-x-lg"></i> Absent</span>
                            @elseif($att->status === 'late')
                                <span class="status-badge bg-warning text-dark"><i class="bi bi-clock"></i> Late</span>
                            @endif
                        @else
                            <span class="small text-muted" style="font-size: 0.65rem;">--</span>
                        @endif
                    </div>
                @endfor
            </div>
        </div>

        <!-- Peers Roster -->
        @if($assignedClassroom)
        <div class="card border-0 shadow-sm rounded-4 bg-white p-4">
            <h5 class="fw-bold text-dark mb-3"><i class="bi bi-people-fill text-primary me-2"></i> Classroom Classmates ({{ $assignedClassroom->name }})</h5>
            <div class="row g-3">
                @forelse($classPeers as $peer)
                    <div class="col-6 col-md-3 col-lg-2">
                        <div class="p-3 bg-light rounded-3 text-center border">
                            <div class="rounded-circle bg-dark text-white d-flex align-items-center justify-content-center mx-auto mb-2 fw-bold" style="width: 45px; height: 45px;">
                                {{ substr($peer->first_name, 0, 1) }}
                            </div>
                            <div class="fw-bold text-dark small text-truncate">{{ $peer->first_name }} {{ $peer->last_name }}</div>
                            <div class="text-muted" style="font-size: 0.7rem;">{{ $peer->student_id_number }}</div>
                        </div>
                    </div>
                @empty
                    <div class="col-12 text-muted small">No other classmates listed in this environment.</div>
                @endforelse
            </div>
        </div>
        @endif
    </div>

    <!-- TAB 3: ASSIGNED LESSONS -->
    <div class="tab-pane fade" id="tab-lessons" role="tabpanel">
        <div class="card border-0 shadow-sm rounded-4 bg-white p-4">
            <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
                <div>
                    <h5 class="fw-bold text-dark mb-0"><i class="bi bi-journal-bookmark-fill text-success me-2"></i> Lessons Assigned by Directress</h5>
                    <p class="text-muted small mb-0">Scheduled presentations and three-period lesson plans for your environment</p>
                </div>
            </div>

            <div class="row g-3">
                @forelse($assignedLessons as $lesson)
                    <div class="col-12 col-md-6">
                        <div class="p-3 border rounded-3 bg-white shadow-sm h-100">
                            <div class="d-flex justify-content-between align-items-start mb-2">
                                <span class="badge bg-success-subtle text-success border border-success-subtle font-monospace">
                                    {{ $lesson->montessori_area ?? 'Montessori Area' }}
                                </span>
                                <span class="badge bg-light text-dark border">
                                    Status: {{ ucfirst($lesson->status ?? 'Planned') }}
                                </span>
                            </div>
                            <h6 class="fw-bold text-dark mb-1">{{ $lesson->title }}</h6>
                            <div class="small text-muted mb-2">
                                <i class="bi bi-calendar-event me-1"></i> Presentation Date: {{ $lesson->scheduled_date ? \Carbon\Carbon::parse($lesson->scheduled_date)->format('M d, Y') : 'Scheduled' }}
                            </div>
                            <p class="small text-secondary mb-2">{{ $lesson->description ?? 'Individual presentation plan.' }}</p>
                            @if($lesson->materials_required)
                                <div class="small text-dark font-italic bg-light p-2 rounded">
                                    <strong>Materials Needed:</strong> {{ $lesson->materials_required }}
                                </div>
                            @endif
                        </div>
                    </div>
                @empty
                    <div class="col-12 text-center py-4 text-muted">
                        <i class="bi bi-journal-x fs-1 d-block mb-2 text-secondary"></i>
                        <h6>No Lessons Assigned Yet</h6>
                        <p class="small">Your Directress has not posted new lesson plans for this period.</p>
                    </div>
                @endforelse
            </div>
        </div>
    </div>

    <!-- TAB 4: RELEASED OBSERVATIONS -->
    <div class="tab-pane fade" id="tab-observations" role="tabpanel">
        <div class="card border-0 shadow-sm rounded-4 bg-white p-4">
            <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
                <div>
                    <h5 class="fw-bold text-dark mb-0"><i class="bi bi-eye-fill text-primary me-2"></i> Directress Observation Log</h5>
                    <p class="text-muted small mb-0">Qualitative developmental progress notes and work cycle observations</p>
                </div>
            </div>

            <div class="row g-3">
                @forelse($observations as $obs)
                    <div class="col-12">
                        <div class="p-3 border rounded-3 bg-white shadow-sm border-start border-4 border-primary">
                            <div class="d-flex justify-content-between align-items-center mb-2 flex-wrap gap-2">
                                <div>
                                    <span class="badge bg-primary-subtle text-primary border border-primary-subtle font-monospace me-2">
                                        {{ $obs->avenue }}
                                    </span>
                                    <strong class="text-dark">{{ $obs->title }}</strong>
                                </div>
                                <div>
                                    <span class="badge bg-success text-white px-2 py-1 me-2">{{ $obs->mastery_level }}</span>
                                    <small class="text-muted">{{ $obs->observed_at ? \Carbon\Carbon::parse($obs->observed_at)->format('M d, Y') : date('M d, Y') }}</small>
                                </div>
                            </div>
                            <p class="text-secondary small mb-0 font-italic">
                                "{{ $obs->notes }}"
                            </p>
                            <div class="small text-muted mt-2">
                                Recorded by: <strong>{{ $obs->teacher && $obs->teacher->user ? $obs->teacher->user->name : 'Lead Directress' }}</strong>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-12 text-center py-4 text-muted">
                        <i class="bi bi-eye-slash fs-1 d-block mb-2 text-secondary"></i>
                        <h6>No Observations Recorded</h6>
                        <p class="small">Observation logs will appear here once recorded by your Directress.</p>
                    </div>
                @endforelse
            </div>
        </div>
    </div>

    <!-- TAB 5: GAMIFIED LMS -->
    <div class="tab-pane fade" id="tab-lms" role="tabpanel">

        <!-- XP Progress Bar -->
        <div class="card border-0 shadow-sm rounded-4 bg-white p-4 mb-4">
            <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3 mb-3">
                <div>
                    <h5 class="fw-bold text-dark mb-0"><i class="bi bi-controller text-danger me-2"></i> Gamified Interactive Learning Center</h5>
                    <p class="text-muted small mb-0">Pick a topic, play quiz games & earn XP to level up! 🎮</p>
                </div>
                <div class="d-flex align-items-center gap-3">
                    <div class="text-center">
                        <div class="small text-muted fw-semibold">XP Earned</div>
                        <div class="fs-4 fw-bold text-warning" id="userXpDisplay">250 XP</div>
                    </div>
                    <div class="text-center">
                        <div class="small text-muted fw-semibold">Level</div>
                        <div class="fs-4 fw-bold text-success" id="userLevelDisplay">⭐ Lv. 1</div>
                    </div>
                    <div class="text-center">
                        <div class="small text-muted fw-semibold">Quizzes Done</div>
                        <div class="fs-4 fw-bold text-primary" id="quizzesDoneDisplay">0</div>
                    </div>
                </div>
            </div>
            <!-- XP progress bar -->
            <div class="mb-1 d-flex justify-content-between small text-muted">
                <span>Progress to Level 2</span>
                <span id="xpProgressLabel">250 / 500 XP</span>
            </div>
            <div class="progress" style="height: 10px; border-radius: 20px;">
                <div class="progress-bar bg-warning" id="xpProgressBar" style="width: 50%; border-radius: 20px;" role="progressbar"></div>
            </div>
        </div>

        <!-- Topic Selector -->
        <div class="card border-0 shadow-sm rounded-4 bg-white p-4 mb-4">
            <h6 class="fw-bold text-dark mb-3"><i class="bi bi-bookmark-star-fill text-warning me-1"></i> Choose Learning Topic</h6>
            <div class="d-flex flex-wrap gap-2">
                <button class="btn btn-success btn-sm px-3 fw-bold topic-btn active-topic" onclick="loadTopic('Mathematics')">📐 Mathematics</button>
                <button class="btn btn-outline-primary btn-sm px-3 fw-bold topic-btn" onclick="loadTopic('Phonics')">🔤 Phonics & Language</button>
                <button class="btn btn-outline-warning btn-sm px-3 fw-bold topic-btn" onclick="loadTopic('Sensorial')">👁 Sensorial</button>
                <button class="btn btn-outline-danger btn-sm px-3 fw-bold topic-btn" onclick="loadTopic('PracticalLife')">🌿 Practical Life</button>
                <button class="btn btn-outline-info btn-sm px-3 fw-bold topic-btn" onclick="loadTopic('Cultural')">🌍 Cultural Studies</button>
                <button class="btn btn-outline-secondary btn-sm px-3 fw-bold topic-btn" onclick="loadTopic('Science')">🔬 Science & Nature</button>
            </div>
        </div>

        <!-- Games Row -->
        <div class="row g-4 mb-4">

            <!-- GAME 1: MCQ QUIZ QUEST (10 Questions) -->
            <div class="col-12 col-lg-8">
                <div class="card border-0 shadow-sm rounded-4 bg-white p-4 h-100">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <div>
                            <span class="badge bg-success text-white font-monospace me-2"><i class="bi bi-question-circle-fill me-1"></i> QUIZ QUEST</span>
                            <span class="small fw-bold text-success">+20 XP per correct answer</span>
                        </div>
                        <span class="badge bg-dark text-white" id="quizTopicBadge">📐 Mathematics</span>
                    </div>
                    <h5 class="fw-bold text-dark mb-1">🎯 Multiple Choice Knowledge Quiz</h5>
                    <p class="small text-muted mb-3">Answer 10 topic-specific questions. Every correct answer earns XP!</p>

                    <!-- Progress Bar -->
                    <div class="d-flex justify-content-between small text-muted mb-1">
                        <span>Question <span id="qNum">1</span> of <span id="qTotal">10</span></span>
                        <span>Score: <span id="qScore" class="fw-bold text-success">0</span> / <span id="qTotal2">10</span></span>
                    </div>
                    <div class="progress mb-3" style="height: 6px; border-radius: 20px;">
                        <div class="progress-bar bg-success" id="quizProgressBar" style="width: 10%; border-radius: 20px;"></div>
                    </div>

                    <!-- Question Card -->
                    <div class="p-4 bg-light rounded-3 border mb-3" id="questionCard">
                        <div class="fw-bold text-dark mb-3 lh-base" id="mcqQuestion" style="font-size: 1rem;"></div>
                        <div class="d-flex flex-column gap-2" id="mcqOptions"></div>
                    </div>

                    <div class="d-flex justify-content-between align-items-center">
                        <div id="mcqFeedback" class="fw-bold small"></div>
                        <button class="btn btn-m-primary btn-sm px-3" id="nextQuizBtn" onclick="nextQuestion()" style="display:none;">Next Question →</button>
                    </div>

                    <!-- Quiz Complete Message -->
                    <div id="quizComplete" style="display:none;" class="text-center py-3">
                        <div class="fs-2">🏆</div>
                        <h5 class="fw-bold text-success mb-1">Quiz Complete!</h5>
                        <p class="text-muted small">You scored <strong id="finalScore"></strong> / 10</p>
                        <button class="btn btn-success btn-sm px-4 mt-1" onclick="restartQuiz()"><i class="bi bi-arrow-clockwise me-1"></i> Play Again</button>
                    </div>
                </div>
            </div>

            <!-- GAME 2: MEMORY MATCH -->
            <div class="col-12 col-lg-4">
                <div class="card border-0 shadow-sm rounded-4 bg-white p-4 h-100">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span class="badge bg-warning text-dark font-monospace"><i class="bi bi-grid-3x3-gap-fill me-1"></i> MEMORY MATCH</span>
                        <span class="small fw-bold text-success">+50 XP</span>
                    </div>
                    <h6 class="fw-bold text-dark mb-1">🧩 Flip & Match Cards</h6>
                    <p class="small text-muted mb-3">Match identical Montessori term pairs. Find all 4 matches!</p>

                    <div class="row g-2 mb-3" id="memoryGameBoard">
                        <div class="col-3"><div class="memory-card" onclick="flipCard(this, 'Pink Tower', '🗼')">❓</div></div>
                        <div class="col-3"><div class="memory-card" onclick="flipCard(this, 'Phonics', '🔤')">❓</div></div>
                        <div class="col-3"><div class="memory-card" onclick="flipCard(this, 'Spindle Box', '🔢')">❓</div></div>
                        <div class="col-3"><div class="memory-card" onclick="flipCard(this, 'Pink Tower', '🗼')">❓</div></div>
                        <div class="col-3"><div class="memory-card" onclick="flipCard(this, 'Cylinder', '🔴')">❓</div></div>
                        <div class="col-3"><div class="memory-card" onclick="flipCard(this, 'Spindle Box', '🔢')">❓</div></div>
                        <div class="col-3"><div class="memory-card" onclick="flipCard(this, 'Phonics', '🔤')">❓</div></div>
                        <div class="col-3"><div class="memory-card" onclick="flipCard(this, 'Cylinder', '🔴')">❓</div></div>
                    </div>
                    <div class="d-flex justify-content-between align-items-center">
                        <div id="memoryGameScore" class="small fw-bold text-primary">Matches: 0 / 4</div>
                        <button class="btn btn-outline-secondary btn-sm" onclick="resetMemoryGame()"><i class="bi bi-arrow-clockwise"></i> Reset</button>
                    </div>
                </div>
            </div>
        </div>

        <!-- GAME 3: WORD SCRAMBLE -->
        <div class="card border-0 shadow-sm rounded-4 bg-white p-4 mb-4">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <div>
                    <span class="badge bg-info text-white font-monospace me-2"><i class="bi bi-shuffle me-1"></i> WORD SCRAMBLE</span>
                    <span class="small fw-bold text-success">+30 XP per correct guess</span>
                </div>
            </div>
            <h5 class="fw-bold text-dark mb-1">🔀 Unscramble the Montessori Word!</h5>
            <p class="small text-muted mb-3">Rearrange the letters to spell the correct Montessori term.</p>

            <div class="row align-items-center g-3">
                <div class="col-12 col-md-4 text-center">
                    <div class="p-3 bg-dark rounded-3">
                        <div class="text-warning fs-3 fw-bold font-monospace letter-spacing-wide" id="scrambledWord">ROTEPM</div>
                        <div class="text-white-50 small mt-1">Unscramble the word above</div>
                    </div>
                </div>
                <div class="col-12 col-md-5">
                    <label class="small fw-semibold text-muted mb-1">Your Answer:</label>
                    <div class="input-group">
                        <input type="text" id="scrambleInput" class="form-control form-control-lg fw-bold text-uppercase" placeholder="Type your answer..." onkeypress="if(event.key==='Enter') checkScramble()">
                        <button class="btn btn-m-primary px-3" onclick="checkScramble()"><i class="bi bi-check2 me-1"></i> Check</button>
                    </div>
                    <div class="small mt-2" id="scrambleFeedback"></div>
                </div>
                <div class="col-12 col-md-3 text-center">
                    <div class="small text-muted mb-1">Hint:</div>
                    <div class="p-2 bg-light border rounded-3 small text-dark fw-semibold" id="scrambleHint">A key material in Montessori Practical Life area</div>
                    <button class="btn btn-outline-secondary btn-sm mt-2 w-100" onclick="nextScramble()"><i class="bi bi-arrow-right me-1"></i> Next Word</button>
                </div>
            </div>
        </div>

        <!-- Teacher Created Quests -->
        <div class="card border-0 shadow-sm rounded-4 bg-white p-4">
            <h5 class="fw-bold text-dark mb-3"><i class="bi bi-award-fill text-warning me-2"></i> Teacher-Created Learning Quests</h5>
            <div class="row g-3">
                @forelse($teacherLmsPaths as $path)
                    <div class="col-12 col-md-6">
                        <div class="p-3 border rounded-3 bg-light shadow-sm d-flex justify-content-between align-items-center">
                            <div>
                                <span class="badge bg-primary-subtle text-primary border border-primary-subtle font-monospace mb-1">{{ $path->montessori_domain ?? 'General' }}</span>
                                <h6 class="fw-bold text-dark mb-1">{{ $path->title }}</h6>
                                <div class="text-muted small">{{ $path->description ?? 'Interactive learning quest designed by your Directress.' }}</div>
                            </div>
                            <button class="btn btn-sm btn-m-primary px-3 text-nowrap ms-2" onclick="startQuest('{{ addslashes($path->title) }}')">
                                <i class="bi bi-play-circle-fill me-1"></i> Play Quest
                            </button>
                        </div>
                    </div>
                @empty
                    <div class="col-12 text-center py-3">
                        <i class="bi bi-joystick fs-1 text-muted d-block mb-2"></i>
                        <div class="text-muted small">No teacher-created quests assigned yet. Use the quiz above to earn XP!</div>
                    </div>
                @endforelse
            </div>
        </div>
    </div>

    <!-- TAB 6: RELEASED REPORTS -->
    <div class="tab-pane fade" id="tab-reports" role="tabpanel">
        <div class="card border-0 shadow-sm rounded-4 bg-white p-4">
            <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
                <div>
                    <h5 class="fw-bold text-dark mb-0"><i class="bi bi-file-earmark-check-fill text-success me-2"></i> Official Assessment Report Cards</h5>
                    <p class="text-muted small mb-0">Verified term progress reports signed by Lead Directress & Principal</p>
                </div>
            </div>

            <div class="row g-3">
                @forelse($assessments as $report)
                    <div class="col-12">
                        <div class="p-4 border rounded-3 bg-white shadow-sm">
                            <div class="d-flex justify-content-between align-items-start mb-3 flex-wrap gap-2">
                                <div>
                                    <span class="badge bg-success-subtle text-success border border-success-subtle fw-bold mb-1">
                                        <i class="bi bi-shield-check me-1"></i> RELEASED & VERIFIED
                                    </span>
                                    <h4 class="fw-bold text-dark mb-1">{{ $report->title }}</h4>
                                    <div class="text-muted small">
                                        Evaluation Period: <strong>{{ $report->evaluation_period }}</strong> • Published: {{ $report->published_at ? $report->published_at->format('M d, Y') : date('M d, Y') }}
                                    </div>
                                </div>
                                <a href="{{ route('assessments.pdf', $report->id) }}" target="_blank" class="btn btn-m-primary px-3 py-2 fw-semibold">
                                    <i class="bi bi-download me-1"></i> Download PDF Report
                                </a>
                            </div>

                            <div class="row g-2 text-center my-3">
                                <div class="col-6 col-md-3">
                                    <div class="p-2 bg-light rounded border">
                                        <div class="small text-muted">Practical Life</div>
                                        <div class="badge bg-success text-white font-monospace mt-1">{{ $report->practical_life_status }} ({{ $report->practical_life_score }}%)</div>
                                    </div>
                                </div>
                                <div class="col-6 col-md-3">
                                    <div class="p-2 bg-light rounded border">
                                        <div class="small text-muted">Sensorial</div>
                                        <div class="badge bg-success text-white font-monospace mt-1">{{ $report->sensorial_status }} ({{ $report->sensorial_score }}%)</div>
                                    </div>
                                </div>
                                <div class="col-6 col-md-3">
                                    <div class="p-2 bg-light rounded border">
                                        <div class="small text-muted">Mathematics</div>
                                        <div class="badge bg-warning text-dark font-monospace mt-1">{{ $report->mathematics_status }} ({{ $report->mathematics_score }}%)</div>
                                    </div>
                                </div>
                                <div class="col-6 col-md-3">
                                    <div class="p-2 bg-light rounded border">
                                        <div class="small text-muted">Language</div>
                                        <div class="badge bg-info text-white font-monospace mt-1">{{ $report->language_status }} ({{ $report->language_score }}%)</div>
                                    </div>
                                </div>
                            </div>

                            <div class="p-3 bg-light rounded border-start border-3 border-success mt-2">
                                <div class="small fw-bold text-dark mb-1">Directress Summary:</div>
                                <div class="small text-secondary font-italic">"{{ $report->overall_summary }}"</div>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-12 text-center py-4 text-muted">
                        <i class="bi bi-file-earmark-x fs-1 d-block mb-2 text-secondary"></i>
                        <h6>No Released Reports Yet</h6>
                        <p class="small">Official report cards will appear here once released by administrative staff.</p>
                    </div>
                @endforelse
            </div>
        </div>
    </div>

</div>

<!-- Gamified LMS JavaScript -->
<script>
// ─── Global State ────────────────────────────────────────────────
let currentXp    = 250;
let quizzesDone  = 0;
let flippedCards = [];
let matchedPairs = 0;

// ─── MCQ Question Bank (10 per topic) ────────────────────────────
const QUIZ_BANK = {
    Mathematics: [
        { q: "What is the purpose of the Montessori Spindle Box?", opts: ["To introduce the concept of zero and loose quantity","To practice writing numbers","To sort geometric shapes","To learn decimal place values"], a: 0 },
        { q: "The Golden Bead material is used to teach which concept?", opts: ["Phonetic sounds","Decimal system (units, tens, hundreds, thousands)","Color gradation","Continent geography"], a: 1 },
        { q: "How many beads are in one 'thousand cube' of the Golden Bead?", opts: ["100","10","500","1000"], a: 3 },
        { q: "What does the Stamp Game help children practice?", opts: ["Reading sentences","Four operations with large numbers","Sensorial discrimination","Practical life pouring"], a: 1 },
        { q: "The Red and Blue Number Rods introduce children to which concept?", opts: ["Linear counting from 1–10","Phonics blending","Colour mixing","Three-period lessons"], a: 0 },
        { q: "Which material introduces the teen numbers (11–19)?", opts: ["Bead Chains","Teen Boards (Seguin Board A)","Number Rods","Sandpaper Numbers"], a: 1 },
        { q: "What is the purpose of the Sandpaper Numbers?", opts: ["To teach addition","Tactile recognition of number symbols","Sorting by size","Colour matching"], a: 1 },
        { q: "Checkerboard material is used for which mathematical operation?", opts: ["Simple addition","Long multiplication of large numbers","Subtraction with borrowing","Division with remainder"], a: 1 },
        { q: "How many units must be exchanged to make one 'ten bar'?", opts: ["5","10","100","1000"], a: 1 },
        { q: "The Bead Chains are used for which purpose?", opts: ["Skip counting and squaring numbers","Phonetic analysis","Practical life dressing","Cultural continent study"], a: 0 },
    ],
    Phonics: [
        { q: "What are Sandpaper Letters used for in Montessori?", opts: ["Counting","Tactile introduction to letter sounds & shapes","Sensorial colour grading","Cultural geography"], a: 1 },
        { q: "The Moveable Alphabet allows children to:", opts: ["Sort objects by weight","Compose words before writing","Count beads","Do long multiplication"], a: 1 },
        { q: "What is a 'phonogram' in Montessori language work?", opts: ["A picture card","A combination of letters making one sound (e.g. 'sh', 'th')","A number symbol","A geographic term"], a: 1 },
        { q: "What are 'Pink Series' books used for?", opts: ["Cultural studies","Early CVC word reading (e.g. cat, hat, mat)","Long division","Practical life exercises"], a: 1 },
        { q: "The Metal Insets prepare children for:", opts: ["Mathematical operations","Pencil control and writing readiness","Sensorial colour work","Science experiments"], a: 1 },
        { q: "Which series comes after the Pink Series in Montessori reading?", opts: ["Blue Series","Green Series","Purple Series","Red Series"], a: 0 },
        { q: "What does 'phonetic awareness' mean?", opts: ["Recognising letter shapes only","Understanding and manipulating individual sounds in words","Memorising whole words","Learning grammar rules"], a: 1 },
        { q: "The Classified Cards in language help children:", opts: ["Practice multiplication","Build vocabulary linked to real objects or images","Learn continent names","Measure length"], a: 1 },
        { q: "Which skill does the Montessori 'I Spy' activity develop?", opts: ["Mathematical counting","Phonemic awareness – initial sounds","Practical life pouring","Sensorial weight comparison"], a: 1 },
        { q: "Grammar Symbols in Montessori use which approach?", opts: ["Colour-coded geometric shapes to represent parts of speech","Number lines","Bead chains","Sandpaper textures"], a: 0 },
    ],
    Sensorial: [
        { q: "What is the primary purpose of Sensorial materials?", opts: ["Teach reading","Refine and educate the five senses","Introduce mathematics","Develop writing skills"], a: 1 },
        { q: "The Pink Tower is used to develop which sense?", opts: ["Touch","Visual discrimination of size (large to small)","Sound","Smell"], a: 1 },
        { q: "How many cubes make up the Pink Tower?", opts: ["5","8","10","12"], a: 2 },
        { q: "The Colour Tablets work develops discrimination of:", opts: ["Weight","Colour shades and hues","Sound pitches","Geometric shapes"], a: 1 },
        { q: "The Sound Boxes help children refine which sense?", opts: ["Sight","Taste","Hearing – matching identical sounds","Touch – texture"], a: 2 },
        { q: "The Baric Tablets help children discriminate:", opts: ["Weight (heavy/light)","Colour","Sound pitch","Surface texture"], a: 0 },
        { q: "What does the Brown Stair (Broad Stair) teach?", opts: ["Linear counting","Visual discrimination of thickness","Phonetic sounds","Cultural geography"], a: 1 },
        { q: "The Geometric Solids introduce children to:", opts: ["Number symbols","Three-dimensional shapes and their names","Letter formation","Continent names"], a: 1 },
        { q: "The Knobbed Cylinders develop which skill?", opts: ["Counting to 100","Pincer grip + visual discrimination of dimension","Phonetic blending","Long multiplication"], a: 1 },
        { q: "The Red Rods help children understand:", opts: ["Colour gradation","Length – ordering from shortest to longest","Sound pitch","Weight"], a: 1 },
    ],
    PracticalLife: [
        { q: "What is the main goal of Practical Life exercises?", opts: ["Teach reading","Develop independence, order, coordination, and concentration","Introduce number concepts","Practice sensorial discrimination"], a: 1 },
        { q: "Pouring activities in Practical Life primarily develop:", opts: ["Mathematical operations","Hand-eye coordination and fine motor control","Letter recognition","Geographic knowledge"], a: 1 },
        { q: "Care of Self activities include:", opts: ["Bead chain counting","Dressing frames, hand washing, shoe polishing","Metal inset tracing","Colour tablet matching"], a: 1 },
        { q: "The Dressing Frames help children learn:", opts: ["Mathematical concepts","Independent dressing skills (buttons, zippers, laces)","Phonetic analysis","Sensorial colour work"], a: 1 },
        { q: "Care of Environment in Montessori includes:", opts: ["Counting with golden beads","Dusting, sweeping, plant care, table washing","Grammar symbol work","Metal inset drawing"], a: 1 },
        { q: "Grace and Courtesy lessons teach children:", opts: ["Long multiplication","Respectful social behaviour and manners","Letter sounds","Geometric solids"], a: 1 },
        { q: "Tweezing and sorting activities strengthen:", opts: ["Reading comprehension","Pincer grip (fine motor skill preparation for writing)","Cultural geography","Number operations"], a: 1 },
        { q: "What does 'normalisation' mean in Montessori?", opts: ["Following a strict timetable","When a child reaches deep concentration, order, and self-discipline","Learning by rote","Memorising facts"], a: 1 },
        { q: "Flower arranging in Practical Life develops:", opts: ["Mathematical skills","Aesthetics, fine motor control, and care of environment","Phonetic awareness","Sensorial weight work"], a: 1 },
        { q: "The three-hour work cycle in Montessori allows:", opts: ["Structured teacher-led lessons only","Uninterrupted time for children to choose and concentrate deeply","Standardised testing","Homework completion"], a: 1 },
    ],
    Cultural: [
        { q: "What are the five areas of Montessori Cultural studies?", opts: ["Reading, Writing, Math, Science, Art","Geography, History, Science, Zoology, Botany","Phonics, Grammar, Spelling, Composition, Literature","Practical Life, Sensorial, Math, Language, Culture"], a: 1 },
        { q: "The Puzzle Maps in Montessori Geography teach:", opts: ["Number operations","Recognition of countries and continents","Letter sounds","Colour discrimination"], a: 1 },
        { q: "Botany works in Montessori include:", opts: ["Bead chain counting","Leaf cabinet, parts of a plant, classification","Metal inset drawing","Sound cylinder matching"], a: 1 },
        { q: "The Montessori Timeline of Life represents:", opts: ["Mathematical sequences","History of life on Earth – from single cells to humans","Phonetic word lists","Geometric shapes"], a: 1 },
        { q: "Zoology in Montessori uses which materials?", opts: ["Spindle boxes","Classified cards, puzzle pieces of animals and their parts","Sandpaper letters","Pink Tower cubes"], a: 1 },
        { q: "The Continent Globe in Montessori is:", opts: ["A colour-matching exercise","A tactile globe with sandpaper oceans and raised land masses","A phonics reading tool","A mathematical bead material"], a: 1 },
        { q: "Which sense do the Land and Water Form trays develop?", opts: ["Hearing","Tactile understanding of geographic forms (island, lake, peninsula)","Smell","Mathematical quantity"], a: 1 },
        { q: "The Montessori Command Cards are used in:", opts: ["Mathematics – place value","Language – reading and acting out commands","Sensorial – colour grading","Practical Life – dressing"], a: 1 },
        { q: "History study in Montessori typically starts with:", opts: ["Medieval history","The child's own personal timeline and birthday ceremony","Ancient Rome","Industrial Revolution"], a: 1 },
        { q: "Science experiments in Montessori emphasise:", opts: ["Memorising facts","Discovery, observation, hypothesis and hands-on exploration","Standardised tests","Worksheet completion"], a: 0 },
    ],
    Science: [
        { q: "What does the Montessori science approach emphasise?", opts: ["Rote memorisation of facts","Hands-on observation, discovery, and classification","Standardised worksheet tests","Teacher-led lectures only"], a: 1 },
        { q: "A hypothesis is:", opts: ["A proven scientific law","An educated guess that can be tested by experiment","A type of Montessori material","A geographic term"], a: 1 },
        { q: "Which planet is closest to the Sun in our Solar System?", opts: ["Earth","Venus","Mercury","Mars"], a: 2 },
        { q: "What is photosynthesis?", opts: ["Animals breathing oxygen","Plants making food from sunlight, water, and CO₂","Water cycle process","Digestion in humans"], a: 1 },
        { q: "The three states of matter are:", opts: ["Hot, Warm, Cold","Solid, Liquid, Gas","Heavy, Light, Medium","Red, Yellow, Blue"], a: 1 },
        { q: "What does a magnet attract?", opts: ["All metals","Ferromagnetic materials like iron, nickel, cobalt","Only gold","Plastic objects"], a: 1 },
        { q: "The Water Cycle includes which processes?", opts: ["Addition, subtraction, multiplication","Evaporation, condensation, precipitation","Reading, writing, arithmetic","Sorting, matching, grading"], a: 1 },
        { q: "What is an ecosystem?", opts: ["A type of Montessori shelf","A community of living organisms and their physical environment","A mathematical graph","A phonetic series"], a: 1 },
        { q: "Which force pulls objects towards Earth?", opts: ["Magnetism","Friction","Gravity","Electricity"], a: 2 },
        { q: "What does a thermometer measure?", opts: ["Weight","Temperature","Length","Volume"], a: 1 },
    ],
};

// ─── Word Scramble Bank ───────────────────────────────────────────
const SCRAMBLE_BANK = [
    { word: "MONTESSORI", hint: "The founder and educational philosophy name" },
    { word: "PRACTICAL",  hint: "Practical _____ Life — first Montessori area" },
    { word: "SENSORIAL",  hint: "Area that educates the five senses" },
    { word: "PHONICS",    hint: "Learning letter sounds to build reading skills" },
    { word: "CYLINDER",   hint: "Knobbed _______ — Sensorial dimension material" },
    { word: "FREEDOM",    hint: "Children have _______ to choose their work" },
    { word: "MASTERY",    hint: "The highest level in Montessori rubric" },
    { word: "OBSERVE",    hint: "What a Montessori Directress does silently" },
    { word: "CULTURE",    hint: "Geography, History, Science area in Montessori" },
    { word: "ALGEBRA",    hint: "Advanced branch of Mathematics" },
];

// ─── State ────────────────────────────────────────────────────────
let currentTopic   = 'Mathematics';
let currentQIndex  = 0;
let currentScore   = 0;
let quizActive     = true;
let scrambleIndex  = 0;

// ─── XP & Level Functions ─────────────────────────────────────────
function addXp(amount) {
    currentXp += amount;
    document.getElementById('userXpPoints').innerText = currentXp;
    document.getElementById('userXpDisplay').innerText = currentXp + ' XP';
    const pct = Math.min((currentXp % 500) / 5, 100);
    document.getElementById('xpProgressBar').style.width = pct + '%';
    document.getElementById('xpProgressLabel').innerText = currentXp + ' / ' + (Math.ceil(currentXp / 500) * 500) + ' XP';
    const lvl = Math.floor(currentXp / 500) + 1;
    document.getElementById('userLevelDisplay').innerText = '⭐ Lv. ' + lvl;
}

// ─── Topic Loader ─────────────────────────────────────────────────
function loadTopic(topic) {
    currentTopic  = topic;
    currentQIndex = 0;
    currentScore  = 0;
    quizActive    = true;

    document.getElementById('quizTopicBadge').innerText = topic;
    document.getElementById('quizComplete').style.display = 'none';
    document.getElementById('questionCard').style.display = '';
    document.getElementById('qScore').innerText = '0';
    renderQuestion();
}

function renderQuestion() {
    const bank = QUIZ_BANK[currentTopic];
    if (!bank || currentQIndex >= bank.length) { showQuizComplete(); return; }

    const q = bank[currentQIndex];
    document.getElementById('qNum').innerText = currentQIndex + 1;
    document.getElementById('qTotal').innerText = bank.length;
    document.getElementById('qTotal2').innerText = bank.length;
    document.getElementById('quizProgressBar').style.width = ((currentQIndex + 1) / bank.length * 100) + '%';
    document.getElementById('mcqQuestion').innerText = 'Q' + (currentQIndex + 1) + ': ' + q.q;
    document.getElementById('mcqFeedback').innerHTML = '';
    document.getElementById('nextQuizBtn').style.display = 'none';

    const optHtml = q.opts.map((o, i) => {
        const letter = ['A','B','C','D'][i];
        return `<button class="btn btn-outline-dark text-start btn-sm w-100 py-2 px-3" onclick="answerMCQ(${i})">${letter}) ${o}</button>`;
    }).join('');
    document.getElementById('mcqOptions').innerHTML = optHtml;
}

function answerMCQ(chosen) {
    if (!quizActive) return;
    const q    = QUIZ_BANK[currentTopic][currentQIndex];
    const btns = document.getElementById('mcqOptions').querySelectorAll('button');
    btns.forEach((b, i) => {
        b.disabled = true;
        if (i === q.a) b.classList.replace('btn-outline-dark', 'btn-success');
        else if (i === chosen && chosen !== q.a) b.classList.replace('btn-outline-dark', 'btn-danger');
    });

    const fb = document.getElementById('mcqFeedback');
    if (chosen === q.a) {
        currentScore++;
        addXp(20);
        quizzesDone++;
        document.getElementById('quizzesDoneDisplay').innerText = quizzesDone;
        document.getElementById('qScore').innerText = currentScore;
        fb.innerHTML = '<span class="text-success"><i class="bi bi-check-circle-fill me-1"></i>Correct! +20 XP</span>';
    } else {
        fb.innerHTML = '<span class="text-danger"><i class="bi bi-x-circle-fill me-1"></i>Incorrect! The correct answer is highlighted in green.</span>';
    }

    document.getElementById('nextQuizBtn').style.display = '';
}

function nextQuestion() {
    currentQIndex++;
    if (currentQIndex >= QUIZ_BANK[currentTopic].length) {
        showQuizComplete();
    } else {
        renderQuestion();
    }
}

function showQuizComplete() {
    document.getElementById('questionCard').style.display  = 'none';
    document.getElementById('nextQuizBtn').style.display   = 'none';
    document.getElementById('mcqFeedback').innerHTML       = '';
    document.getElementById('quizComplete').style.display  = '';
    document.getElementById('finalScore').innerText        = currentScore;
    if (currentScore >= 8) addXp(100);
}

function restartQuiz() {
    loadTopic(currentTopic);
}

// ─── Memory Match ─────────────────────────────────────────────────
function flipCard(element, term, icon) {
    if (element.classList.contains('flipped') || element.classList.contains('matched') || flippedCards.length >= 2) return;
    element.classList.add('flipped');
    element.innerHTML = icon + '<br><small style="font-size:0.6rem;">' + term + '</small>';
    flippedCards.push({ element, term });

    if (flippedCards.length === 2) {
        if (flippedCards[0].term === flippedCards[1].term) {
            setTimeout(() => {
                flippedCards[0].element.classList.add('matched');
                flippedCards[1].element.classList.add('matched');
                matchedPairs++;
                document.getElementById('memoryGameScore').innerHTML = 'Matches: ' + matchedPairs + ' / 4 🎉';
                flippedCards = [];
                if (matchedPairs === 4) { addXp(50); alert('🎉 All matched! +50 XP earned!'); }
            }, 400);
        } else {
            setTimeout(() => {
                flippedCards[0].element.classList.remove('flipped'); flippedCards[0].element.innerHTML = '❓';
                flippedCards[1].element.classList.remove('flipped'); flippedCards[1].element.innerHTML = '❓';
                flippedCards = [];
            }, 800);
        }
    }
}

function resetMemoryGame() {
    matchedPairs = 0;
    flippedCards = [];
    document.getElementById('memoryGameScore').innerHTML = 'Matches: 0 / 4';
    document.querySelectorAll('.memory-card').forEach(c => {
        c.classList.remove('flipped','matched');
        c.innerHTML = '❓';
    });
}

// ─── Word Scramble ────────────────────────────────────────────────
function scrambleStr(w) {
    let a = w.split('');
    for (let i = a.length - 1; i > 0; i--) {
        const j = Math.floor(Math.random() * (i + 1));
        [a[i], a[j]] = [a[j], a[i]];
    }
    const s = a.join('');
    return s === w ? scrambleStr(w) : s;
}

function renderScramble() {
    const item = SCRAMBLE_BANK[scrambleIndex % SCRAMBLE_BANK.length];
    document.getElementById('scrambledWord').innerText = scrambleStr(item.word);
    document.getElementById('scrambleHint').innerText  = item.hint;
    document.getElementById('scrambleInput').value     = '';
    document.getElementById('scrambleFeedback').innerHTML = '';
}

function checkScramble() {
    const item    = SCRAMBLE_BANK[scrambleIndex % SCRAMBLE_BANK.length];
    const answer  = document.getElementById('scrambleInput').value.trim().toUpperCase();
    const fb      = document.getElementById('scrambleFeedback');
    if (answer === item.word) {
        addXp(30);
        fb.innerHTML = '<span class="text-success fw-bold"><i class="bi bi-check-circle-fill me-1"></i>Correct! The word is "' + item.word + '" — +30 XP earned!</span>';
        setTimeout(() => nextScramble(), 1500);
    } else {
        fb.innerHTML = '<span class="text-danger fw-bold"><i class="bi bi-x-circle-fill me-1"></i>Not quite! Try again.</span>';
    }
}

function nextScramble() {
    scrambleIndex++;
    renderScramble();
}

function startQuest(title) {
    addXp(50);
    alert('🎮 Starting Quest: "' + title + '"!\n\n+50 XP awarded on completion! Work through the activities your Directress has prepared.');
}

// ─── Initialise on page load ─────────────────────────────────────
document.addEventListener('DOMContentLoaded', () => {
    loadTopic('Mathematics');
    renderScramble();
});
</script>
@endsection
