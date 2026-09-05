@extends('layouts.app')

@section('title', 'Montessori Curriculum Management')

@section('content')
<!-- Hero Card -->
<div class="hero-welcome-card mb-4">
    <div class="d-flex flex-column flex-lg-row align-items-lg-center justify-content-between gap-3">
        <div>
            <div class="hero-role-pill">
                <i class="bi bi-journal-bookmark-fill"></i> Montessori Curriculum Roadmap
            </div>
            <h1 class="hero-welcome-title fs-2 mb-1">Montessori Curriculum Management</h1>
            <p class="hero-welcome-subtitle">
                Manage material maps, learning objectives, and sequential developmental frameworks across all 5 core Montessori avenues.
            </p>
        </div>

        @php
            $user = Auth::user();
            $roleName = $user && $user->role ? $user->role->name : 'guest';
            $canEdit = in_array($roleName, ['superadmin', 'principal', 'admin', 'teacher']);
        @endphp

        @if($canEdit)
        <div>
            <button type="button" class="btn-pill-gold" data-bs-toggle="modal" data-bs-target="#addCurriculumModal">
                <i class="bi bi-plus-circle-fill me-1"></i> Add Montessori Curriculum
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
            <div class="stat-box-sub">Fine motor & self-care</div>
        </div>
    </div>

    <div class="col-6 col-md-4 col-xl">
        <div class="stat-box-card h-100">
            <div class="stat-box-header">
                <span>Sensorial</span>
                <i class="bi bi-eye-fill text-primary"></i>
            </div>
            <div class="stat-box-number text-primary">{{ $avenueCounts['Sensorial'] ?? 0 }}</div>
            <div class="stat-box-sub">Refining 5 senses</div>
        </div>
    </div>

    <div class="col-6 col-md-4 col-xl">
        <div class="stat-box-card h-100">
            <div class="stat-box-header">
                <span>Mathematics</span>
                <i class="bi bi-calculator-fill text-warning"></i>
            </div>
            <div class="stat-box-number text-warning">{{ $avenueCounts['Mathematics'] ?? 0 }}</div>
            <div class="stat-box-sub">Concrete quantities</div>
        </div>
    </div>

    <div class="col-6 col-md-4 col-xl">
        <div class="stat-box-card h-100">
            <div class="stat-box-header">
                <span>Language</span>
                <i class="bi bi-chat-quote-fill text-info"></i>
            </div>
            <div class="stat-box-number text-info">{{ $avenueCounts['Language'] ?? 0 }}</div>
            <div class="stat-box-sub">Phonics & writing</div>
        </div>
    </div>

    <div class="col-6 col-md-4 col-xl">
        <div class="stat-box-card h-100">
            <div class="stat-box-header">
                <span>Cultural</span>
                <i class="bi bi-globe-americas text-danger"></i>
            </div>
            <div class="stat-box-number text-danger">{{ $avenueCounts['Cultural'] ?? 0 }}</div>
            <div class="stat-box-sub">Geography, science & art</div>
        </div>
    </div>
</div>

<!-- Main Curriculum Card -->
<div class="m-card">
    <div class="d-flex align-items-center justify-content-between mb-4 flex-wrap gap-3">
        <div>
            <h4 class="fw-bold mb-1 text-dark">Curriculum Material Directory</h4>
            <p class="text-muted small mb-0">Showing material maps scoped to your authorized classrooms</p>
        </div>

        <form action="{{ route('curriculum.index') }}" method="GET" class="d-flex gap-2 flex-wrap align-items-center">
            <input type="text" name="search" class="form-control form-control-sm" placeholder="Search title or objectives..." value="{{ request('search') }}" style="width: 210px;">
            
            <select name="avenue" class="form-select form-select-sm" style="width: 160px;" onchange="this.form.submit()">
                <option value="">All Avenues</option>
                <option value="Practical Life" {{ request('avenue') == 'Practical Life' ? 'selected' : '' }}>Practical Life</option>
                <option value="Sensorial" {{ request('avenue') == 'Sensorial' ? 'selected' : '' }}>Sensorial</option>
                <option value="Mathematics" {{ request('avenue') == 'Mathematics' ? 'selected' : '' }}>Mathematics</option>
                <option value="Language" {{ request('avenue') == 'Language' ? 'selected' : '' }}>Language</option>
                <option value="Cultural" {{ request('avenue') == 'Cultural' ? 'selected' : '' }}>Cultural</option>
            </select>

            <select name="classroom_id" class="form-select form-select-sm" style="width: 170px;" onchange="this.form.submit()">
                <option value="">All Classrooms</option>
                @foreach($classrooms as $c)
                    <option value="{{ $c->id }}" {{ request('classroom_id') == $c->id ? 'selected' : '' }}>{{ $c->name }}</option>
                @endforeach
            </select>

            <button type="submit" class="btn btn-sm btn-m-primary"><i class="bi bi-search"></i> Search</button>
            @if(request('search') || request('avenue') || request('classroom_id'))
                <a href="{{ route('curriculum.index') }}" class="btn btn-sm btn-outline-secondary">Reset</a>
            @endif
        </form>
    </div>

    <!-- Cards Grid -->
    <div class="row g-3">
        @forelse($curriculumItems as $item)
            @php
                $badgeBg = match($item->avenue) {
                    'Practical Life' => 'bg-success-subtle text-success border-success-subtle',
                    'Sensorial' => 'bg-primary-subtle text-primary border-primary-subtle',
                    'Mathematics' => 'bg-warning-subtle text-dark border-warning-subtle',
                    'Language' => 'bg-info-subtle text-info border-info-subtle',
                    'Cultural' => 'bg-danger-subtle text-danger border-danger-subtle',
                    default => 'bg-secondary-subtle text-secondary'
                };
            @endphp
            <div class="col-12 col-md-6 col-xl-4">
                <div class="card h-100 border shadow-sm rounded-3">
                    <div class="card-header bg-white border-bottom-0 pt-3 pb-0 d-flex justify-content-between align-items-center">
                        <span class="badge border {{ $badgeBg }} px-2 py-1">
                            <i class="bi bi-tag-fill me-1"></i> {{ $item->avenue }}
                        </span>
                        <span class="badge bg-light text-muted border">
                            {{ $item->age_group ?? 'Agnostic Level' }}
                        </span>
                    </div>

                    <div class="card-body">
                        <h5 class="fw-bold text-dark mb-2">{{ $item->title }}</h5>
                        <p class="text-muted small mb-3 text-truncate-2">
                            {{ $item->description }}
                        </p>

                        @if($item->learning_objectives)
                            <div class="p-2 bg-light rounded-2 border mb-3">
                                <div class="small fw-bold text-dark mb-1"><i class="bi bi-bullseye text-primary me-1"></i> Objectives:</div>
                                <div class="small text-secondary">{{ $item->learning_objectives }}</div>
                            </div>
                        @endif

                        <div class="d-flex align-items-center justify-content-between pt-2 border-top">
                            <div class="small text-muted">
                                <i class="bi bi-door-open me-1"></i> {{ $item->classroom ? $item->classroom->name : 'All Classrooms' }}
                            </div>

                            @if($canEdit)
                            <div class="d-flex gap-1">
                                <button type="button" class="btn btn-sm btn-outline-primary border-0" data-bs-toggle="modal" data-bs-target="#editCurriculumModal_{{ $item->id }}" title="Edit">
                                    <i class="bi bi-pencil-square"></i>
                                </button>
                                <form action="{{ route('curriculum.destroy', $item->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete curriculum item {{ $item->title }}?');">
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

            <!-- Edit Modal for Item -->
            @if($canEdit)
            <div class="modal fade" id="editCurriculumModal_{{ $item->id }}" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered modal-lg">
                    <div class="modal-content border-0 shadow">
                        <div class="modal-header bg-dark text-white">
                            <h5 class="modal-title fw-bold"><i class="bi bi-pencil-square me-2"></i> Edit Curriculum Item</h5>
                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <form action="{{ route('curriculum.update', $item->id) }}" method="POST">
                            @csrf
                            @method('PUT')
                            <div class="modal-body p-4">
                                <div class="row g-3">
                                    <div class="col-md-8">
                                        <label class="form-label fw-semibold">Item Title</label>
                                        <input type="text" name="title" class="form-control" value="{{ $item->title }}" required>
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label fw-semibold">Montessori Avenue</label>
                                        <select name="avenue" class="form-select" required>
                                            <option value="Practical Life" {{ $item->avenue == 'Practical Life' ? 'selected' : '' }}>Practical Life</option>
                                            <option value="Sensorial" {{ $item->avenue == 'Sensorial' ? 'selected' : '' }}>Sensorial</option>
                                            <option value="Mathematics" {{ $item->avenue == 'Mathematics' ? 'selected' : '' }}>Mathematics</option>
                                            <option value="Language" {{ $item->avenue == 'Language' ? 'selected' : '' }}>Language</option>
                                            <option value="Cultural" {{ $item->avenue == 'Cultural' ? 'selected' : '' }}>Cultural</option>
                                        </select>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold">Classroom Environment</label>
                                        <select name="classroom_id" class="form-select">
                                            <option value="">Global / All Classrooms</option>
                                            @foreach($classrooms as $c)
                                                <option value="{{ $c->id }}" {{ $item->classroom_id == $c->id ? 'selected' : '' }}>{{ $c->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold">Target Age Group / Level</label>
                                        <input type="text" name="age_group" class="form-control" value="{{ $item->age_group }}" placeholder="e.g. 3 - 5 Years">
                                    </div>
                                    <div class="col-12">
                                        <label class="form-label fw-semibold">Description & Material Details</label>
                                        <textarea name="description" class="form-control" rows="3" required>{{ $item->description }}</textarea>
                                    </div>
                                    <div class="col-12">
                                        <label class="form-label fw-semibold">Direct Learning Objectives</label>
                                        <textarea name="learning_objectives" class="form-control" rows="2">{{ $item->learning_objectives }}</textarea>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold">Status</label>
                                        <select name="status" class="form-select" required>
                                            <option value="active" {{ $item->status == 'active' ? 'selected' : '' }}>Active</option>
                                            <option value="archived" {{ $item->status == 'archived' ? 'selected' : '' }}>Archived</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="modal-footer bg-light">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                <button type="submit" class="btn btn-m-primary px-4">Update Changes</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            @endif

        @empty
            <div class="col-12 text-center py-5">
                <i class="bi bi-journal-x fs-1 text-muted d-block mb-2"></i>
                <h5 class="fw-bold text-dark">No Montessori Curriculum Found</h5>
                <p class="text-muted small">No curriculum items match your filter criteria.</p>
                @if($canEdit)
                <button type="button" class="btn btn-sm btn-m-primary mt-2" data-bs-toggle="modal" data-bs-target="#addCurriculumModal">
                    <i class="bi bi-plus-circle me-1"></i> Add First Curriculum Item
                </button>
                @endif
            </div>
        @endforelse
    </div>

    <!-- Pagination -->
    <div class="mt-4">
        {{ $curriculumItems->links() }}
    </div>
</div>

<!-- Modal: Add New Curriculum Item -->
@if($canEdit)
<div class="modal fade" id="addCurriculumModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-dark text-white">
                <h5 class="modal-title fw-bold"><i class="bi bi-journal-plus me-2"></i> Add Montessori Curriculum Item</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('curriculum.store') }}" method="POST">
                @csrf
                <div class="modal-body p-4">
                    <div class="row g-3">
                        <div class="col-md-8">
                            <label class="form-label fw-semibold">Item Title <span class="text-danger">*</span></label>
                            <input type="text" name="title" class="form-control" placeholder="e.g. Dry Pouring & Spooning Exercises" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Montessori Avenue <span class="text-danger">*</span></label>
                            <select name="avenue" class="form-select" required>
                                <option value="Practical Life">Practical Life</option>
                                <option value="Sensorial">Sensorial</option>
                                <option value="Mathematics">Mathematics</option>
                                <option value="Language">Language</option>
                                <option value="Cultural">Cultural</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Target Classroom Environment</label>
                            <select name="classroom_id" class="form-select">
                                <option value="">Global (All Classrooms)</option>
                                @foreach($classrooms as $c)
                                    <option value="{{ $c->id }}">{{ $c->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Target Age Group / Level</label>
                            <input type="text" name="age_group" class="form-control" placeholder="e.g. 3 - 4.5 Years">
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-semibold">Description & Material Map <span class="text-danger">*</span></label>
                            <textarea name="description" class="form-control" rows="3" placeholder="Describe the material, presentation steps, and direct purpose..." required></textarea>
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-semibold">Learning Objectives</label>
                            <textarea name="learning_objectives" class="form-control" rows="2" placeholder="Key developmental skills (e.g. Fine motor control, 1-to-1 quantity correspondence)..."></textarea>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Status</label>
                            <select name="status" class="form-select" required>
                                <option value="active">Active</option>
                                <option value="archived">Archived</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn-pill-gold px-4">Publish Curriculum Item</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif
@endsection
