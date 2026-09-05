@extends('layouts.app')

@section('title', 'Attendance & Gate Pass Security')
@section('page_title', 'Check-In & Pickups')

@section('breadcrumb')
    <li class="breadcrumb-item active" aria-current="page">Attendance & Gate Pass</li>
@endsection

@section('content')
@php
    $isParent = Auth::check() && Auth::user()->role && Auth::user()->role->name === 'parent';
    $isStudent = Auth::check() && Auth::user()->role && Auth::user()->role->name === 'student';
    $isTeacherOrAdmin = Auth::check() && Auth::user()->role && in_array(Auth::user()->role->name, ['teacher', 'admin', 'principal', 'superadmin']);
@endphp

<div class="d-flex flex-column gap-4">
    <!-- Success Banner -->
    @if(session('success'))
        <div class="alert alert-success border-0 shadow-sm rounded-3 d-flex align-items-center justify-content-between mb-0">
            <div>
                <i class="bi bi-check-circle-fill me-2 fs-5"></i>
                <strong>Success:</strong> {{ session('success') }}
            </div>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <!-- Header Banner -->
    <div class="p-4 rounded-4 text-white shadow-sm" style="background: linear-gradient(135deg, #1c382b 0%, #2d5a45 100%);">
        <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3">
            <div>
                <span class="badge bg-white bg-opacity-20 text-white border border-white border-opacity-25 px-3 py-1 rounded-pill mb-2 fs-7">
                    <i class="bi bi-clock-history me-1"></i> Campus Attendance & Safety System
                </span>
                <h3 class="fw-bold mb-1 text-white">Daily Attendance & Digital Gate Pass</h3>
                <p class="text-white-50 mb-0 small">
                    Real-time check-in logs, database-backed attendance tracking, and parent/student isolation.
                </p>
            </div>
            <div class="d-flex align-items-center gap-2">
                @if($isTeacherOrAdmin)
                    <button class="btn btn-warning text-dark fw-bold rounded-pill px-4 shadow-sm" data-bs-toggle="modal" data-bs-target="#markClassroomAttendanceModal">
                        <i class="bi bi-check2-square me-1"></i> Mark Class Attendance
                    </button>
                @endif
                <span class="badge bg-success text-white px-3 py-2 rounded-pill fs-7 fw-bold shadow-sm">
                    <i class="bi bi-shield-check me-1"></i> Status: DB Scoping Active
                </span>
            </div>
        </div>
    </div>

    <!-- Attendance Summary Metrics -->
    <div class="row g-3">
        <div class="col-md-3">
            <div class="card border-0 shadow-sm rounded-4 p-3 bg-white border-start border-4 border-success">
                <span class="text-muted small fw-bold text-uppercase">Total Enrolled Students</span>
                <h3 class="fw-bold text-success mt-1 mb-0">{{ $assignedStudents->count() }}</h3>
                <p class="text-muted fs-8 mb-0 mt-1">Authorized for attendance</p>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card border-0 shadow-sm rounded-4 p-3 bg-white border-start border-4 border-info">
                <span class="text-muted small fw-bold text-uppercase">Assigned Classrooms</span>
                <h3 class="fw-bold text-info mt-1 mb-0">{{ $assignedClassrooms->count() > 0 ? $assignedClassrooms->count() : 1 }}</h3>
                <p class="text-muted fs-8 mb-0 mt-1">Environments active</p>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card border-0 shadow-sm rounded-4 p-3 bg-white border-start border-4 border-primary">
                <span class="text-muted small fw-bold text-uppercase">Attendance Logs</span>
                <h3 class="fw-bold text-primary mt-1 mb-0">{{ $attendances->total() ?? $attendances->count() }}</h3>
                <p class="text-muted fs-8 mb-0 mt-1">Total saved database records</p>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card border-0 shadow-sm rounded-4 p-3 bg-white border-start border-4 border-warning">
                <span class="text-muted small fw-bold text-uppercase">Today's Date</span>
                <h3 class="fw-bold text-dark mt-1 mb-0">{{ date('d M Y') }}</h3>
                <p class="text-muted fs-8 mb-0 mt-1">Standard session date</p>
            </div>
        </div>
    </div>

    <!-- Main Content Section -->
    <div class="row g-4">
        <!-- Attendance Table Card -->
        <div class="{{ $isTeacherOrAdmin ? 'col-lg-8' : 'col-lg-7' }}">
            <div class="card border-0 shadow-sm rounded-4 bg-white p-4 h-100">
                <div class="d-flex align-items-center justify-content-between mb-3 border-bottom pb-3">
                    <div>
                        <h5 class="fw-bold text-dark mb-0">
                            <i class="bi bi-calendar-check text-success me-2"></i> Real Attendance Database Log
                        </h5>
                        <span class="text-muted fs-8">Isolated for current authenticated user profile</span>
                    </div>
                    @if($isTeacherOrAdmin)
                        <button class="btn btn-sm btn-outline-success rounded-pill fw-bold" data-bs-toggle="modal" data-bs-target="#markClassroomAttendanceModal">
                            <i class="bi bi-plus-lg me-1"></i> New Roll Call
                        </button>
                    @endif
                </div>

                <div class="table-responsive">
                    <table class="table align-middle table-hover">
                        <thead class="table-light text-secondary fs-7 text-uppercase">
                            <tr>
                                <th>Student Name</th>
                                <th>Classroom</th>
                                <th>Date</th>
                                <th>Status</th>
                                <th>Remarks</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($attendances as $att)
                                <tr>
                                    <td class="fw-bold text-dark">
                                        {{ $att->student && $att->student->user ? $att->student->user->name : 'Student #'.$att->student_id }}
                                    </td>
                                    <td class="small text-secondary">
                                        {{ $att->classroom ? $att->classroom->name : 'General' }}
                                    </td>
                                    <td class="fw-bold text-dark small">
                                        {{ \Carbon\Carbon::parse($att->date)->format('M d, Y') }}
                                    </td>
                                    <td>
                                        @if(strtolower($att->status) === 'present')
                                            <span class="badge bg-success rounded-pill px-3 py-1">PRESENT</span>
                                        @elseif(strtolower($att->status) === 'absent')
                                            <span class="badge bg-danger rounded-pill px-3 py-1">ABSENT</span>
                                        @elseif(strtolower($att->status) === 'tardy')
                                            <span class="badge bg-warning text-dark rounded-pill px-3 py-1">TARDY</span>
                                        @else
                                            <span class="badge bg-info rounded-pill px-3 py-1">{{ strtoupper($att->status) }}</span>
                                        @endif
                                    </td>
                                    <td class="small text-muted">
                                        {{ $att->remarks ?? '-' }}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center py-4 text-muted">
                                        <i class="bi bi-folder-x fs-2 d-block mb-2 text-secondary"></i>
                                        No attendance records found yet. Click <strong>"Mark Class Attendance"</strong> to log attendance.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if(method_exists($attendances, 'links'))
                    <div class="mt-3">
                        {{ $attendances->links() }}
                    </div>
                @endif
            </div>
        </div>

        <!-- Digital Gate Pass Side Card -->
        <div class="{{ $isTeacherOrAdmin ? 'col-lg-4' : 'col-lg-5' }}">
            <div class="card border-0 shadow-sm rounded-4 bg-white p-4 text-center h-100">
                <span class="badge bg-success-subtle text-success border border-success-subtle px-3 py-1 rounded-pill fw-bold mb-3 d-inline-block">
                    <i class="bi bi-qr-code-scan me-1"></i> Official Digital Gate Pass
                </span>
                
                <div class="p-3 bg-light rounded-4 d-inline-block mb-3 border">
                    <i class="bi bi-qr-code text-dark" style="font-size: 6.5rem;"></i>
                </div>

                <h5 class="fw-bold text-dark mb-1">{{ Auth::user()->name }}</h5>
                <p class="text-muted small mb-3">Role: <strong>{{ strtoupper(Auth::user()->role ? Auth::user()->role->name : 'USER') }}</strong></p>

                <div class="bg-light p-3 rounded-3 text-start mb-3">
                    <span class="fw-bold text-dark small d-block mb-2"><i class="bi bi-person-check-fill text-success me-1"></i> Security Verification:</span>
                    <ul class="list-unstyled mb-0 small text-secondary">
                        <li class="mb-1"><i class="bi bi-check-circle text-success me-2"></i> Verified DB Role Access</li>
                        <li class="mb-1"><i class="bi bi-check-circle text-success me-2"></i> Real-time Attendance Sync</li>
                        <li><i class="bi bi-check-circle text-success me-2"></i> Parent & Student Scoped</li>
                    </ul>
                </div>

                <button type="button" class="btn btn-outline-dark rounded-pill w-100 fw-bold mt-auto">
                    <i class="bi bi-download me-1"></i> Download Gate Pass Card
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Modal for Class Attendance Roll Call -->
@if($isTeacherOrAdmin)
<div class="modal fade" id="markClassroomAttendanceModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <form action="{{ route('attendance.store-batch') }}" method="POST" class="modal-content border-0 shadow-lg rounded-4">
            @csrf
            <div class="modal-header bg-dark text-white rounded-top-4">
                <h5 class="modal-title fw-bold"><i class="bi bi-check2-square text-warning me-2"></i> Mark Classroom Roll Call</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <div class="modal-body p-4">
                <div class="row g-3 mb-3">
                    <div class="col-md-6">
                        <label class="form-label fw-bold text-dark small">Attendance Date</label>
                        <input type="date" name="date" class="form-control fw-bold border-success-subtle" value="{{ date('Y-m-d') }}" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-bold text-dark small">Classroom Environment</label>
                        <select name="classroom_id" class="form-select fw-bold border-success-subtle">
                            @foreach($assignedClassrooms as $cls)
                                <option value="{{ $cls->id }}">{{ $cls->name }} ({{ $cls->age_group }})</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="table-responsive border rounded-3 overflow-hidden">
                    <table class="table align-middle mb-0">
                        <thead class="table-light small text-uppercase">
                            <tr>
                                <th>Student Name</th>
                                <th>Classroom</th>
                                <th>Status</th>
                                <th>Remarks</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($assignedStudents as $index => $st)
                                <tr>
                                    <td>
                                        <strong class="text-dark">{{ $st->user ? $st->user->name : 'Student #'.$st->id }}</strong>
                                        <input type="hidden" name="attendance[{{ $index }}][student_id]" value="{{ $st->id }}">
                                    </td>
                                    <td class="small text-muted">
                                        {{ $st->classroom ? $st->classroom->name : 'Primary' }}
                                    </td>
                                    <td>
                                        <select name="attendance[{{ $index }}][status]" class="form-select form-select-sm border-success fw-bold">
                                            <option value="present" selected>PRESENT</option>
                                            <option value="absent">ABSENT</option>
                                            <option value="tardy">TARDY / LATE</option>
                                            <option value="excused">EXCUSED</option>
                                        </select>
                                    </td>
                                    <td>
                                        <input type="text" name="attendance[{{ $index }}][remarks]" class="form-control form-control-sm" placeholder="Optional remark...">
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center py-4 text-muted">No assigned students found for roll call.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="modal-footer border-0 bg-light rounded-bottom-4">
                <button type="button" class="btn btn-secondary rounded-pill px-4" data-bs-dismiss="modal">Cancel</button>
                <button type="submit" class="btn btn-success rounded-pill px-4 fw-bold">
                    <i class="bi bi-save me-1"></i> Save & Broadcast Attendance
                </button>
            </div>
        </form>
    </div>
</div>
@endif
@endsection
