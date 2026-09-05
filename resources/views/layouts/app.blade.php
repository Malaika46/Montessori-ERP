<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Dashboard') - {{ config('app.name', 'Montessori ERP') }}</title>
    
    <!-- Bootstrap 5 CSS & Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
    
    <!-- Montessori Custom Dark Sidebar & SaaS Theme -->
    <link href="{{ asset('css/montessori-theme.css') }}" rel="stylesheet">
    @stack('styles')
</head>
<body>
    <div class="app-wrapper">
        <!-- Sidebar Dark Navigation (Matching Screenshot) -->
        <aside class="app-sidebar" id="appSidebar">
            <div class="app-sidebar-header">
                <div class="app-brand-logo bg-dark border border-warning shadow-sm">
                    <i class="bi bi-mortarboard-fill text-warning fs-5"></i>
                </div>
                <div>
                    <span class="app-brand-text">Montessori ERP</span>
                    <span class="app-brand-tag">Management Portal</span>
                </div>
            </div>

            <div class="app-sidebar-body">
                @php
                    $user = Auth::user();
                    $roleName = $user && $user->role ? $user->role->name : 'superadmin';
                    $roleDisplay = $user && $user->role ? strtoupper($user->role->display_name) : 'SUPERADMIN';
                @endphp

                <!-- MAIN -->
                <div class="nav-section-title">MAIN</div>
                <a href="{{ route('dashboard') }}" class="sidebar-nav-item {{ request()->routeIs('dashboard*') ? 'active' : '' }}">
                    <span><i class="bi bi-grid me-2"></i> Dashboard</span>
                </a>

                @if(in_array($roleName, ['superadmin', 'principal', 'admin', 'teacher']))
                <!-- ACADEMIC & MONTESSORI -->
                <div class="nav-section-title">ACADEMIC & MONTESSORI</div>

                <a href="{{ route('students.index') }}" class="sidebar-nav-item {{ request()->routeIs('students*') ? 'active' : '' }}">
                    <span><i class="bi bi-mortarboard me-2"></i> Students</span>
                </a>
                @if(in_array($roleName, ['superadmin', 'principal', 'admin']))
                <a href="{{ route('teachers.index') }}" class="sidebar-nav-item {{ request()->routeIs('teachers*') ? 'active' : '' }}">
                    <span><i class="bi bi-person-badge me-2"></i> Teachers & Guides</span>
                </a>
                @endif
                <a href="{{ route('parents.index') }}" class="sidebar-nav-item {{ request()->routeIs('parents*') ? 'active' : '' }}">
                    <span><i class="bi bi-heart me-2"></i> Parents</span>
                </a>
                <a href="{{ route('classrooms.index') }}" class="sidebar-nav-item {{ request()->routeIs('classrooms*') ? 'active' : '' }}">
                    <span><i class="bi bi-building me-2"></i> Classrooms</span>
                </a>
                <a href="{{ route('curriculum.index') }}" class="sidebar-nav-item {{ request()->routeIs('curriculum*') ? 'active' : '' }}">
                    <span><i class="bi bi-journal-text me-2"></i> Curriculum</span>
                </a>
                <a href="{{ route('lessons.index') }}" class="sidebar-nav-item {{ request()->routeIs('lessons*') ? 'active' : '' }}">
                    <span><i class="bi bi-calendar-check me-2"></i> Lesson Planning</span>
                </a>
                <a href="{{ route('observations.index') }}" class="sidebar-nav-item {{ request()->routeIs('observations*') ? 'active' : '' }}">
                    <span><i class="bi bi-eye me-2"></i> Observations</span>
                </a>
                <a href="{{ route('lms.index') }}" class="sidebar-nav-item {{ request()->routeIs('lms*') ? 'active' : '' }}">
                    <span><i class="bi bi-controller me-2"></i> Gamified LMS</span>
                </a>
                <a href="{{ route('assessments.index') }}" class="sidebar-nav-item {{ request()->routeIs('assessments*') ? 'active' : '' }}">
                    <span><i class="bi bi-file-earmark-bar-graph me-2"></i> Assessments & Reports</span>
                </a>

                <!-- OPERATIONS -->
                <div class="nav-section-title">OPERATIONS</div>
                <a href="{{ route('attendance.index') }}" class="sidebar-nav-item {{ request()->routeIs('attendance*') ? 'active' : '' }}">
                    <span><i class="bi bi-clock me-2"></i> Attendance & Logs</span>
                </a>
                <a href="{{ route('communication.index') }}" class="sidebar-nav-item {{ request()->routeIs('communication*') ? 'active' : '' }}">
                    <span><i class="bi bi-chat-dots me-2"></i> Communication</span>
                </a>
                <a href="{{ route('inventory.index') }}" class="sidebar-nav-item {{ request()->routeIs('inventory*') ? 'active' : '' }}">
                    <span><i class="bi bi-box-seam me-2"></i> Material Inventory</span>
                </a>
                @endif

                @if(in_array($roleName, ['student']))
                <div class="nav-section-title">MY LEARNING & ACADEMICS</div>
                <a href="{{ route('curriculum.index') }}" class="sidebar-nav-item {{ request()->routeIs('curriculum*') ? 'active' : '' }}">
                    <span><i class="bi bi-journal-bookmark-fill me-2 text-info"></i> Curriculum Map</span>
                </a>
                <a href="{{ route('lessons.index') }}" class="sidebar-nav-item {{ request()->routeIs('lessons*') ? 'active' : '' }}">
                    <span><i class="bi bi-calendar-event-fill me-2 text-primary"></i> Lesson Planning</span>
                </a>
                <a href="{{ route('lms.index') }}" class="sidebar-nav-item {{ request()->routeIs('lms*') ? 'active' : '' }}">
                    <span><i class="bi bi-controller me-2 text-warning"></i> Gamified LMS</span>
                </a>
                <a href="{{ route('assessments.index') }}" class="sidebar-nav-item {{ request()->routeIs('assessments*') ? 'active' : '' }}">
                    <span><i class="bi bi-file-earmark-check-fill me-2 text-success"></i> Reports & PDF</span>
                </a>
                @endif

                @if(in_array($roleName, ['parent']))
                <div class="nav-section-title">ACADEMIC & MONTESSORI</div>
                <a href="{{ route('students.index') }}" class="sidebar-nav-item {{ request()->routeIs('students*') ? 'active' : '' }}">
                    <span><i class="bi bi-mortarboard me-2"></i> Students</span>
                </a>
                <a href="{{ route('observations.index') }}" class="sidebar-nav-item {{ request()->routeIs('observations*') ? 'active' : '' }}">
                    <span><i class="bi bi-eye me-2"></i> Observations</span>
                </a>
                <a href="{{ route('assessments.index') }}" class="sidebar-nav-item {{ request()->routeIs('assessments*') ? 'active' : '' }}">
                    <span><i class="bi bi-file-earmark-text me-2"></i> Assessments & Reports</span>
                </a>

                <div class="nav-section-title">OPERATIONS</div>
                <a href="{{ route('attendance.index') }}" class="sidebar-nav-item {{ request()->routeIs('attendance*') ? 'active' : '' }}">
                    <span><i class="bi bi-clock me-2"></i> Check-In & Pickups</span>
                </a>
                <a href="{{ route('communication.index') }}" class="sidebar-nav-item {{ request()->routeIs('communication*') ? 'active' : '' }}">
                    <span><i class="bi bi-chat-dots me-2"></i> Communication</span>
                </a>

                <div class="nav-section-title">FINANCE & HR</div>
                <a href="{{ route('fees.index') }}" class="sidebar-nav-item {{ request()->routeIs('fees*') ? 'active' : '' }}">
                    <span><i class="bi bi-receipt me-2"></i> My Invoices & Fees</span>
                </a>
                @endif

                @if(in_array($roleName, ['superadmin', 'principal', 'admin']))
                <div class="nav-section-title">OPERATIONS & FINANCE</div>
                <a href="{{ route('attendance.index') }}" class="sidebar-nav-item {{ request()->routeIs('attendance*') ? 'active' : '' }}">
                    <span><i class="bi bi-clock me-2"></i> Attendance</span>
                </a>
                <a href="{{ route('inventory.index') }}" class="sidebar-nav-item {{ request()->routeIs('inventory*') ? 'active' : '' }}">
                    <span><i class="bi bi-box-seam me-2"></i> Material Inventory</span>
                </a>
                <a href="{{ route('fees.index') }}" class="sidebar-nav-item {{ request()->routeIs('fees*') ? 'active' : '' }}">
                    <span><i class="bi bi-receipt me-2"></i> Student Fees</span>
                </a>
                <a href="{{ route('communication.index') }}" class="sidebar-nav-item {{ request()->routeIs('communication*') ? 'active' : '' }}">
                    <span><i class="bi bi-chat-dots me-2"></i> Communication</span>
                </a>
                <a href="{{ route('users.index') }}" class="sidebar-nav-item {{ request()->routeIs('users*') ? 'active' : '' }}">
                    <span><i class="bi bi-people me-2"></i> Users & Staff</span>
                </a>
                @endif

                @if(in_array($roleName, ['superadmin', 'principal']))
                <div class="nav-section-title">SYSTEM</div>
                <a href="{{ route('campuses.index') }}" class="sidebar-nav-item {{ request()->routeIs('campuses*') ? 'active' : '' }}">
                    <span><i class="bi bi-buildings me-2"></i> Campuses</span>
                </a>
                <a href="{{ route('logs.index') }}" class="sidebar-nav-item {{ request()->routeIs('logs*') ? 'active' : '' }}">
                    <span><i class="bi bi-shield-check me-2"></i> Audit Logs</span>
                </a>
                <a href="{{ route('settings.index') }}" class="sidebar-nav-item {{ request()->routeIs('settings*') ? 'active' : '' }}">
                    <span><i class="bi bi-gear me-2"></i> Settings</span>
                </a>
                @endif
            </div>

            <!-- Sidebar Bottom Box (Matching Screenshot) -->
            <div class="sidebar-footer-box">
                <div class="sidebar-footer-title">Montessori ERP System</div>
                <div class="sidebar-footer-sub">• Role Isolation Active: {{ $roleDisplay }}</div>
            </div>
        </aside>
        
        <div class="sidebar-overlay" id="sidebarOverlay"></div>

        <!-- Main Content Area -->
        <div class="app-main">
            <!-- Header (Matching Screenshot) -->
            <header class="app-header">
                <div class="d-flex align-items-center gap-3">
                    <button class="mobile-toggle-btn d-lg-none border-0 bg-transparent" id="mobileSidebarToggle" type="button">
                        <i class="bi bi-list fs-3"></i>
                    </button>
                    
                    <!-- Environment Pill Filter -->
                    <div class="filter-pill-select d-none d-sm-inline-flex">
                        <i class="bi bi-building"></i>
                        <span>All Classrooms</span>
                        <i class="bi bi-chevron-down ms-1 text-muted small"></i>
                    </div>
                </div>

                <!-- Center Pill Search -->
                <div class="header-search-pill d-none d-md-block">
                    <i class="bi bi-search"></i>
                    <input type="text" placeholder="Search records, students, works, or modules...">
                </div>

                <!-- Right Action Bar -->
                <div class="d-flex align-items-center gap-3">
                    <div class="icon-btn rounded-circle bg-light border p-2 d-flex align-items-center justify-content-center" style="width:38px; height:38px; cursor:pointer;">
                        <i class="bi bi-bell text-secondary"></i>
                    </div>

                    @auth
                    @php
                        $names = explode(' ', Auth::user()->name ?? 'User');
                        $initials = strtoupper(substr($names[0], 0, 1) . (isset($names[1]) ? substr($names[1], 0, 1) : ''));
                    @endphp
                    <div class="dropdown">
                        <div class="user-avatar-pill shadow-sm px-2.5 py-1" data-bs-toggle="dropdown" aria-expanded="false">
                            <div class="avatar-initials bg-dark text-warning border border-warning fw-bold">
                                {{ $initials ?: 'MM' }}
                            </div>
                            <div class="d-none d-lg-block text-start ms-1">
                                <div class="user-name-text mb-0">{{ Auth::user()->name }}</div>
                                <span class="badge bg-success-subtle text-success border border-success-subtle font-monospace px-2" style="font-size: 0.68rem;">{{ $roleDisplay }}</span>
                            </div>
                            <i class="bi bi-chevron-down text-muted small ms-1"></i>
                        </div>
                        <ul class="dropdown-menu dropdown-menu-end shadow-sm border mt-2">
                            <li class="px-3 py-2 border-bottom">
                                <div class="fw-bold text-dark">{{ Auth::user()->name }}</div>
                                <div class="small text-muted">{{ Auth::user()->email }}</div>
                            </li>
                            <li><a class="dropdown-item py-2" href="{{ route('dashboard') }}"><i class="bi bi-grid me-2"></i> Dashboard</a></li>
                            <li><a class="dropdown-item py-2" href="{{ route('settings.index') }}"><i class="bi bi-gear me-2"></i> Settings</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li>
                                <form action="{{ route('auth.logout') }}" method="POST" class="m-0">
                                    @csrf
                                    <button type="submit" class="dropdown-item py-2 text-danger">
                                        <i class="bi bi-box-arrow-right me-2"></i> Sign Out
                                    </button>
                                </form>
                            </li>
                        </ul>
                    </div>
                    @endauth
                </div>
            </header>

            <!-- Main Page Content -->
            <main class="flex-grow-1 p-3 p-md-4">
                @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show rounded-3 mb-4" role="alert">
                        <i class="bi bi-check-circle-fill me-2"></i> {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                @if(session('error'))
                    <div class="alert alert-danger alert-dismissible fade show rounded-3 mb-4" role="alert">
                        <i class="bi bi-exclamation-triangle-fill me-2"></i> {{ session('error') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                @if($errors->any())
                    <div class="alert alert-danger alert-dismissible fade show rounded-3 mb-4" role="alert">
                        <div class="fw-bold mb-1"><i class="bi bi-exclamation-triangle-fill me-2"></i> Validation Errors:</div>
                        <ul class="mb-0 ps-3 small">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                @yield('content')
            </main>
        </div>
    </div>

    <!-- Bootstrap 5 JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const toggleBtn = document.getElementById('mobileSidebarToggle');
            const sidebar = document.getElementById('appSidebar');
            const overlay = document.getElementById('sidebarOverlay');

            if (toggleBtn && sidebar && overlay) {
                toggleBtn.addEventListener('click', function() {
                    sidebar.classList.toggle('show');
                    overlay.classList.toggle('show');
                });

                overlay.addEventListener('click', function() {
                    sidebar.classList.remove('show');
                    overlay.classList.remove('show');
                });
            }
        });
    </script>
    @stack('scripts')
</body>
</html>
