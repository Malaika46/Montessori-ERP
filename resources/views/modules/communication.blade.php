@extends('layouts.app')

@section('title', 'School Communication')

@section('content')
<!-- Hero Card -->
<div class="hero-welcome-card mb-4">
    <div class="d-flex flex-column flex-lg-row align-items-lg-center justify-content-between gap-3">
        <div>
            <div class="hero-role-pill">
                <i class="bi bi-chat-dots-fill"></i> Campus Communication & Announcements
            </div>
            <h1 class="hero-welcome-title fs-2 mb-1">School Communication Hub</h1>
            <p class="hero-welcome-subtitle">
                Superadmin monitoring overview for all teacher-created broadcasts, classroom messaging, and campus notifications.
            </p>
        </div>

        <div>
            <button type="button" class="btn-pill-gold" data-bs-toggle="modal" data-bs-target="#addCommunicationModal">
                <i class="bi bi-megaphone-fill me-1"></i> New Announcement
            </button>
        </div>
    </div>
</div>

<!-- Stat Row -->
<div class="row g-3 mb-4">
    <div class="col-12 col-sm-6 col-xl-6">
        <div class="stat-box-card">
            <div class="stat-box-header">
                <span>Total Communications Sent</span>
                <i class="bi bi-send-check-fill stat-box-icon text-info"></i>
            </div>
            <div class="stat-box-number text-info">{{ $totalMessages }}</div>
            <div class="stat-box-sub">Organization-wide broadcast log</div>
        </div>
    </div>
</div>

<!-- Main Table Card -->
<div class="m-card">
    <div class="d-flex align-items-center justify-content-between mb-4 flex-wrap gap-3">
        <div>
            <h4 class="fw-bold mb-1 text-dark">Communication Threads & Logs</h4>
            <p class="text-muted small mb-0">Teacher & Administrative message history</p>
        </div>
    </div>

    <div class="table-responsive">
        <table class="table table-hover align-middle">
            <thead class="table-light">
                <tr>
                    <th scope="col">Subject</th>
                    <th scope="col">Sender (Author)</th>
                    <th scope="col">Target Audience</th>
                    <th scope="col">Recipients</th>
                    <th scope="col">Sent Date</th>
                    <th scope="col" class="text-end">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($communications as $comm)
                    <tr>
                        <td>
                            <div class="fw-bold text-dark">{{ $comm->subject }}</div>
                            <div class="small text-muted text-truncate" style="max-width: 250px;">{{ $comm->message }}</div>
                        </td>
                        <td>
                            <span class="badge bg-light text-dark border">
                                <i class="bi bi-person me-1"></i> {{ $comm->sender->name ?? 'System' }} ({{ ucfirst($comm->sender->role->name ?? 'Staff') }})
                            </span>
                        </td>
                        <td>
                            <span class="badge bg-primary-subtle text-primary border border-primary-subtle">
                                {{ strtoupper(str_replace('_', ' ', $comm->audience_type)) }}
                                @if($comm->targetClassroom)
                                    ({{ $comm->targetClassroom->name }})
                                @endif
                            </span>
                        </td>
                        <td>
                            <span class="small text-muted"><strong>{{ $comm->recipients->count() }}</strong> users</span>
                        </td>
                        <td>
                            <span class="small text-muted">{{ $comm->created_at->format('M d, Y g:i A') }}</span>
                        </td>
                        <td class="text-end">
                            <button type="button" class="btn btn-sm btn-outline-primary border-0" data-bs-toggle="modal" data-bs-target="#viewCommModal_{{ $comm->id }}" title="View Communication">
                                <i class="bi bi-eye-fill"></i> Read Message
                            </button>
                        </td>
                    </tr>

                    <!-- View Communication Modal -->
                    <div class="modal fade" id="viewCommModal_{{ $comm->id }}" tabindex="-1" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered modal-lg">
                            <div class="modal-content border-0 shadow-lg rounded-4">
                                <div class="modal-header border-bottom p-4">
                                    <div>
                                        <h5 class="modal-title fw-bold text-dark">{{ $comm->subject }}</h5>
                                        <span class="small text-muted">Sent by {{ $comm->sender->name }} on {{ $comm->created_at->format('F d, Y \a\t g:i A') }}</span>
                                    </div>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                </div>
                                <div class="modal-body p-4">
                                    <div class="mb-3">
                                        <span class="badge bg-primary-subtle text-primary border border-primary-subtle me-2">
                                            Audience: {{ strtoupper(str_replace('_', ' ', $comm->audience_type)) }}
                                        </span>
                                        <span class="badge bg-light text-muted border">
                                            Total Dispatched: {{ $comm->recipients->count() }} Users
                                        </span>
                                    </div>
                                    <div class="p-3 bg-light rounded-3 border">
                                        <p class="mb-0 text-dark" style="white-space: pre-wrap;">{{ $comm->message }}</p>
                                    </div>
                                </div>
                                <div class="modal-footer border-top p-3 bg-light rounded-bottom-4">
                                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Close</button>
                                </div>
                            </div>
                        </div>
                    </div>
                @empty
                    <tr>
                        <td colspan="6" class="text-center py-4 text-muted">
                            No communications recorded. Click <strong>New Announcement</strong> to send a broadcast!
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-3">
        {{ $communications->links() }}
    </div>
</div>

<!-- Add Communication Modal -->
<div class="modal fade" id="addCommunicationModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 shadow-lg rounded-4">
            <div class="modal-header border-bottom p-4">
                <div>
                    <h5 class="modal-title fw-bold text-dark">
                        <i class="bi bi-megaphone-fill text-primary me-2"></i> Create Broadcast Announcement
                    </h5>
                    <p class="text-muted small mb-0">Superadmin organization-wide or classroom targeted message.</p>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <form action="{{ route('communication.store') }}" method="POST">
                @csrf
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label for="subject" class="form-label fw-semibold small">Subject / Title</label>
                        <input type="text" name="subject" class="form-control" placeholder="e.g. Campus Parent-Teacher Orientation Announcement" required>
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label for="audience_type" class="form-label fw-semibold small">Target Audience</label>
                            <select name="audience_type" class="form-select" required>
                                <option value="all_parents">All Parents & Guardians</option>
                                <option value="all_students">All Students</option>
                                <option value="specific_classroom">Specific Classroom</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label for="target_classroom_id" class="form-label fw-semibold small">Target Classroom (If Applicable)</label>
                            <select name="target_classroom_id" class="form-select">
                                <option value="">-- All / Not Applicable --</option>
                                @foreach($classrooms as $c)
                                    <option value="{{ $c->id }}">{{ $c->name }} ({{ $c->age_group }})</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="message" class="form-label fw-semibold small">Message Content</label>
                        <textarea name="message" class="form-control" rows="5" placeholder="Write your announcement details..." required></textarea>
                    </div>
                </div>

                <div class="modal-footer border-top p-3 bg-light rounded-bottom-4">
                    <button type="button" class="btn btn-secondary btn-sm px-3" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-m-primary btn-sm px-4">
                        <i class="bi bi-send-fill me-1"></i> Send Announcement
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
