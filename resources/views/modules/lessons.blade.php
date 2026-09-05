@extends('layouts.app')

@section('title', 'Montessori Lesson Planning')

@section('content')
<!-- Hero Card -->
<div class="hero-welcome-card mb-4">
    <div class="d-flex flex-column flex-lg-row align-items-lg-center justify-content-between gap-3">
        <div>
            <div class="hero-role-pill">
                <i class="bi bi-journal-text"></i> Academic & Lesson Planning
            </div>
            <h1 class="hero-welcome-title fs-2 mb-1">Montessori Lesson Planning</h1>
            <p class="hero-welcome-subtitle">
                Schedule, track presentations, and organize weekly lesson plans across your assigned classroom environments.
            </p>
        </div>

        @php
            $user = Auth::user();
            $roleName = $user && $user->role ? $user->role->name : 'guest';
            $canEdit = in_array($roleName, ['superadmin', 'principal', 'admin', 'teacher']);
        @endphp

        @if($canEdit)
        <div>
            <button type="button" class="btn-pill-gold" data-bs-toggle="modal" data-bs-target="#addLessonModal">
                <i class="bi bi-plus-circle-fill me-1"></i> Create Lesson Plan
            </button>
        </div>
        @endif
    </div>
</div>

<!-- Alert Notifications -->
@if(session('success'))
<div class="alert alert-success alert-dismissible fade show" role="alert">
    <i class="bi bi-check-circle-fill me-2"></i> {{ session('success') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
</div>
@endif

<!-- Avenue Stats Row -->
<div class="row g-3 mb-4">
    <div class="col-6 col-md-4 col-xl">
        <div class="stat-box-card h-100">
            <div class="stat-box-header">
                <span>Practical Life</span>
                <i class="bi bi-hand-index-fill text-success"></i>
            </div>
            <div class="stat-box-number text-success">{{ $avenueCounts['Practical Life'] ?? 0 }}</div>
            <div class="stat-box-sub">Scheduled Lessons</div>
        </div>
    </div>

    <div class="col-6 col-md-4 col-xl">
        <div class="stat-box-card h-100">
            <div class="stat-box-header">
                <span>Sensorial</span>
                <i class="bi bi-eye-fill text-primary"></i>
            </div>
            <div class="stat-box-number text-primary">{{ $avenueCounts['Sensorial'] ?? 0 }}</div>
            <div class="stat-box-sub">Scheduled Lessons</div>
        </div>
    </div>

    <div class="col-6 col-md-4 col-xl">
        <div class="stat-box-card h-100">
            <div class="stat-box-header">
                <span>Mathematics</span>
                <i class="bi bi-calculator-fill text-warning"></i>
            </div>
            <div class="stat-box-number text-warning">{{ $avenueCounts['Mathematics'] ?? 0 }}</div>
            <div class="stat-box-sub">Scheduled Lessons</div>
        </div>
    </div>

    <div class="col-6 col-md-4 col-xl">
        <div class="stat-box-card h-100">
            <div class="stat-box-header">
                <span>Language</span>
                <i class="bi bi-chat-quote-fill text-info"></i>
            </div>
            <div class="stat-box-number text-info">{{ $avenueCounts['Language'] ?? 0 }}</div>
            <div class="stat-box-sub">Scheduled Lessons</div>
        </div>
    </div>

    <div class="col-6 col-md-4 col-xl">
        <div class="stat-box-card h-100">
            <div class="stat-box-header">
                <span>Cultural</span>
                <i class="bi bi-globe-americas text-danger"></i>
            </div>
            <div class="stat-box-number text-danger">{{ $avenueCounts['Cultural'] ?? 0 }}</div>
            <div class="stat-box-sub">Scheduled Lessons</div>
        </div>
    </div>
</div>

<!-- Main Lesson Planning Container -->
<div class="m-card">
    <div class="d-flex align-items-center justify-content-between mb-4 flex-wrap gap-3">
        <div>
            <h4 class="fw-bold mb-1 text-dark">Scheduled Lesson Schedule</h4>
            <p class="text-muted small mb-0">Showing lessons scoped to authorized classrooms</p>
        </div>

        <form action="{{ route('lessons.index') }}" method="GET" class="d-flex gap-2 flex-wrap align-items-center">
            <input type="text" name="search" class="form-control form-control-sm" placeholder="Search lesson or materials..." value="{{ request('search') }}" style="width: 200px;">
            
            <select name="avenue" class="form-select form-select-sm" style="width: 150px;" onchange="this.form.submit()">
                <option value="">All Avenues</option>
                <option value="Practical Life" {{ request('avenue') == 'Practical Life' ? 'selected' : '' }}>Practical Life</option>
                <option value="Sensorial" {{ request('avenue') == 'Sensorial' ? 'selected' : '' }}>Sensorial</option>
                <option value="Mathematics" {{ request('avenue') == 'Mathematics' ? 'selected' : '' }}>Mathematics</option>
                <option value="Language" {{ request('avenue') == 'Language' ? 'selected' : '' }}>Language</option>
                <option value="Cultural" {{ request('avenue') == 'Cultural' ? 'selected' : '' }}>Cultural</option>
            </select>

            <select name="classroom_id" class="form-select form-select-sm" style="width: 160px;" onchange="this.form.submit()">
                <option value="">All Classrooms</option>
                @foreach($classrooms as $c)
                    <option value="{{ $c->id }}" {{ request('classroom_id') == $c->id ? 'selected' : '' }}>{{ $c->name }}</option>
                @endforeach
            </select>

            <select name="status" class="form-select form-select-sm" style="width: 130px;" onchange="this.form.submit()">
                <option value="">All Statuses</option>
                <option value="planned" {{ request('status') == 'planned' ? 'selected' : '' }}>Planned</option>
                <option value="presented" {{ request('status') == 'presented' ? 'selected' : '' }}>Presented</option>
                <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Completed</option>
            </select>

            <button type="submit" class="btn btn-sm btn-m-primary"><i class="bi bi-search"></i> Search</button>
            @if(request('search') || request('avenue') || request('classroom_id') || request('status'))
                <a href="{{ route('lessons.index') }}" class="btn btn-sm btn-outline-secondary">Reset</a>
            @endif
        </form>
    </div>

    <!-- Cards Grid -->
    <div class="row g-3">
        @forelse($lessonPlans as $plan)
            @php
                $badgeBg = match($plan->avenue) {
                    'Practical Life' => 'bg-success-subtle text-success border-success-subtle',
                    'Sensorial' => 'bg-primary-subtle text-primary border-primary-subtle',
                    'Mathematics' => 'bg-warning-subtle text-dark border-warning-subtle',
                    'Language' => 'bg-info-subtle text-info border-info-subtle',
                    'Cultural' => 'bg-danger-subtle text-danger border-danger-subtle',
                    default => 'bg-secondary-subtle text-secondary'
                };

                $statusPill = match($plan->status) {
                    'planned' => 'bg-warning text-dark',
                    'presented' => 'bg-primary text-white',
                    'completed' => 'bg-success text-white',
                    default => 'bg-secondary text-white'
                };
            @endphp
            <div class="col-12 col-md-6 col-xl-4">
                <div class="card h-100 border shadow-sm rounded-3">
                    <div class="card-header bg-white border-bottom-0 pt-3 pb-0 d-flex justify-content-between align-items-center">
                        <span class="badge border {{ $badgeBg }} px-2 py-1">
                            <i class="bi bi-book-fill me-1"></i> {{ $plan->avenue }}
                        </span>
                        <span class="badge {{ $statusPill }} px-2 py-1 text-uppercase" style="font-size: 0.72rem;">
                            {{ $plan->status }}
                        </span>
                    </div>

                    <div class="card-body">
                        <h5 class="fw-bold text-dark mb-1">{{ $plan->title }}</h5>
                        
                        <div class="d-flex align-items-center gap-2 mb-2 text-muted small">
                            <span><i class="bi bi-door-open text-primary me-1"></i> {{ $plan->classroom ? $plan->classroom->name : 'N/A' }}</span>
                            <span>•</span>
                            <span><i class="bi bi-calendar-event text-danger me-1"></i> {{ \Carbon\Carbon::parse($plan->scheduled_date)->format('M d, Y') }}</span>
                        </div>

                        <p class="text-muted small mb-3 text-truncate-2">
                            {{ $plan->description }}
                        </p>

                        @if($plan->materials_needed)
                            <div class="p-2 bg-light rounded-2 border mb-3">
                                <div class="small fw-bold text-dark mb-1"><i class="bi bi-box-seam text-warning me-1"></i> Required Materials:</div>
                                <div class="small text-secondary">{{ $plan->materials_needed }}</div>
                            </div>
                        @endif

                        <div class="d-flex align-items-center justify-content-between pt-2 border-top">
                            <div class="small text-muted">
                                <i class="bi bi-person-badge me-1"></i> {{ $plan->teacher && $plan->teacher->user ? $plan->teacher->user->name : 'Guide / Staff' }}
                            </div>

                            @if($canEdit)
                            <div class="d-flex gap-1">
                                <button type="button" class="btn btn-sm btn-outline-primary border-0" data-bs-toggle="modal" data-bs-target="#editLessonModal_{{ $plan->id }}" title="Edit">
                                    <i class="bi bi-pencil-square"></i>
                                </button>
                                <form action="{{ route('lessons.destroy', $plan->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete lesson plan {{ $plan->title }}?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger border-0" title="Delete">
                                        <i class="bi bi-trash-fill"></i>
                                    </button>
                                </form>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- Edit Modal for Lesson Plan -->
            @if($canEdit)
            <div class="modal fade" id="editLessonModal_{{ $plan->id }}" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered modal-lg">
                    <div class="modal-content border-0 shadow">
                        <div class="modal-header bg-dark text-white">
                            <h5 class="modal-title fw-bold"><i class="bi bi-pencil-square me-2"></i> Edit Lesson Plan</h5>
                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <form action="{{ route('lessons.update', $plan->id) }}" method="POST">
                            @csrf
                            @method('PUT')
                            <div class="modal-body p-4">
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold">Classroom Environment</label>
                                        <select name="classroom_id" class="form-select" required>
                                            @foreach($classrooms as $c)
                                                <option value="{{ $c->id }}" {{ $plan->classroom_id == $c->id ? 'selected' : '' }}>{{ $c->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold">Montessori Avenue</label>
                                        <select name="avenue" class="form-select" required>
                                            <option value="Practical Life" {{ $plan->avenue == 'Practical Life' ? 'selected' : '' }}>Practical Life</option>
                                            <option value="Sensorial" {{ $plan->avenue == 'Sensorial' ? 'selected' : '' }}>Sensorial</option>
                                            <option value="Mathematics" {{ $plan->avenue == 'Mathematics' ? 'selected' : '' }}>Mathematics</option>
                                            <option value="Language" {{ $plan->avenue == 'Language' ? 'selected' : '' }}>Language</option>
                                            <option value="Cultural" {{ $plan->avenue == 'Cultural' ? 'selected' : '' }}>Cultural</option>
                                        </select>
                                    </div>
                                    <div class="col-md-8">
                                        <label class="form-label fw-semibold">Lesson Title</label>
                                        <input type="text" name="title" class="form-control" value="{{ $plan->title }}" required>
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label fw-semibold">Scheduled Date</label>
                                        <input type="date" name="scheduled_date" class="form-control" value="{{ $plan->scheduled_date }}" required>
                                    </div>
                                    <div class="col-12">
                                        <label class="form-label fw-semibold">Presentation Steps & Description</label>
                                        <textarea name="description" class="form-control" rows="3">{{ $plan->description }}</textarea>
                                    </div>
                                    <div class="col-12">
                                        <label class="form-label fw-semibold">Required Materials & Trays</label>
                                        <textarea name="materials_needed" class="form-control" rows="2">{{ $plan->materials_needed }}</textarea>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold">Presentation Status</label>
                                        <select name="status" class="form-select" required>
                                            <option value="planned" {{ $plan->status == 'planned' ? 'selected' : '' }}>Planned</option>
                                            <option value="presented" {{ $plan->status == 'presented' ? 'selected' : '' }}>Presented</option>
                                            <option value="completed" {{ $plan->status == 'completed' ? 'selected' : '' }}>Completed</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="modal-footer bg-light">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                <button type="submit" class="btn btn-m-primary px-4">Save Changes</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            @endif

        @empty
            <div class="col-12 text-center py-5">
                <i class="bi bi-calendar-x fs-1 text-muted d-block mb-2"></i>
                <h5 class="fw-bold text-dark">No Lesson Plans Scheduled</h5>
                <p class="text-muted small">No lesson plans match your filter criteria.</p>
                @if($canEdit)
                <button type="button" class="btn btn-sm btn-m-primary mt-2" data-bs-toggle="modal" data-bs-target="#addLessonModal">
                    <i class="bi bi-plus-circle me-1"></i> Create First Lesson Plan
                </button>
                @endif
            </div>
        @endforelse
    </div>

    <!-- Pagination -->
    <div class="mt-4">
        {{ $lessonPlans->links() }}
    </div>
</div>

<!-- Modal: Create Lesson Plan -->
@if($canEdit)
<div class="modal fade" id="addLessonModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-dark text-white">
                <h5 class="modal-title fw-bold"><i class="bi bi-plus-circle me-2"></i> Schedule New Lesson Plan</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('lessons.store') }}" method="POST">
                @csrf
                <div class="modal-body p-4">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Classroom Environment <span class="text-danger">*</span></label>
                            <select name="classroom_id" class="form-select" required>
                                <option value="">Select Classroom</option>
                                @foreach($classrooms as $c)
                                    <option value="{{ $c->id }}">{{ $c->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Montessori Avenue <span class="text-danger">*</span></label>
                            <select name="avenue" class="form-select" required>
                                <option value="Practical Life">Practical Life</option>
                                <option value="Sensorial">Sensorial</option>
                                <option value="Mathematics">Mathematics</option>
                                <option value="Language">Language</option>
                                <option value="Cultural">Cultural</option>
                            </select>
                        </div>
                        <div class="col-md-8">
                            <label class="form-label fw-semibold">Lesson Title <span class="text-danger">*</span></label>
                            <input type="text" name="title" class="form-control" placeholder="e.g. Spindle Box Quantity Presentation" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Scheduled Date <span class="text-danger">*</span></label>
                            <input type="date" name="scheduled_date" class="form-control" value="{{ date('Y-m-d') }}" required>
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-semibold">Presentation Overview & Steps</label>
                            <textarea name="description" class="form-control" rows="3" placeholder="Detail presentation sequence, key points of interest, and control of error..."></textarea>
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-semibold">Required Materials & Apparatus</label>
                            <textarea name="materials_needed" class="form-control" rows="2" placeholder="List apparatus, trays, mats, or cards required..."></textarea>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Status</label>
                            <select name="status" class="form-select" required>
                                <option value="planned">Planned</option>
                                <option value="presented">Presented</option>
                                <option value="completed">Completed</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn-pill-gold px-4">Schedule Lesson Plan</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif
@endsection
