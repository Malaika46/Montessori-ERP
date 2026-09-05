@extends('layouts.app')

@section('title', 'Parent Directory & Family Linkage')

@section('content')
<!-- Hero Card -->
<div class="hero-welcome-card mb-4">
    <div class="d-flex flex-column flex-lg-row align-items-lg-center justify-content-between gap-3">
        <div>
            <div class="hero-role-pill">
                <i class="bi bi-person-heart"></i> Parent Portal Directory
            </div>
            <h1 class="hero-welcome-title fs-2 mb-1">Parent & Guardian Management</h1>
            <p class="hero-welcome-subtitle">
                Superadmin parent directory to register family accounts, link students, and inspect email verification status.
            </p>
        </div>

        <div>
            <button type="button" class="btn-pill-gold" data-bs-toggle="modal" data-bs-target="#addParentModal">
                <i class="bi bi-person-plus-fill me-1"></i> Register New Parent
            </button>
        </div>
    </div>
</div>

<!-- Stat Row -->
<div class="row g-3 mb-4">
    <div class="col-12 col-sm-6 col-xl-6">
        <div class="stat-box-card">
            <div class="stat-box-header">
                <span>Total Parent Accounts</span>
                <i class="bi bi-people stat-box-icon text-warning"></i>
            </div>
            <div class="stat-box-number text-warning">{{ $totalParents }}</div>
            <div class="stat-box-sub">Registered parents & guardians</div>
        </div>
    </div>
</div>

<!-- Main Table Card -->
<div class="m-card">
    <div class="d-flex align-items-center justify-content-between mb-4 flex-wrap gap-3">
        <div>
            <h4 class="fw-bold mb-1 text-dark">Parent Directory</h4>
            <p class="text-muted small mb-0">Linked children and email verification status</p>
        </div>

        <form action="{{ route('parents.index') }}" method="GET" class="d-flex gap-2">
            <input type="text" name="search" class="form-control form-control-sm" placeholder="Search parent..." value="{{ request('search') }}" style="width: 200px;">
            <button type="submit" class="btn btn-sm btn-m-primary"><i class="bi bi-search"></i></button>
        </form>
    </div>

    <div class="table-responsive">
        <table class="table table-hover align-middle">
            <thead class="table-light">
                <tr>
                    <th scope="col">Parent Name</th>
                    <th scope="col">Email & Phone</th>
                    <th scope="col">Verification Status</th>
                    <th scope="col">Linked Children</th>
                    <th scope="col" class="text-end">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($parents as $parent)
                    <tr>
                        <td>
                            <div class="d-flex align-items-center gap-2">
                                <div class="avatar-initials" style="width: 34px; height: 34px; font-size: 0.85rem; background: var(--m-gold); color: #111;">
                                    {{ strtoupper(substr($parent->user->first_name ?? 'P', 0, 1) . substr($parent->user->last_name ?? '', 0, 1)) }}
                                </div>
                                <div>
                                    <div class="fw-bold text-dark">{{ $parent->user->name }}</div>
                                    <div class="small text-muted">{{ $parent->occupation ?? 'Parent' }}</div>
                                </div>
                            </div>
                        </td>
                        <td>
                            <div class="small text-dark fw-semibold">{{ $parent->user->email }}</div>
                            <div class="small text-muted">{{ $parent->phone ?? 'No Phone' }}</div>
                        </td>
                        <td>
                            @if($parent->user->hasVerifiedEmail())
                                <span class="badge bg-success text-white">Active (Verified)</span>
                            @else
                                <span class="badge bg-warning text-dark">Unverified (Code Sent)</span>
                            @endif
                        </td>
                        <td>
                            @if($parent->children->count() > 0)
                                @foreach($parent->children as $child)
                                    <span class="badge bg-info-subtle text-dark border border-info-subtle me-1">
                                        <i class="bi bi-person me-1"></i> {{ $child->user->name }} ({{ $child->pivot->relationship_type }})
                                    </span>
                                @endforeach
                            @else
                                <span class="badge bg-light text-muted border">No Child Linked</span>
                            @endif
                        </td>
                        <td class="text-end">
                            <div class="d-flex align-items-center justify-content-end gap-1">
                                @if(!$parent->user->hasVerifiedEmail())
                                    <button type="button" class="btn btn-sm btn-outline-primary border-0" data-bs-toggle="modal" data-bs-target="#enterParentCodeModal_{{ $parent->user->id }}" title="Enter 6-digit Verification Code">
                                        <i class="bi bi-key-fill"></i> Enter Code
                                    </button>

                                    <form action="{{ route('users.verify-now', $parent->user->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Manually verify email for parent {{ $parent->user->name }}?');">
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-outline-success border-0" title="Verify Email Now">
                                            <i class="bi bi-check-circle-fill"></i> Verify
                                        </button>
                                    </form>

                                    <form action="{{ route('users.resend-verification', $parent->user->id) }}" method="POST" class="d-inline">
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-outline-warning border-0" title="Resend Verification Code & Link">
                                            <i class="bi bi-send-fill"></i> Resend Code
                                        </button>
                                    </form>

                                    <!-- Modal: Enter Code for Parent -->
                                    <div class="modal fade text-start" id="enterParentCodeModal_{{ $parent->user->id }}" tabindex="-1" aria-hidden="true">
                                        <div class="modal-dialog modal-dialog-centered modal-sm">
                                            <div class="modal-content border-0 shadow-lg rounded-4">
                                                <div class="modal-header border-bottom p-3">
                                                    <h6 class="modal-title fw-bold text-dark mb-0">
                                                        <i class="bi bi-shield-lock-fill text-success me-1"></i> Enter Code
                                                    </h6>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                </div>
                                                <form action="{{ route('verification.code') }}" method="POST">
                                                    @csrf
                                                    <input type="hidden" name="email" value="{{ $parent->user->email }}">
                                                    <div class="modal-body p-3">
                                                        <p class="small text-muted mb-2">Enter 6-digit code for <strong>{{ $parent->user->email }}</strong>:</p>
                                                        <input type="text" name="code" class="form-control text-center font-monospace fw-bold fs-4" placeholder="123456" maxlength="6" required style="letter-spacing: 0.3rem;">
                                                    </div>
                                                    <div class="modal-footer border-top p-2 bg-light rounded-bottom-4">
                                                        <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Cancel</button>
                                                        <button type="submit" class="btn btn-success btn-sm">Verify Parent</button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                @endif

                                <button type="button" class="btn btn-sm btn-outline-primary border-0" data-bs-toggle="modal" data-bs-target="#linkStudentModal_{{ $parent->id }}" title="Link Student">
                                    <i class="bi bi-link-45deg fs-6"></i> Link Student
                                </button>

                                <form action="{{ route('parents.destroy', $parent->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Archive this parent record?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger border-0" title="Archive Parent">
                                        <i class="bi bi-archive-fill"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>

                    <!-- Link Student Modal for each parent -->
                    <div class="modal fade" id="linkStudentModal_{{ $parent->id }}" tabindex="-1" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered">
                            <div class="modal-content border-0 shadow-lg rounded-4">
                                <div class="modal-header border-bottom p-4">
                                    <h5 class="modal-title fw-bold text-dark">
                                        Link Student to {{ $parent->user->name }}
                                    </h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                </div>
                                <form action="{{ route('parents.link-student', $parent->id) }}" method="POST">
                                    @csrf
                                    <div class="modal-body p-4">
                                        <div class="mb-3">
                                            <label class="form-label fw-semibold small">Select Student</label>
                                            <select name="student_id" class="form-select" required>
                                                <option value="" disabled selected>-- Select Student --</option>
                                                @foreach($students as $st)
                                                    <option value="{{ $st->id }}">{{ $st->user->name }} ({{ $st->student_number }})</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label fw-semibold small">Relationship</label>
                                            <select name="relationship_type" class="form-select" required>
                                                <option value="Father">Father</option>
                                                <option value="Mother">Mother</option>
                                                <option value="Guardian">Guardian</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="modal-footer border-top p-3 bg-light rounded-bottom-4">
                                        <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Cancel</button>
                                        <button type="submit" class="btn btn-m-primary btn-sm">Link Student</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                @empty
                    <tr>
                        <td colspan="5" class="text-center py-4 text-muted">
                            No parent records found. Click <strong>Register New Parent</strong> to create your first parent!
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-3">
        {{ $parents->links() }}
    </div>
</div>

<!-- Add Parent Modal -->
<div class="modal fade" id="addParentModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4">
            <div class="modal-header border-bottom p-4">
                <div>
                    <h5 class="modal-title fw-bold text-dark">
                        <i class="bi bi-person-plus-fill text-success me-2"></i> Register Parent (Superadmin)
                    </h5>
                    <p class="text-muted small mb-0">Creates parent user, triggers email verification, and links child.</p>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <form action="{{ route('parents.store') }}" method="POST">
                @csrf
                <div class="modal-body p-4">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="first_name" class="form-label fw-semibold small">First Name</label>
                            <input type="text" name="first_name" class="form-control" placeholder="e.g. Robert" required>
                        </div>
                        <div class="col-md-6">
                            <label for="last_name" class="form-label fw-semibold small">Last Name</label>
                            <input type="text" name="last_name" class="form-control" placeholder="e.g. Oppenheimer" required>
                        </div>
                    </div>

                    <div class="mt-3">
                        <label for="email" class="form-label fw-semibold small">Parent Email Address</label>
                        <input type="email" name="email" class="form-control" placeholder="parent@domain.com" required>
                        <div class="form-text small">Verification code & link will be emailed to parent. Account remains unverified until verified.</div>
                    </div>

                    <div class="row g-3 mt-1">
                        <div class="col-md-6">
                            <label for="phone" class="form-label fw-semibold small">Phone Number</label>
                            <input type="text" name="phone" class="form-control" placeholder="+92 300 1234567">
                        </div>
                        <div class="col-md-6">
                            <label for="occupation" class="form-label fw-semibold small">Occupation</label>
                            <input type="text" name="occupation" class="form-control" placeholder="e.g. Engineer">
                        </div>
                    </div>

                    <div class="mt-3">
                        <label class="form-label fw-semibold small">Link Child / Children</label>
                        <select name="student_ids[]" class="form-select" multiple style="height: 100px;">
                            @foreach($students as $st)
                                <option value="{{ $st->id }}">{{ $st->user->name }} ({{ $st->student_number }})</option>
                            @endforeach
                        </select>
                        <div class="form-text small">Hold Ctrl/Cmd to select multiple students.</div>
                    </div>

                    <div class="mt-3">
                        <label for="password" class="form-label fw-semibold small">Temporary Password</label>
                        <input type="password" name="password" class="form-control" placeholder="Minimum 8 characters" required>
                    </div>
                </div>

                <div class="modal-footer border-top p-3 bg-light rounded-bottom-4">
                    <button type="button" class="btn btn-secondary btn-sm px-3" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-m-primary btn-sm px-4">
                        <i class="bi bi-check-circle me-1"></i> Register Parent & Trigger Verification
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
