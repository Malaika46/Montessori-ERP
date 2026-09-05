@extends('layouts.app')

@section('title', 'Observation & Mastery Journal')
@section('page_title', 'Observation & Mastery Engine')

@section('breadcrumb')
    <li class="breadcrumb-item active" aria-current="page">Observations & Mastery</li>
@endsection

@section('content')
@php
    $isParent = Auth::check() && Auth::user()->role && Auth::user()->role->name === 'parent';
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
                    <i class="bi bi-journal-check me-1"></i> Continuous Narrative Progress Tracking
                </span>
                <h3 class="fw-bold mb-1 text-white">Montessori Child Observation & Mastery Journal</h3>
                <p class="text-white-50 mb-0 small">
                    Real-time qualitative developmental logs across 5 core avenues. Scoped per student and verified parents.
                </p>
            </div>
            <div class="d-flex align-items-center gap-2">
                @if($isTeacherOrAdmin)
                    <button class="btn btn-warning text-dark fw-bold rounded-pill px-4 shadow-sm" data-bs-toggle="modal" data-bs-target="#newObservationModal">
                        <i class="bi bi-plus-circle me-1"></i> Record New Observation
                    </button>
                @endif
                <span class="badge bg-success text-white px-3 py-2 rounded-pill fs-7 fw-bold shadow-sm">
                    <i class="bi bi-shield-check me-1"></i> DB Isolation Active
                </span>
            </div>
        </div>
    </div>

    <!-- Observations List Grid -->
    <div class="card border-0 shadow-sm rounded-4 bg-white p-4">
        <div class="d-flex align-items-center justify-content-between mb-4 border-bottom pb-3">
            <h5 class="fw-bold text-dark mb-0">
                <i class="bi bi-eye text-success me-2"></i> Recent Guide Observations
            </h5>
            <span class="text-muted small">Showing records for assigned roster</span>
        </div>

        <div class="row g-4">
            @forelse($dbObservations as $obs)
                <div class="col-lg-6">
                    <div class="p-4 rounded-4 border bg-light h-100 position-relative shadow-sm">
                        <div class="d-flex align-items-center justify-content-between mb-3">
                            <span class="badge bg-success text-white rounded-pill px-3 py-1 fw-bold fs-8">
                                {{ strtoupper($obs->avenue) }}
                            </span>
                            <span class="badge bg-dark text-white rounded-pill px-3 py-1 fs-8">
                                {{ $obs->mastery_level }}
                            </span>
                        </div>

                        <h5 class="fw-bold text-dark mb-1">{{ $obs->title }}</h5>
                        <p class="text-muted fs-8 mb-2">
                            Student: <strong>{{ $obs->student && $obs->student->user ? $obs->student->user->name : 'Student #'.$obs->student_id }}</strong>
                            • Classroom: <strong>{{ $obs->classroom ? $obs->classroom->name : 'Primary' }}</strong>
                        </p>

                        <div class="p-3 bg-white rounded-3 border mb-3 text-secondary fs-7">
                            "{{ $obs->notes }}"
                        </div>

                        <div class="d-flex align-items-center justify-content-between text-muted fs-8">
                            <span><i class="bi bi-person-circle me-1"></i> {{ $obs->teacher && $obs->teacher->user ? $obs->teacher->user->name : 'Directress' }}</span>
                            <span><i class="bi bi-calendar3 me-1"></i> {{ \Carbon\Carbon::parse($obs->observed_at)->format('M d, Y') }}</span>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-12 text-center py-5 text-muted">
                    <i class="bi bi-journal-x fs-1 d-block mb-2 text-secondary"></i>
                    No custom database observations logged yet. Click <strong>"Record New Observation"</strong> to create a report.
                </div>
            @endforelse
        </div>

        @if(method_exists($dbObservations, 'links'))
            <div class="mt-4">
                {{ $dbObservations->links() }}
            </div>
        @endif
    </div>
</div>

<!-- Modal for New Observation -->
@if($isTeacherOrAdmin)
<div class="modal fade" id="newObservationModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <form action="{{ route('observations.store') }}" method="POST" class="modal-content border-0 shadow-lg rounded-4">
            @csrf
            <div class="modal-header bg-dark text-white rounded-top-4">
                <h5 class="modal-title fw-bold"><i class="bi bi-plus-circle me-2 text-success"></i> Record New Montessori Observation</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <div class="modal-body p-4">
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label fw-bold small text-dark">Select Student (Assigned Classrooms)</label>
                        <select class="form-select fw-bold border-success-subtle" name="student_id" required>
                            @forelse($assignedStudents as $st)
                                <option value="{{ $st->id }}">{{ $st->user ? $st->user->name : 'Student #'.$st->id }} ({{ $st->classroom ? $st->classroom->name : 'Enrolled' }})</option>
                            @empty
                                <option value="">No assigned students found</option>
                            @endforelse
                        </select>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-bold small text-dark">Montessori Curriculum Area</label>
                        <select class="form-select fw-bold border-success-subtle" name="avenue" required>
                            <option value="Practical Life">Practical Life</option>
                            <option value="Sensorial" selected>Sensorial</option>
                            <option value="Mathematics">Mathematics</option>
                            <option value="Language">Language</option>
                            <option value="Cultural & Science">Cultural & Science</option>
                        </select>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-bold small text-dark">Observation Title / Material Name</label>
                        <input type="text" name="title" class="form-control fw-bold border-success-subtle" placeholder="e.g. Pink Tower Size Discrimination" required>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-bold small text-dark">Mastery Progress Stage</label>
                        <select class="form-select fw-bold border-success-subtle" name="mastery_level" required>
                            <option value="Introduced">Introduced (Presentation Stage)</option>
                            <option value="Working" selected>Working (Active Practice)</option>
                            <option value="Mastered">Mastered (Independent Competency)</option>
                        </select>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-bold small text-dark">Observation Date</label>
                        <input type="date" name="observed_at" class="form-control fw-bold border-success-subtle" value="{{ date('Y-m-d') }}" required>
                    </div>

                    <div class="col-md-6 d-flex align-items-end">
                        <div class="form-check form-switch mb-2">
                            <input class="form-check-input" type="checkbox" name="is_family_visible" id="familyVisibleSwitch" value="1" checked>
                            <label class="form-check-label fw-bold text-dark small" for="familyVisibleSwitch">Share with Verified Parents</label>
                        </div>
                    </div>

                    <div class="col-12">
                        <label class="form-label fw-bold small text-dark">Directress Qualitative Focus Notes</label>
                        <textarea name="notes" class="form-control fw-bold border-success-subtle" rows="4" placeholder="Describe concentration depth, work cycle duration, self-correction, or peer interactions..." required></textarea>
                    </div>
                </div>
            </div>

            <div class="modal-footer border-0 bg-light rounded-bottom-4">
                <button type="button" class="btn btn-secondary rounded-pill px-4" data-bs-dismiss="modal">Cancel</button>
                <button type="submit" class="btn btn-success rounded-pill px-4 fw-bold">
                    <i class="bi bi-save me-1"></i> Save & Publish Observation
                </button>
            </div>
        </form>
    </div>
</div>
@endif
@endsection
