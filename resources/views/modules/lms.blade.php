@extends('layouts.app')

@section('title', 'Gamified Student LMS')

@section('content')
<!-- Hero Card -->
<div class="hero-welcome-card mb-4">
    <div class="d-flex flex-column flex-lg-row align-items-lg-center justify-content-between gap-3">
        <div>
            <div class="hero-role-pill">
                <i class="bi bi-controller"></i> Gamified Montessori Learning System
            </div>
            <h1 class="hero-welcome-title fs-2 mb-1">Gamified LMS Management</h1>
            <p class="hero-welcome-subtitle">
                Superadmin visibility into teacher-created learning paths, skill quests, XP rewards, and student progress.
            </p>
        </div>

        <div>
            <button type="button" class="btn-pill-gold" data-bs-toggle="modal" data-bs-target="#addPathModal">
                <i class="bi bi-plus-circle-fill me-1"></i> Create Learning Path
            </button>
        </div>
    </div>
</div>

<!-- Stat Row -->
<div class="row g-3 mb-4">
    <div class="col-12 col-sm-4 col-xl-4">
        <div class="stat-box-card">
            <div class="stat-box-header">
                <span>Learning Paths</span>
                <i class="bi bi-journal-check stat-box-icon text-primary"></i>
            </div>
            <div class="stat-box-number">{{ $totalPaths }}</div>
            <div class="stat-box-sub">Active curriculum tracks</div>
        </div>
    </div>

    <div class="col-12 col-sm-4 col-xl-4">
        <div class="stat-box-card">
            <div class="stat-box-header">
                <span>Total Quests & Activities</span>
                <i class="bi bi-ui-checks-grid stat-box-icon text-info"></i>
            </div>
            <div class="stat-box-number text-info">{{ $totalActivities }}</div>
            <div class="stat-box-sub">Quizzes, exercises & games</div>
        </div>
    </div>

    <div class="col-12 col-sm-4 col-xl-4">
        <div class="stat-box-card">
            <div class="stat-box-header">
                <span>Total Student XP Earned</span>
                <i class="bi bi-trophy-fill stat-box-icon text-warning"></i>
            </div>
            <div class="stat-box-number text-warning">{{ number_format($totalXpGranted) }} XP</div>
            <div class="stat-box-sub">Gamified mastery engagement</div>
        </div>
    </div>
</div>

<!-- Main Table Card -->
<div class="m-card">
    <div class="d-flex align-items-center justify-content-between mb-4 flex-wrap gap-3">
        <div>
            <h4 class="fw-bold mb-1 text-dark">Montessori Learning Paths</h4>
            <p class="text-muted small mb-0">Teacher authorship & publication status</p>
        </div>
    </div>

    <div class="table-responsive">
        <table class="table table-hover align-middle">
            <thead class="table-light">
                <tr>
                    <th scope="col">Title & Domain</th>
                    <th scope="col">Author (Teacher)</th>
                    <th scope="col">Target Classroom</th>
                    <th scope="col">Quests / Activities</th>
                    <th scope="col">Publication Status</th>
                    <th scope="col" class="text-end">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($learningPaths as $path)
                    <tr>
                        <td>
                            <div class="fw-bold text-dark">{{ $path->title }}</div>
                            <span class="badge bg-success-subtle text-success border border-success-subtle me-1">
                                {{ $path->montessori_domain }}
                            </span>
                        </td>
                        <td>
                            <span class="small text-dark fw-semibold">
                                <i class="bi bi-person-badge me-1"></i> {{ $path->teacher->name ?? 'Superadmin' }}
                            </span>
                        </td>
                        <td>
                            <span class="small text-muted">{{ $path->classroom->name ?? 'All Classrooms' }}</span>
                        </td>
                        <td>
                            <span class="badge bg-light text-dark border">
                                {{ $path->activities->count() }} Activities
                            </span>
                        </td>
                        <td>
                            @if($path->is_published)
                                <span class="badge bg-success text-white">Published</span>
                            @else
                                <span class="badge bg-secondary text-white">Draft (Unpublished)</span>
                            @endif
                        </td>
                        <td class="text-end">
                            <div class="d-flex align-items-center justify-content-end gap-1">
                                <form action="{{ route('lms.paths.toggle-publish', $path->id) }}" method="POST" class="d-inline">
                                    @csrf
                                    <button type="submit" class="btn btn-sm btn-outline-warning border-0" title="Toggle Publish Status">
                                        <i class="bi bi-eye-slash-fill"></i> {{ $path->is_published ? 'Unpublish' : 'Publish' }}
                                    </button>
                                </form>

                                <button type="button" class="btn btn-sm btn-outline-primary border-0" data-bs-toggle="modal" data-bs-target="#addActivityModal_{{ $path->id }}" title="Add Quest / Activity">
                                    <i class="bi bi-plus-lg"></i> Add Quest
                                </button>

                                <form action="{{ route('lms.paths.destroy', $path->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Archive this learning path?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger border-0" title="Archive Path">
                                        <i class="bi bi-archive-fill"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>

                    <!-- Add Activity Modal for each path -->
                    <div class="modal fade" id="addActivityModal_{{ $path->id }}" tabindex="-1" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered">
                            <div class="modal-content border-0 shadow-lg rounded-4">
                                <div class="modal-header border-bottom p-4">
                                    <h5 class="modal-title fw-bold text-dark">
                                        Add Quest / Activity to {{ $path->title }}
                                    </h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                </div>
                                <form action="{{ route('lms.activities.store', $path->id) }}" method="POST">
                                    @csrf
                                    <div class="modal-body p-4">
                                        <div class="mb-3">
                                            <label class="form-label fw-semibold small">Activity Title</label>
                                            <input type="text" name="title" class="form-control" placeholder="e.g. Pouring Liquids Exercise" required>
                                        </div>
                                        <div class="row g-3 mb-3">
                                            <div class="col-md-6">
                                                <label class="form-label fw-semibold small">Activity Type</label>
                                                <select name="type" class="form-select" required>
                                                    <option value="exercise">Practical Exercise</option>
                                                    <option value="quiz">Interactive Quiz</option>
                                                    <option value="game">Gamified Skill Quest</option>
                                                    <option value="skill_node">Skill Node</option>
                                                </select>
                                            </div>
                                            <div class="col-md-6">
                                                <label class="form-label fw-semibold small">XP Points Reward</label>
                                                <input type="number" name="xp_points" class="form-control" value="50" min="5" max="500" required>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="modal-footer border-top p-3 bg-light rounded-bottom-4">
                                        <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Cancel</button>
                                        <button type="submit" class="btn btn-m-primary btn-sm">Add Quest</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                @empty
                    <tr>
                        <td colspan="6" class="text-center py-4 text-muted">
                            No learning paths found. Click <strong>Create Learning Path</strong> to create one!
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-3">
        {{ $learningPaths->links() }}
    </div>
</div>

<!-- Add Path Modal -->
<div class="modal fade" id="addPathModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4">
            <div class="modal-header border-bottom p-4">
                <div>
                    <h5 class="modal-title fw-bold text-dark">
                        <i class="bi bi-plus-circle-fill text-primary me-2"></i> Create Learning Path
                    </h5>
                    <p class="text-muted small mb-0">Define a new Montessori domain learning path.</p>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <form action="{{ route('lms.paths.store') }}" method="POST">
                @csrf
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label for="title" class="form-label fw-semibold small">Learning Path Title</label>
                        <input type="text" name="title" class="form-control" placeholder="e.g. Sensorial Color Tablets & Gradients" required>
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label for="montessori_domain" class="form-label fw-semibold small">Montessori Domain</label>
                            <select name="montessori_domain" class="form-select" required>
                                <option value="Practical Life">Practical Life</option>
                                <option value="Sensorial">Sensorial</option>
                                <option value="Language">Language</option>
                                <option value="Mathematics">Mathematics</option>
                                <option value="Cultural">Cultural</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label for="classroom_id" class="form-label fw-semibold small">Target Classroom</label>
                            <select name="classroom_id" class="form-select">
                                <option value="">-- All Classrooms --</option>
                                @foreach($classrooms as $c)
                                    <option value="{{ $c->id }}">{{ $c->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="description" class="form-label fw-semibold small">Description & Objectives</label>
                        <textarea name="description" class="form-control" rows="3" placeholder="Path goals and materials..."></textarea>
                    </div>
                </div>

                <div class="modal-footer border-top p-3 bg-light rounded-bottom-4">
                    <button type="button" class="btn btn-secondary btn-sm px-3" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-m-primary btn-sm px-4">
                        <i class="bi bi-check-circle me-1"></i> Save Learning Path
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
