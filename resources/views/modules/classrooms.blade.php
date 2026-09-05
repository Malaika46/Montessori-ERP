@extends('layouts.app')

@section('title', 'Classrooms & Environments')

@section('content')
<!-- Hero Card -->
<div class="hero-welcome-card mb-4">
    <div class="d-flex flex-column flex-lg-row align-items-lg-center justify-content-between gap-3">
        <div>
            <div class="hero-role-pill">
                <i class="bi bi-door-open-fill"></i> Prepared Montessori Environments
            </div>
            <h1 class="hero-welcome-title fs-2 mb-1">Classroom Environments</h1>
            <p class="hero-welcome-subtitle">
                Superadmin registry for mixed-age Montessori classrooms, capacities, and lead guide assignments.
            </p>
        </div>

        <div>
            <button type="button" class="btn-pill-gold" data-bs-toggle="modal" data-bs-target="#addClassroomModal">
                <i class="bi bi-plus-circle-fill me-1"></i> Create Classroom Environment
            </button>
        </div>
    </div>
</div>

<!-- Stat Row -->
<div class="row g-3 mb-4">
    <div class="col-12 col-sm-6 col-xl-6">
        <div class="stat-box-card">
            <div class="stat-box-header">
                <span>Active Environments</span>
                <i class="bi bi-door-open stat-box-icon text-success"></i>
            </div>
            <div class="stat-box-number text-success">{{ $totalClassrooms }}</div>
            <div class="stat-box-sub">Mixed-age Montessori rooms</div>
        </div>
    </div>
</div>

<!-- Main Table Card -->
<div class="m-card">
    <div class="d-flex align-items-center justify-content-between mb-4 flex-wrap gap-3">
        <div>
            <h4 class="fw-bold mb-1 text-dark">Classrooms Directory</h4>
            <p class="text-muted small mb-0">Configured environments and enrolled student count</p>
        </div>
    </div>

    <div class="table-responsive">
        <table class="table table-hover align-middle">
            <thead class="table-light">
                <tr>
                    <th scope="col">Code</th>
                    <th scope="col">Classroom Name</th>
                    <th scope="col">Montessori Age Group</th>
                    <th scope="col">Lead Guide</th>
                    <th scope="col">Capacity</th>
                    <th scope="col">Status</th>
                    <th scope="col" class="text-end">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($classrooms as $classroom)
                    <tr>
                        <td>
                            <span class="badge bg-secondary font-monospace">{{ $classroom->code }}</span>
                        </td>
                        <td>
                            <div class="fw-bold text-dark">{{ $classroom->name }}</div>
                            <div class="small text-muted">{{ $classroom->campus->name ?? 'Main Campus' }}</div>
                        </td>
                        <td>
                            <span class="badge bg-primary-subtle text-primary border border-primary-subtle">
                                {{ $classroom->age_group }}
                            </span>
                        </td>
                        <td>
                            <span class="small text-dark fw-semibold">
                                {{ $classroom->leadTeacher->name ?? 'Unassigned' }}
                            </span>
                        </td>
                        <td>
                            <span class="small text-muted">
                                <strong>{{ $classroom->students->count() }}</strong> / {{ $classroom->capacity }} enrolled
                            </span>
                        </td>
                        <td>
                            @if($classroom->status === 'active')
                                <span class="badge bg-success text-white">Active</span>
                            @else
                                <span class="badge bg-secondary text-white">{{ ucfirst($classroom->status) }}</span>
                            @endif
                        </td>
                        <td class="text-end">
                            <div class="d-flex align-items-center justify-content-end gap-1">
                                <button type="button" class="btn btn-sm btn-outline-primary border-0" data-bs-toggle="modal" data-bs-target="#assignTeacherModal_{{ $classroom->id }}" title="Assign Teacher">
                                    <i class="bi bi-person-badge me-1"></i> Assign Guide
                                </button>

                                <button type="button" class="btn btn-sm btn-outline-success border-0" data-bs-toggle="modal" data-bs-target="#enrollStudentModal_{{ $classroom->id }}" title="Enroll Student">
                                    <i class="bi bi-person-plus me-1"></i> Enroll Student
                                </button>

                                <form action="{{ route('classrooms.destroy', $classroom->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Archive this classroom?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger border-0" title="Archive Classroom">
                                        <i class="bi bi-archive-fill"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>

                    <!-- Assign Teacher Modal -->
                    <div class="modal fade" id="assignTeacherModal_{{ $classroom->id }}" tabindex="-1" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered">
                            <div class="modal-content border-0 shadow-lg rounded-4">
                                <div class="modal-header border-bottom p-4">
                                    <h5 class="modal-title fw-bold text-dark">
                                        Assign Teacher to {{ $classroom->name }}
                                    </h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                </div>
                                <form action="{{ route('classrooms.assign-teacher', $classroom->id) }}" method="POST">
                                    @csrf
                                    <div class="modal-body p-4">
                                        <div class="mb-3">
                                            <label class="form-label fw-semibold small">Select Guide / Teacher</label>
                                            <select name="teacher_id" class="form-select" required>
                                                <option value="" disabled selected>-- Select Teacher --</option>
                                                @foreach($teachers as $t)
                                                    <option value="{{ $t->id }}">{{ $t->user->name }} ({{ $t->specialization ?? 'Guide' }})</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="form-check mb-3">
                                            <input class="form-check-input" type="checkbox" name="is_lead" value="1" id="lead_{{ $classroom->id }}">
                                            <label class="form-check-label small" for="lead_{{ $classroom->id }}">
                                                Set as Lead Guide for this classroom environment
                                            </label>
                                        </div>
                                    </div>
                                    <div class="modal-footer border-top p-3 bg-light rounded-bottom-4">
                                        <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Cancel</button>
                                        <button type="submit" class="btn btn-m-primary btn-sm">Assign Teacher</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>

                    <!-- Enroll Student Modal -->
                    <div class="modal fade" id="enrollStudentModal_{{ $classroom->id }}" tabindex="-1" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered">
                            <div class="modal-content border-0 shadow-lg rounded-4">
                                <div class="modal-header border-bottom p-4">
                                    <h5 class="modal-title fw-bold text-dark">
                                        Enroll Student in {{ $classroom->name }}
                                    </h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                </div>
                                <form action="{{ route('classrooms.enroll-student', $classroom->id) }}" method="POST">
                                    @csrf
                                    <div class="modal-body p-4">
                                        <div class="mb-3">
                                            <label class="form-label fw-semibold small">Select Student</label>
                                            <select name="student_id" class="form-select" required>
                                                <option value="" disabled selected>-- Select Student --</option>
                                                @foreach($students as $st)
                                                    <option value="{{ $st->id }}">{{ $st->user->name }} ({{ $st->student_number }}) - Current: {{ $st->classroom->name ?? 'Unassigned' }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="modal-footer border-top p-3 bg-light rounded-bottom-4">
                                        <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Cancel</button>
                                        <button type="submit" class="btn btn-m-primary btn-sm">Enroll Student</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="text-center py-4 text-muted">
                            No classrooms configured. Click <strong>Create Classroom Environment</strong> to add one!
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-3">
        {{ $classrooms->links() }}
    </div>
</div>

<!-- Add Classroom Modal -->
<div class="modal fade" id="addClassroomModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4">
            <div class="modal-header border-bottom p-4">
                <div>
                    <h5 class="modal-title fw-bold text-dark">
                        <i class="bi bi-plus-circle-fill text-success me-2"></i> Create Classroom Environment
                    </h5>
                    <p class="text-muted small mb-0">Configure a new Montessori classroom environment.</p>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <form action="{{ route('classrooms.store') }}" method="POST">
                @csrf
                <div class="modal-body p-4">
                    <div class="row g-3">
                        <div class="col-md-8">
                            <label for="name" class="form-label fw-semibold small">Classroom Environment Name</label>
                            <input type="text" name="name" class="form-control" placeholder="e.g. Children's House Beta" required>
                        </div>
                        <div class="col-md-4">
                            <label for="code" class="form-label fw-semibold small">Code</label>
                            <input type="text" name="code" class="form-control font-monospace" placeholder="CH-02" required>
                        </div>
                    </div>

                    <div class="row g-3 mt-1">
                        <div class="col-md-6">
                            <label for="age_group" class="form-label fw-semibold small">Montessori Age Group</label>
                            <select name="age_group" class="form-select" required>
                                <option value="Toddler (1.5-3 yrs)">Toddler (1.5-3 yrs)</option>
                                <option value="Primary (3-6 yrs)" selected>Primary (3-6 yrs)</option>
                                <option value="Lower Elementary (6-9 yrs)">Lower Elementary (6-9 yrs)</option>
                                <option value="Upper Elementary (9-12 yrs)">Upper Elementary (9-12 yrs)</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label for="campus_id" class="form-label fw-semibold small">Campus</label>
                            <select name="campus_id" class="form-select">
                                @foreach($campuses as $campus)
                                    <option value="{{ $campus->id }}">{{ $campus->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="row g-3 mt-1">
                        <div class="col-md-6">
                            <label for="capacity" class="form-label fw-semibold small">Student Capacity</label>
                            <input type="number" name="capacity" class="form-control" value="25" min="1" required>
                        </div>
                        <div class="col-md-6">
                            <label for="lead_teacher_id" class="form-label fw-semibold small">Lead Guide (Teacher)</label>
                            <select name="lead_teacher_id" class="form-select">
                                <option value="">-- Unassigned --</option>
                                @foreach($teachers as $t)
                                    <option value="{{ $t->user->id }}">{{ $t->user->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="mt-3">
                        <label for="description" class="form-label fw-semibold small">Environment Description</label>
                        <textarea name="description" class="form-control" rows="2" placeholder="Montessori material layout, focus area..."></textarea>
                    </div>

                    <input type="hidden" name="status" value="active">
                </div>

                <div class="modal-footer border-top p-3 bg-light rounded-bottom-4">
                    <button type="button" class="btn btn-secondary btn-sm px-3" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-m-primary btn-sm px-4">
                        <i class="bi bi-check-circle me-1"></i> Save Environment
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
