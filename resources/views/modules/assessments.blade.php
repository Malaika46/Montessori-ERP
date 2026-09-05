@extends('layouts.app')

@section('title', 'Child Assessment & Evaluation Center')

@section('content')
<!-- Hero Card -->
<div class="hero-welcome-card mb-4">
    <div class="d-flex flex-column flex-lg-row align-items-lg-center justify-content-between gap-3">
        <div>
            <div class="hero-role-pill">
                <i class="bi bi-file-earmark-check-fill"></i> Academic Evaluation
            </div>
            <h1 class="hero-welcome-title fs-2 mb-1">Child Assessment & Evaluation Center</h1>
            <p class="hero-welcome-subtitle">
                Comprehensive developmental rubrics, term narrative reports, and directress evaluation summaries.
            </p>
        </div>

        @php
            $user = Auth::user();
            $roleName = $user && $user->role ? $user->role->name : 'guest';
            $canEdit = in_array($roleName, ['superadmin', 'principal', 'admin', 'teacher']);
        @endphp

        @if($canEdit)
        <div>
            <button type="button" class="btn-pill-gold" data-bs-toggle="modal" data-bs-target="#addAssessmentModal">
                <i class="bi bi-plus-circle-fill me-1"></i> Create Student Assessment
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

<!-- Stats Cards Row -->
<div class="row g-3 mb-4">
    <div class="col-12 col-md-4">
        <div class="card border-0 shadow-sm rounded-3 p-3 border-start border-4 border-success h-100 bg-white">
            <div class="text-uppercase small text-muted fw-bold">Term 1 Evaluation</div>
            <div class="fs-3 fw-bold text-success my-1">Mastered ({{ $avgScore }}%)</div>
            <div class="small text-secondary"><i class="bi bi-check2-circle text-success me-1"></i> High Independence in Practical Life & Sensorial</div>
        </div>
    </div>
    <div class="col-12 col-md-4">
        <div class="card border-0 shadow-sm rounded-3 p-3 border-start border-4 border-primary h-100 bg-white">
            <div class="text-uppercase small text-muted fw-bold">Released Reports</div>
            <div class="fs-3 fw-bold text-primary my-1">{{ $releasedCount }} Official Reports</div>
            <div class="small text-secondary"><i class="bi bi-shield-check text-primary me-1"></i> Signed by Lead Directress & Campus Director</div>
        </div>
    </div>
    <div class="col-12 col-md-4">
        <div class="card border-0 shadow-sm rounded-3 p-3 border-start border-4 border-warning h-100 bg-white">
            <div class="text-uppercase small text-muted fw-bold">Next Evaluation Cycle</div>
            <div class="fs-3 fw-bold text-warning my-1">Term 2 Progress</div>
            <div class="small text-secondary"><i class="bi bi-calendar-event text-warning me-1"></i> Scheduled for Mid-November 2026</div>
        </div>
    </div>
</div>

<!-- Filter Container -->
<div class="m-card mb-4">
    <div class="d-flex align-items-center justify-content-between flex-wrap gap-3">
        <div>
            <h5 class="fw-bold mb-0 text-dark"><i class="bi bi-journal-check text-success me-2"></i> Official Released Report Cards</h5>
            <p class="text-muted small mb-0">Filtered based on student rosters and authorized campus environments</p>
        </div>

        <form action="{{ route('assessments.index') }}" method="GET" class="d-flex gap-2 flex-wrap align-items-center">
            <select name="student_id" class="form-select form-select-sm" style="width: 180px;" onchange="this.form.submit()">
                <option value="">All Students</option>
                @foreach($students as $st)
                    <option value="{{ $st->id }}" {{ request('student_id') == $st->id ? 'selected' : '' }}>
                        {{ $st->first_name }} {{ $st->last_name }}
                    </option>
                @endforeach
            </select>

            <select name="classroom_id" class="form-select form-select-sm" style="width: 160px;" onchange="this.form.submit()">
                <option value="">All Classrooms</option>
                @foreach($classrooms as $c)
                    <option value="{{ $c->id }}" {{ request('classroom_id') == $c->id ? 'selected' : '' }}>{{ $c->name }}</option>
                @endforeach
            </select>

            <select name="status" class="form-select form-select-sm" style="width: 130px;" onchange="this.form.submit()">
                <option value="">All Statuses</option>
                <option value="released" {{ request('status') == 'released' ? 'selected' : '' }}>Released</option>
                <option value="review" {{ request('status') == 'review' ? 'selected' : '' }}>Review</option>
                <option value="draft" {{ request('status') == 'draft' ? 'selected' : '' }}>Draft</option>
            </select>

            <button type="submit" class="btn btn-sm btn-m-primary"><i class="bi bi-search"></i> Filter</button>
            @if(request('student_id') || request('classroom_id') || request('status'))
                <a href="{{ route('assessments.index') }}" class="btn btn-sm btn-outline-secondary">Reset</a>
            @endif
        </form>
    </div>
</div>

<!-- Assessment Cards List -->
<div class="row g-4">
    @forelse($assessments as $item)
        <div class="col-12">
            <div class="card border shadow-sm rounded-4 overflow-hidden bg-white">
                <div class="card-body p-4">
                    <!-- Top Bar: Status & PDF Download -->
                    <div class="d-flex justify-content-between align-items-start flex-wrap gap-3 mb-3">
                        <div>
                            <div class="d-flex align-items-center gap-2 mb-2">
                                <span class="badge bg-success-subtle text-success border border-success-subtle px-2.5 py-1 text-uppercase fw-bold" style="font-size: 0.75rem;">
                                    <i class="bi bi-shield-check me-1"></i> {{ strtoupper($item->status) }} & VERIFIED
                                </span>
                                <span class="badge bg-secondary-subtle text-dark border px-2 py-1 small">
                                    <i class="bi bi-person-fill text-primary me-1"></i> {{ $item->student ? $item->student->first_name . ' ' . $item->student->last_name : 'Student' }}
                                </span>
                                <span class="badge bg-light text-muted border px-2 py-1 small">
                                    <i class="bi bi-door-open me-1"></i> {{ $item->classroom ? $item->classroom->name : 'Environment' }}
                                </span>
                            </div>
                            <h4 class="fw-bold text-dark mb-1">{{ $item->title }}</h4>
                            <div class="text-muted small">
                                Evaluated Period: <strong>{{ $item->evaluation_period }}</strong> • Published on {{ $item->published_at ? $item->published_at->format('M d, Y') : date('M d, Y') }}
                            </div>
                        </div>

                        <div class="d-flex align-items-center gap-2">
                            <a href="{{ route('assessments.pdf', $item->id) }}" target="_blank" class="btn btn-m-primary px-3 py-2 fw-semibold shadow-sm">
                                <i class="bi bi-download me-1"></i> Download PDF Report
                            </a>

                            @if($canEdit)
                            <button type="button" class="btn btn-sm btn-outline-primary border-0 fs-5" data-bs-toggle="modal" data-bs-target="#editAssessmentModal_{{ $item->id }}" title="Edit">
                                <i class="bi bi-pencil-square"></i>
                            </button>
                            <form action="{{ route('assessments.destroy', $item->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete assessment for {{ $item->student ? $item->student->first_name : 'student' }}?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-outline-danger border-0 fs-5" title="Delete">
                                    <i class="bi bi-trash-fill"></i>
                                </button>
                            </form>
                            @endif
                        </div>
                    </div>

                    <!-- 4 Column Avenues Row -->
                    <div class="row g-2 my-3 text-center">
                        <div class="col-6 col-md-3">
                            <div class="p-3 bg-light rounded-3 border">
                                <div class="small text-muted fw-semibold">Practical Life</div>
                                <div class="badge bg-success text-white px-3 py-1 mt-1 font-monospace" style="font-size: 0.85rem;">
                                    {{ $item->practical_life_status }} ({{ $item->practical_life_score }}%)
                                </div>
                            </div>
                        </div>
                        <div class="col-6 col-md-3">
                            <div class="p-3 bg-light rounded-3 border">
                                <div class="small text-muted fw-semibold">Sensorial Area</div>
                                <div class="badge bg-success text-white px-3 py-1 mt-1 font-monospace" style="font-size: 0.85rem;">
                                    {{ $item->sensorial_status }} ({{ $item->sensorial_score }}%)
                                </div>
                            </div>
                        </div>
                        <div class="col-6 col-md-3">
                            <div class="p-3 bg-light rounded-3 border">
                                <div class="small text-muted fw-semibold">Mathematics</div>
                                <div class="badge bg-warning text-dark px-3 py-1 mt-1 font-monospace" style="font-size: 0.85rem;">
                                    {{ $item->mathematics_status }} ({{ $item->mathematics_score }}%)
                                </div>
                            </div>
                        </div>
                        <div class="col-6 col-md-3">
                            <div class="p-3 bg-light rounded-3 border">
                                <div class="small text-muted fw-semibold">Language & Phonics</div>
                                <div class="badge bg-info text-white px-3 py-1 mt-1 font-monospace" style="font-size: 0.85rem;">
                                    {{ $item->language_status }} ({{ $item->language_score }}%)
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Directress Summary Box -->
                    <div class="p-3 bg-light rounded-3 border-start border-4 border-success mt-3">
                        <div class="small fw-bold text-dark mb-1">
                            <i class="bi bi-chat-quote-fill text-success me-1"></i> Directress Overall Summary:
                        </div>
                        <p class="text-secondary small mb-0 font-italic">
                            "{{ $item->overall_summary }}"
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Edit Modal for Assessment -->
        @if($canEdit)
        <div class="modal fade" id="editAssessmentModal_{{ $item->id }}" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-lg">
                <div class="modal-content border-0 shadow">
                    <div class="modal-header bg-dark text-white">
                        <h5 class="modal-title fw-bold"><i class="bi bi-pencil-square me-2"></i> Edit Student Assessment</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <form action="{{ route('assessments.update', $item->id) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="modal-body p-4">
                            <div class="row g-3">
                                <div class="col-md-8">
                                    <label class="form-label fw-semibold">Report Title</label>
                                    <input type="text" name="title" class="form-control" value="{{ $item->title }}" required>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label fw-semibold">Term Name</label>
                                    <input type="text" name="term_name" class="form-control" value="{{ $item->term_name }}" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">Evaluation Period</label>
                                    <input type="text" name="evaluation_period" class="form-control" value="{{ $item->evaluation_period }}" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">Publication Status</label>
                                    <select name="status" class="form-select" required>
                                        <option value="draft" {{ $item->status == 'draft' ? 'selected' : '' }}>Draft</option>
                                        <option value="review" {{ $item->status == 'review' ? 'selected' : '' }}>Under Review</option>
                                        <option value="released" {{ $item->status == 'released' ? 'selected' : '' }}>Released & Verified</option>
                                    </select>
                                </div>

                                <div class="col-12"><hr class="my-2"></div>
                                <h6 class="fw-bold text-dark mb-0">Montessori Avenues Rubric Evaluation</h6>

                                <div class="col-md-6">
                                    <label class="form-label small fw-semibold">Practical Life Score (0-100)</label>
                                    <input type="number" name="practical_life_score" class="form-control" value="{{ $item->practical_life_score }}" min="0" max="100" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label small fw-semibold">Practical Life Mastery Status</label>
                                    <select name="practical_life_status" class="form-select" required>
                                        <option value="Introduced" {{ $item->practical_life_status == 'Introduced' ? 'selected' : '' }}>Introduced</option>
                                        <option value="Working" {{ $item->practical_life_status == 'Working' ? 'selected' : '' }}>Working</option>
                                        <option value="Mastered" {{ $item->practical_life_status == 'Mastered' ? 'selected' : '' }}>Mastered</option>
                                    </select>
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label small fw-semibold">Sensorial Score (0-100)</label>
                                    <input type="number" name="sensorial_score" class="form-control" value="{{ $item->sensorial_score }}" min="0" max="100" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label small fw-semibold">Sensorial Mastery Status</label>
                                    <select name="sensorial_status" class="form-select" required>
                                        <option value="Introduced" {{ $item->sensorial_status == 'Introduced' ? 'selected' : '' }}>Introduced</option>
                                        <option value="Working" {{ $item->sensorial_status == 'Working' ? 'selected' : '' }}>Working</option>
                                        <option value="Mastered" {{ $item->sensorial_status == 'Mastered' ? 'selected' : '' }}>Mastered</option>
                                    </select>
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label small fw-semibold">Mathematics Score (0-100)</label>
                                    <input type="number" name="mathematics_score" class="form-control" value="{{ $item->mathematics_score }}" min="0" max="100" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label small fw-semibold">Mathematics Mastery Status</label>
                                    <select name="mathematics_status" class="form-select" required>
                                        <option value="Introduced" {{ $item->mathematics_status == 'Introduced' ? 'selected' : '' }}>Introduced</option>
                                        <option value="Working" {{ $item->mathematics_status == 'Working' ? 'selected' : '' }}>Working</option>
                                        <option value="Mastered" {{ $item->mathematics_status == 'Mastered' ? 'selected' : '' }}>Mastered</option>
                                    </select>
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label small fw-semibold">Language Score (0-100)</label>
                                    <input type="number" name="language_score" class="form-control" value="{{ $item->language_score }}" min="0" max="100" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label small fw-semibold">Language Mastery Status</label>
                                    <select name="language_status" class="form-select" required>
                                        <option value="Introduced" {{ $item->language_status == 'Introduced' ? 'selected' : '' }}>Introduced</option>
                                        <option value="Working" {{ $item->language_status == 'Working' ? 'selected' : '' }}>Working</option>
                                        <option value="Mastered" {{ $item->language_status == 'Mastered' ? 'selected' : '' }}>Mastered</option>
                                    </select>
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label small fw-semibold">Cultural Score (0-100)</label>
                                    <input type="number" name="cultural_score" class="form-control" value="{{ $item->cultural_score }}" min="0" max="100" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label small fw-semibold">Cultural Mastery Status</label>
                                    <select name="cultural_status" class="form-select" required>
                                        <option value="Introduced" {{ $item->cultural_status == 'Introduced' ? 'selected' : '' }}>Introduced</option>
                                        <option value="Working" {{ $item->cultural_status == 'Working' ? 'selected' : '' }}>Working</option>
                                        <option value="Mastered" {{ $item->cultural_status == 'Mastered' ? 'selected' : '' }}>Mastered</option>
                                    </select>
                                </div>

                                <div class="col-12">
                                    <label class="form-label fw-semibold">Directress Overall Narrative Summary</label>
                                    <textarea name="overall_summary" class="form-control" rows="3" required>{{ $item->overall_summary }}</textarea>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer bg-light">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-m-primary px-4">Update Report Card</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        @endif

    @empty
        <div class="col-12 text-center py-5">
            <i class="bi bi-journal-x fs-1 text-muted d-block mb-2"></i>
            <h5 class="fw-bold text-dark">No Assessment Reports Found</h5>
            <p class="text-muted small">No assessment report cards recorded for the selected criteria.</p>
            @if($canEdit)
            <button type="button" class="btn btn-sm btn-m-primary mt-2" data-bs-toggle="modal" data-bs-target="#addAssessmentModal">
                <i class="bi bi-plus-circle me-1"></i> Conduct First Assessment
            </button>
            @endif
        </div>
    @endforelse
</div>

<!-- Pagination -->
<div class="mt-4">
    {{ $assessments->links() }}
</div>

<!-- Modal: Create Student Assessment -->
@if($canEdit)
<div class="modal fade" id="addAssessmentModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-dark text-white">
                <h5 class="modal-title fw-bold"><i class="bi bi-plus-circle me-2"></i> Create Student Assessment Report</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('assessments.store') }}" method="POST">
                @csrf
                <div class="modal-body p-4">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Select Student <span class="text-danger">*</span></label>
                            <select name="student_id" class="form-select" required>
                                <option value="">-- Choose Student --</option>
                                @foreach($students as $st)
                                    <option value="{{ $st->id }}">{{ $st->first_name }} {{ $st->last_name }} ({{ $st->classroom ? $st->classroom->name : 'N/A' }})</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Report Title <span class="text-danger">*</span></label>
                            <input type="text" name="title" class="form-control" value="2026 First Term Montessori Narrative Progress Card" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Term Name <span class="text-danger">*</span></label>
                            <input type="text" name="term_name" class="form-control" value="Term 1" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Evaluation Period <span class="text-danger">*</span></label>
                            <input type="text" name="evaluation_period" class="form-control" value="Jan 2026 - Jun 2026" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Publication Status</label>
                            <select name="status" class="form-select" required>
                                <option value="released">Released & Verified</option>
                                <option value="review">Under Review</option>
                                <option value="draft">Draft</option>
                            </select>
                        </div>

                        <div class="col-12"><hr class="my-2"></div>
                        <h6 class="fw-bold text-dark mb-0">Montessori Avenues Rubric Evaluation</h6>

                        <div class="col-md-6">
                            <label class="form-label small fw-semibold">Practical Life Score (0-100)</label>
                            <input type="number" name="practical_life_score" class="form-control" value="95" min="0" max="100" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold">Practical Life Mastery Status</label>
                            <select name="practical_life_status" class="form-select" required>
                                <option value="Mastered">Mastered</option>
                                <option value="Working">Working</option>
                                <option value="Introduced">Introduced</option>
                            </select>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label small fw-semibold">Sensorial Score (0-100)</label>
                            <input type="number" name="sensorial_score" class="form-control" value="90" min="0" max="100" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold">Sensorial Mastery Status</label>
                            <select name="sensorial_status" class="form-select" required>
                                <option value="Mastered">Mastered</option>
                                <option value="Working">Working</option>
                                <option value="Introduced">Introduced</option>
                            </select>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label small fw-semibold">Mathematics Score (0-100)</label>
                            <input type="number" name="mathematics_score" class="form-control" value="82" min="0" max="100" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold">Mathematics Mastery Status</label>
                            <select name="mathematics_status" class="form-select" required>
                                <option value="Working">Working</option>
                                <option value="Mastered">Mastered</option>
                                <option value="Introduced">Introduced</option>
                            </select>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label small fw-semibold">Language Score (0-100)</label>
                            <input type="number" name="language_score" class="form-control" value="85" min="0" max="100" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold">Language Mastery Status</label>
                            <select name="language_status" class="form-select" required>
                                <option value="Introduced">Introduced</option>
                                <option value="Working">Working</option>
                                <option value="Mastered">Mastered</option>
                            </select>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label small fw-semibold">Cultural Score (0-100)</label>
                            <input type="number" name="cultural_score" class="form-control" value="88" min="0" max="100" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold">Cultural Mastery Status</label>
                            <select name="cultural_status" class="form-select" required>
                                <option value="Mastered">Mastered</option>
                                <option value="Working">Working</option>
                                <option value="Introduced">Introduced</option>
                            </select>
                        </div>

                        <div class="col-12">
                            <label class="form-label fw-semibold">Directress Overall Narrative Summary <span class="text-danger">*</span></label>
                            <textarea name="overall_summary" class="form-control" rows="3" placeholder="Enter detailed directress remarks regarding student concentration, independence, and social integration..." required></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn-pill-gold px-4">Publish Assessment Report</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif
@endsection
