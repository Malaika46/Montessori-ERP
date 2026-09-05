@extends('layouts.app')

@section('title', 'System Audit Logs')

@section('content')
<!-- Hero Card -->
<div class="hero-welcome-card mb-4">
    <div class="d-flex flex-column flex-lg-row align-items-lg-center justify-content-between gap-3">
        <div>
            <div class="hero-role-pill">
                <i class="bi bi-shield-check"></i> System Security & Governance
            </div>
            <h1 class="hero-welcome-title fs-2 mb-1">System Audit Logs</h1>
            <p class="hero-welcome-subtitle">
                Superadmin audit trail recording every administrative creation, modification, status update, and archive event.
            </p>
        </div>
    </div>
</div>

<!-- Stat Row -->
<div class="row g-3 mb-4">
    <div class="col-12 col-sm-6 col-xl-6">
        <div class="stat-box-card">
            <div class="stat-box-header">
                <span>Total Recorded Events</span>
                <i class="bi bi-journal-text stat-box-icon text-primary"></i>
            </div>
            <div class="stat-box-number">{{ $totalLogs }}</div>
            <div class="stat-box-sub">Full administrative audit trail</div>
        </div>
    </div>
</div>

<!-- Main Table Card -->
<div class="m-card">
    <div class="d-flex align-items-center justify-content-between mb-4 flex-wrap gap-3">
        <div>
            <h4 class="fw-bold mb-1 text-dark">Audit Event Log</h4>
            <p class="text-muted small mb-0">Real-time action recording</p>
        </div>

        <form action="{{ route('audit-logs.index') }}" method="GET" class="d-flex gap-2">
            <select name="module" class="form-select form-select-sm" style="width: 160px;" onchange="this.form.submit()">
                <option value="">All Modules</option>
                <option value="students" {{ request('module') == 'students' ? 'selected' : '' }}>Students</option>
                <option value="teachers" {{ request('module') == 'teachers' ? 'selected' : '' }}>Teachers</option>
                <option value="parents" {{ request('module') == 'parents' ? 'selected' : '' }}>Parents</option>
                <option value="classrooms" {{ request('module') == 'classrooms' ? 'selected' : '' }}>Classrooms</option>
                <option value="communication" {{ request('module') == 'communication' ? 'selected' : '' }}>Communication</option>
                <option value="lms" {{ request('module') == 'lms' ? 'selected' : '' }}>LMS</option>
            </select>
        </form>
    </div>

    <div class="table-responsive">
        <table class="table table-hover align-middle">
            <thead class="table-light">
                <tr>
                    <th scope="col">Timestamp</th>
                    <th scope="col">User (Actor)</th>
                    <th scope="col">Action</th>
                    <th scope="col">Module</th>
                    <th scope="col">IP Address</th>
                    <th scope="col">Details JSON</th>
                </tr>
            </thead>
            <tbody>
                @forelse($logs as $log)
                    <tr>
                        <td>
                            <span class="small text-muted font-monospace">{{ $log->created_at->format('Y-m-d H:i:s') }}</span>
                        </td>
                        <td>
                            <div class="fw-semibold text-dark small">{{ $log->user->name ?? 'System' }}</div>
                            <div class="small text-muted">{{ $log->user->email ?? '-' }}</div>
                        </td>
                        <td>
                            <span class="badge bg-primary-subtle text-primary border border-primary-subtle font-monospace">
                                {{ $log->action }}
                            </span>
                        </td>
                        <td>
                            <span class="badge bg-light text-dark border">
                                {{ strtoupper($log->module) }}
                            </span>
                        </td>
                        <td>
                            <span class="small text-muted font-monospace">{{ $log->ip_address ?? '127.0.0.1' }}</span>
                        </td>
                        <td>
                            @if(!empty($log->details_json))
                                <pre class="mb-0 small bg-light p-2 rounded border" style="max-width: 300px; max-height: 80px; overflow-y: auto; font-size: 0.75rem;">{{ json_encode($log->details_json, JSON_PRETTY_PRINT) }}</pre>
                            @else
                                <span class="small text-muted">-</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center py-4 text-muted">
                            No audit logs recorded yet. Administrative actions will automatically log here.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-3">
        {{ $logs->links() }}
    </div>
</div>
@endsection
