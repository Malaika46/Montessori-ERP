<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Montessori ERP | Intelligent School & LMS Platform</title>

    <!-- Google Fonts: Outfit & Inter -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Outfit:wght@500;600;700;800&display=swap" rel="stylesheet">

    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
    <!-- Custom Theme -->
    <link href="{{ asset('css/montessori-theme.css') }}" rel="stylesheet">

    <style>
        :root {
            --m-forest: #1c382b;
            --m-forest-dark: #0f291e;
            --m-forest-light: #2d5a45;
            --m-gold: #d97706;
            --m-sage: #e8f0eb;
            --m-sand: #fbf9f5;
        }

        body {
            font-family: 'Inter', sans-serif;
            background-color: #f8faf9;
            color: #1e293b;
            overflow-x: hidden;
        }

        h1, h2, h3, h4, h5, .brand-title {
            font-family: 'Outfit', sans-serif;
        }

        /* Glassmorphic Top Navbar */
        .navbar-glass {
            background: rgba(255, 255, 255, 0.88);
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            border-bottom: 1px solid rgba(28, 56, 43, 0.08);
            padding: 0.9rem 0;
            position: sticky;
            top: 0;
            z-index: 1050;
            transition: all 0.3s ease;
        }

        .brand-logo-icon {
            width: 42px;
            height: 42px;
            background: linear-gradient(135deg, var(--m-forest) 0%, var(--m-forest-light) 100%);
            color: #ffffff;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.4rem;
            box-shadow: 0 4px 12px rgba(28, 56, 43, 0.2);
        }

        /* Hero Banner Section */
        .hero-banner {
            position: relative;
            padding: 4.5rem 0 5rem;
            background: radial-gradient(circle at 50% 0%, rgba(45, 90, 69, 0.08) 0%, rgba(248, 250, 249, 0) 70%),
                        linear-gradient(180deg, #ffffff 0%, #f4f7f5 100%);
            border-bottom: 1px solid #e2e8f0;
        }

        .hero-badge {
            background: rgba(28, 56, 43, 0.07);
            color: var(--m-forest);
            border: 1px solid rgba(28, 56, 43, 0.15);
            font-size: 0.85rem;
            font-weight: 600;
            padding: 0.4rem 1.1rem;
            border-radius: 50rem;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
        }

        .hero-title {
            font-size: 3.2rem;
            font-weight: 800;
            color: var(--m-forest-dark);
            line-height: 1.18;
            letter-spacing: -0.8px;
        }

        .btn-m-hero-primary {
            background: linear-gradient(135deg, var(--m-forest) 0%, var(--m-forest-light) 100%);
            color: #ffffff !important;
            font-family: 'Outfit', sans-serif;
            font-weight: 700;
            padding: 0.85rem 2rem;
            border-radius: 50rem;
            border: none;
            box-shadow: 0 8px 25px rgba(28, 56, 43, 0.25);
            transition: all 0.25s ease;
            display: inline-flex;
            align-items: center;
            gap: 0.6rem;
            text-decoration: none;
        }

        .btn-m-hero-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 12px 30px rgba(28, 56, 43, 0.35);
            color: #ffffff;
        }

        .btn-m-hero-outline {
            background: #ffffff;
            color: var(--m-forest) !important;
            font-family: 'Outfit', sans-serif;
            font-weight: 700;
            padding: 0.85rem 2rem;
            border-radius: 50rem;
            border: 2px solid var(--m-forest);
            transition: all 0.25s ease;
            display: inline-flex;
            align-items: center;
            gap: 0.6rem;
            text-decoration: none;
        }

        .btn-m-hero-outline:hover {
            background: var(--m-sage);
            transform: translateY(-2px);
            color: var(--m-forest);
        }

        /* Mockup Card Glass */
        .mockup-window {
            background: #ffffff;
            border-radius: 20px;
            border: 1px solid #e2e8f0;
            box-shadow: 0 20px 50px -10px rgba(28, 56, 43, 0.12);
            overflow: hidden;
        }

        .mockup-header {
            background: #f8faf9;
            border-bottom: 1px solid #e2e8f0;
            padding: 0.9rem 1.25rem;
        }

        .dot {
            width: 11px;
            height: 11px;
            border-radius: 50%;
            display: inline-block;
        }

        .stat-card-hero {
            background: #ffffff;
            border-radius: 14px;
            border: 1px solid #e2e8f0;
            padding: 1.25rem;
            transition: all 0.2s ease;
        }

        .stat-card-hero:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 20px rgba(0,0,0,0.04);
        }

        /* Feature Cards */
        .feature-card {
            background: #ffffff;
            border-radius: 18px;
            border: 1px solid #e2e8f0;
            padding: 2rem;
            transition: all 0.3s ease;
            height: 100%;
        }

        .feature-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 14px 35px rgba(28, 56, 43, 0.08);
            border-color: rgba(28, 56, 43, 0.3);
        }

        .icon-box-lg {
            width: 58px;
            height: 58px;
            border-radius: 16px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.6rem;
            margin-bottom: 1.25rem;
        }

        .icon-forest { background: var(--m-sage); color: var(--m-forest); }
        .icon-gold { background: #fef3c7; color: var(--m-gold); }
        .icon-teal { background: #ccfbf1; color: #0d9488; }
        .icon-rose { background: #ffe4e6; color: #e11d48; }

        /* Footer */
        .footer-dark {
            background: var(--m-forest-dark);
            color: #94a3b8;
            padding: 4rem 0 2rem;
            border-top: 1px solid rgba(255,255,255,0.08);
        }
    </style>
</head>
<body>

    <!-- NAVBAR -->
    <header class="navbar-glass">
        <div class="container d-flex align-items-center justify-content-between">
            <a href="{{ route('landing') }}" class="d-flex align-items-center gap-3 text-decoration-none">
                <div class="brand-logo-icon">
                    <i class="bi bi-flower2"></i>
                </div>
                <div>
                    <h5 class="fw-bold text-dark mb-0 brand-title" style="letter-spacing: -0.3px;">Montessori ERP</h5>
                    <span class="text-success small fw-bold text-uppercase" style="font-size: 0.65rem; letter-spacing: 0.5px;">Campus Management & LMS</span>
                </div>
            </a>
            
            <nav class="d-none d-md-flex align-items-center gap-4">
                <a href="#overview" class="text-secondary fw-semibold text-decoration-none small">Platform Overview</a>
                <a href="#features" class="text-secondary fw-semibold text-decoration-none small">Core Capabilities</a>
                <a href="#montessori" class="text-secondary fw-semibold text-decoration-none small">Pedagogy</a>
                <a href="#roles" class="text-secondary fw-semibold text-decoration-none small">Role Architecture</a>
            </nav>

            <div class="d-flex align-items-center gap-2">
                <a href="{{ route('auth.login') }}" class="btn btn-outline-secondary rounded-pill px-4 fw-bold btn-sm">
                    <i class="bi bi-box-arrow-in-right me-1"></i> Sign In
                </a>
                <a href="{{ route('dashboard') }}" class="btn btn-success rounded-pill px-4 fw-bold btn-sm shadow-sm" style="background-color: var(--m-forest); border-color: var(--m-forest);">
                    <i class="bi bi-speedometer2 me-1"></i> Open Workspace
                </a>
            </div>
        </div>
    </header>

    <!-- HERO SECTION -->
    <section class="hero-banner text-center">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-10">
                    <div class="mb-3">
                        <span class="hero-badge shadow-sm">
                            <i class="bi bi-stars text-warning fs-6"></i> Next-Gen Montessori School ERP & LMS Platform
                        </span>
                    </div>

                    <h1 class="hero-title mb-3">
                        Empowering Montessori Education <br class="d-none d-md-block">
                        <span style="color: var(--m-forest-light);">Through One Intelligent Platform</span>
                    </h1>

                    <p class="fs-5 text-secondary mb-4 mx-auto lh-base" style="max-width: 760px;">
                        Manage students, mixed-age environments, qualitative observation mastery, gate pass security, parent communications, and finance from one unified system.
                    </p>

                    <div class="d-flex flex-wrap align-items-center justify-content-center gap-3 mb-5">
                        <a href="{{ route('dashboard') }}" class="btn-m-hero-primary">
                            <i class="bi bi-rocket-takeoff-fill"></i> Launch Admin Workspace
                        </a>
                        <a href="{{ route('auth.login') }}" class="btn-m-hero-outline">
                            <i class="bi bi-person-circle"></i> Portal & Parent Sign In
                        </a>
                    </div>
                </div>
            </div>

            <!-- HERO MOCKUP CARD -->
            <div class="row justify-content-center">
                <div class="col-lg-11">
                    <div class="mockup-window text-start">
                        <div class="mockup-header d-flex align-items-center justify-content-between">
                            <div class="d-flex align-items-center gap-2">
                                <span class="dot bg-danger"></span>
                                <span class="dot bg-warning"></span>
                                <span class="dot bg-success"></span>
                                <span class="small text-muted ms-2 font-monospace">app.montessorierp.com / executive-overview</span>
                            </div>
                            <div class="d-flex align-items-center gap-2">
                                <span class="badge bg-success-subtle text-success border border-success-subtle px-3 py-1 rounded-pill fw-bold fs-8">
                                    <i class="bi bi-circle-fill fs-8 me-1 text-success"></i> Live System Active
                                </span>
                            </div>
                        </div>

                        <div class="p-4 bg-light bg-opacity-50">
                            <div class="row g-3 mb-4">
                                <div class="col-md-3">
                                    <div class="stat-card-hero border-start border-4 border-success">
                                        <div class="d-flex justify-content-between align-items-start mb-2">
                                            <span class="text-uppercase text-muted fw-bold fs-8">Enrolled Students</span>
                                            <span class="badge bg-success-subtle text-success fw-bold">+12 Term</span>
                                        </div>
                                        <h3 class="fw-bold text-dark mb-0">142</h3>
                                        <span class="text-muted fs-8">Active primary & toddler profiles</span>
                                    </div>
                                </div>

                                <div class="col-md-3">
                                    <div class="stat-card-hero border-start border-4 border-teal" style="border-left-color: #0d9488 !important;">
                                        <div class="d-flex justify-content-between align-items-start mb-2">
                                            <span class="text-uppercase text-muted fw-bold fs-8">Active Environments</span>
                                            <span class="badge bg-info-subtle text-info fw-bold">8 Rooms</span>
                                        </div>
                                        <h3 class="fw-bold text-dark mb-0">8 Classrooms</h3>
                                        <span class="text-muted fs-8">Toddler, Primary A-D, Elementary</span>
                                    </div>
                                </div>

                                <div class="col-md-3">
                                    <div class="stat-card-hero border-start border-4 border-warning">
                                        <div class="d-flex justify-content-between align-items-start mb-2">
                                            <span class="text-uppercase text-muted fw-bold fs-8">Observations Recorded</span>
                                            <span class="badge bg-warning-subtle text-warning fw-bold">100% Sync</span>
                                        </div>
                                        <h3 class="fw-bold text-dark mb-0">1,280 Logged</h3>
                                        <span class="text-muted fs-8">Mastery work cycle entries</span>
                                    </div>
                                </div>

                                <div class="col-md-3">
                                    <div class="stat-card-hero border-start border-4 border-primary">
                                        <div class="d-flex justify-content-between align-items-start mb-2">
                                            <span class="text-uppercase text-muted fw-bold fs-8">Attendance Rate</span>
                                            <span class="badge bg-primary-subtle text-primary fw-bold">QR Gate Pass</span>
                                        </div>
                                        <h3 class="fw-bold text-dark mb-0">98.4%</h3>
                                        <span class="text-muted fs-8">Daily check-in verification</span>
                                    </div>
                                </div>
                            </div>

                            <!-- Interactive Live Activity Strip -->
                            <div class="bg-white p-3 rounded-4 border d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3 shadow-sm">
                                <div class="d-flex align-items-center gap-3">
                                    <div class="p-2 rounded-circle text-white text-center" style="background: var(--m-forest); width: 42px; height: 42px;">
                                        <i class="bi bi-journal-check fs-5"></i>
                                    </div>
                                    <div>
                                        <div class="fw-bold text-dark small">Latest Observation Entry</div>
                                        <div class="text-muted fs-8">Directress Ayesha recorded <strong>"Pink Tower Graduated Cubes"</strong> for <strong>Malaika Muneer</strong> • <span class="text-success fw-bold">MASTERED</span></div>
                                    </div>
                                </div>
                                <a href="{{ route('dashboard') }}" class="btn btn-sm btn-outline-success rounded-pill px-3 fw-bold">
                                    View Live Feed <i class="bi bi-arrow-right"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </section>

    <!-- PLATFORM OVERVIEW -->
    <section class="py-5" id="overview">
        <div class="container py-4">
            <div class="text-center mb-5">
                <span class="text-success fw-bold text-uppercase fs-7 tracking-wider">Enterprise Architecture</span>
                <h2 class="display-6 fw-bold text-dark mt-1">Unified School Management System</h2>
                <p class="text-secondary mx-auto" style="max-width: 680px;">Designed specifically to honor Maria Montessori's child-centered principles while streamlining campus administration and financial operations.</p>
            </div>

            <div class="row g-4">
                <div class="col-md-4">
                    <div class="feature-card">
                        <div class="icon-box-lg icon-forest">
                            <i class="bi bi-diagram-3-fill"></i>
                        </div>
                        <h4 class="fw-bold text-dark mb-2">Multi-Role Security</h4>
                        <p class="text-secondary small mb-0">Scalable enterprise architecture with dedicated dashboards for Superadmins, Principals, Guides/Teachers, Students, and Parents.</p>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="feature-card">
                        <div class="icon-box-lg icon-gold">
                            <i class="bi bi-journal-bookmark-fill"></i>
                        </div>
                        <h4 class="fw-bold text-dark mb-2">Child Progress & Mastery</h4>
                        <p class="text-secondary small mb-0">Track observations, three-period lesson progressions, material choices, and qualitative report cards without numerical exam stress.</p>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="feature-card">
                        <div class="icon-box-lg icon-teal">
                            <i class="bi bi-cash-stack"></i>
                        </div>
                        <h4 class="fw-bold text-dark mb-2">Finance & Operations</h4>
                        <p class="text-secondary small mb-0">Automated student fee structures, tuition challans (PKR), staff payroll ledger, material inventory, and digital gate pass security.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- CORE CAPABILITIES GRID -->
    <section class="py-5 bg-white border-top border-bottom" id="features">
        <div class="container py-4">
            <div class="text-center mb-5">
                <span class="text-success fw-bold text-uppercase fs-7">Designed For Excellence</span>
                <h2 class="display-6 fw-bold text-dark mt-1">Core Modules & Capabilities</h2>
                <p class="text-secondary mx-auto" style="max-width: 680px;">Built with high aesthetic precision, responsive navigation, and strict role security.</p>
            </div>

            <div class="row g-4">
                <div class="col-md-6 col-lg-4">
                    <div class="d-flex align-items-start gap-3 p-4 rounded-4 border bg-light h-100">
                        <div class="icon-box-lg icon-forest mb-0 flex-shrink-0" style="width: 48px; height: 48px; font-size: 1.3rem;">
                            <i class="bi bi-grid-fill"></i>
                        </div>
                        <div>
                            <h5 class="fw-bold text-dark mb-1">Classroom Environments</h5>
                            <p class="small text-secondary mb-0">Organize mixed-age communities (Toddler, Primary, Elementary) with specialized material inventories.</p>
                        </div>
                    </div>
                </div>

                <div class="col-md-6 col-lg-4">
                    <div class="d-flex align-items-start gap-3 p-4 rounded-4 border bg-light h-100">
                        <div class="icon-box-lg icon-gold mb-0 flex-shrink-0" style="width: 48px; height: 48px; font-size: 1.3rem;">
                            <i class="bi bi-eye-fill"></i>
                        </div>
                        <div>
                            <h5 class="fw-bold text-dark mb-1">Observation & Work Curve</h5>
                            <p class="small text-secondary mb-0">Log individual concentration spans, material choices, and stage transitions (Introduced -> Working -> Mastered).</p>
                        </div>
                    </div>
                </div>

                <div class="col-md-6 col-lg-4">
                    <div class="d-flex align-items-start gap-3 p-4 rounded-4 border bg-light h-100">
                        <div class="icon-box-lg icon-teal mb-0 flex-shrink-0" style="width: 48px; height: 48px; font-size: 1.3rem;">
                            <i class="bi bi-qr-code-scan"></i>
                        </div>
                        <div>
                            <h5 class="fw-bold text-dark mb-1">Attendance & Gate Pass</h5>
                            <p class="small text-secondary mb-0">Real-time daily arrival tracking, digital pickup QR gate passes, and parent check-in notifications.</p>
                        </div>
                    </div>
                </div>

                <div class="col-md-6 col-lg-4">
                    <div class="d-flex align-items-start gap-3 p-4 rounded-4 border bg-light h-100">
                        <div class="icon-box-lg icon-rose mb-0 flex-shrink-0" style="width: 48px; height: 48px; font-size: 1.3rem;">
                            <i class="bi bi-file-earmark-bar-graph"></i>
                        </div>
                        <div>
                            <h5 class="fw-bold text-dark mb-1">Narrative Assessments</h5>
                            <p class="small text-secondary mb-0">Generate developmental domain rubrics and official PDF term progress cards for parents.</p>
                        </div>
                    </div>
                </div>

                <div class="col-md-6 col-lg-4">
                    <div class="d-flex align-items-start gap-3 p-4 rounded-4 border bg-light h-100">
                        <div class="icon-box-lg icon-forest mb-0 flex-shrink-0" style="width: 48px; height: 48px; font-size: 1.3rem;">
                            <i class="bi bi-chat-quote-fill"></i>
                        </div>
                        <div>
                            <h5 class="fw-bold text-dark mb-1">Communication Hub</h5>
                            <p class="small text-secondary mb-0">Direct parent-directress messaging, school notices, announcement broadcasts, and calendar events.</p>
                        </div>
                    </div>
                </div>

                <div class="col-md-6 col-lg-4">
                    <div class="d-flex align-items-start gap-3 p-4 rounded-4 border bg-light h-100">
                        <div class="icon-box-lg icon-gold mb-0 flex-shrink-0" style="width: 48px; height: 48px; font-size: 1.3rem;">
                            <i class="bi bi-receipt-cutoff"></i>
                        </div>
                        <div>
                            <h5 class="fw-bold text-dark mb-1">Fee Vouchers & Ledger</h5>
                            <p class="small text-secondary mb-0">Transparent billing in PKR, instant receipt downloads, and online payment status tracking.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- MONTESSORI PEDAGOGY SECTION -->
    <section class="py-5" id="montessori">
        <div class="container py-4">
            <div class="row align-items-center g-5">
                <div class="col-lg-6">
                    <span class="badge bg-success-subtle text-success px-3 py-2 rounded-pill fw-bold mb-2">Pedagogical Alignment</span>
                    <h2 class="display-6 fw-bold text-dark mb-3">Honoring Dr. Maria Montessori's Principles</h2>
                    <p class="text-secondary mb-4">Unlike rigid traditional software built for standard letter grades, Montessori ERP centers around five foundational curriculum areas:</p>
                    
                    <div class="d-flex flex-column gap-3">
                        <div class="d-flex align-items-start gap-3">
                            <i class="bi bi-check-circle-fill text-success fs-5 mt-1"></i>
                            <div>
                                <strong class="text-dark">Practical Life:</strong>
                                <span class="text-muted small"> Motor coordination, pouring, dressing frames, grace & courtesy.</span>
                            </div>
                        </div>
                        <div class="d-flex align-items-start gap-3">
                            <i class="bi bi-check-circle-fill text-success fs-5 mt-1"></i>
                            <div>
                                <strong class="text-dark">Sensorial Area:</strong>
                                <span class="text-muted small"> Pink Tower, Broad Stair, Color Tablets, tactile and acoustic ordering.</span>
                            </div>
                        </div>
                        <div class="d-flex align-items-start gap-3">
                            <i class="bi bi-check-circle-fill text-success fs-5 mt-1"></i>
                            <div>
                                <strong class="text-dark">Mathematics:</strong>
                                <span class="text-muted small"> Number Rods, Golden Beads, Spindle Boxes, Stamp Game.</span>
                            </div>
                        </div>
                        <div class="d-flex align-items-start gap-3">
                            <i class="bi bi-check-circle-fill text-success fs-5 mt-1"></i>
                            <div>
                                <strong class="text-dark">Language:</strong>
                                <span class="text-muted small"> Sandpaper Letters, Moveable Alphabet, Three-Period Lessons.</span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-6">
                    <div class="card border-0 shadow-lg rounded-4 p-4 text-white" style="background: linear-gradient(135deg, #1c382b 0%, #2d5a45 100%);">
                        <h4 class="fw-bold mb-3 text-white"><i class="bi bi-diagram-2 me-2"></i> Observation Progression Flow</h4>
                        
                        <div class="p-3 bg-white bg-opacity-10 rounded-3 mb-3 border border-white border-opacity-25">
                            <div class="fw-bold text-warning mb-1"><i class="bi bi-sparkles me-1"></i> Stage 1: Introduced</div>
                            <div class="small text-white-50">Lead directress gives initial individual presentation with material.</div>
                        </div>

                        <div class="p-3 bg-white bg-opacity-10 rounded-3 mb-3 border border-white border-opacity-25">
                            <div class="fw-bold text-info mb-1"><i class="bi bi-gear-wide-connected me-1"></i> Stage 2: Working Stage</div>
                            <div class="small text-white-50">Child freely chooses material during 3-hour uninterrupted work cycle.</div>
                        </div>

                        <div class="p-3 bg-white bg-opacity-10 rounded-3 border border-white border-opacity-25">
                            <div class="fw-bold text-success mb-1"><i class="bi bi-check-circle-fill me-1"></i> Stage 3: Mastered</div>
                            <div class="small text-white-50">Child demonstrates total independence and assists younger peers.</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- CALL TO ACTION BANNER -->
    <section class="py-5 text-center text-white" style="background: linear-gradient(135deg, var(--m-forest-dark) 0%, var(--m-forest) 100%);">
        <div class="container py-4">
            <h2 class="display-6 fw-bold mb-3 text-white">Ready to Elevate Your Montessori Campus?</h2>
            <p class="text-white-50 mb-4 mx-auto" style="max-width: 640px;">Access the interactive portal now to explore multi-role dashboards, student journals, and administrative tools.</p>
            
            <div class="d-flex justify-content-center gap-3">
                <a href="{{ route('dashboard') }}" class="btn btn-light rounded-pill px-5 py-3 fw-bold text-success fs-6 shadow-lg">
                    <i class="bi bi-rocket-takeoff-fill me-2"></i> Launch Workspace Dashboard
                </a>
                <a href="{{ route('auth.login') }}" class="btn btn-outline-light rounded-pill px-5 py-3 fw-bold fs-6">
                    <i class="bi bi-box-arrow-in-right me-2"></i> Portal Login
                </a>
            </div>
        </div>
    </section>

    <!-- FOOTER -->
    <footer class="footer-dark">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-md-6 text-center text-md-start mb-3 mb-md-0">
                    <div class="d-flex align-items-center gap-2 mb-1 justify-content-center justify-content-md-start">
                        <i class="bi bi-flower2 text-success fs-4"></i>
                        <span class="fw-bold text-white fs-5 brand-title">Montessori ERP</span>
                    </div>
                    <div class="small text-muted">© {{ date('Y') }} Montessori ERP Platform. All rights reserved.</div>
                </div>
                <div class="col-md-6 text-center text-md-end">
                    <a href="{{ route('auth.login') }}" class="text-white-50 text-decoration-none small me-3">Sign In</a>
                    <a href="{{ route('auth.register') }}" class="text-white-50 text-decoration-none small me-3">Register</a>
                    <a href="{{ route('dashboard') }}" class="text-white-50 text-decoration-none small">Admin Workspace</a>
                </div>
            </div>
        </div>
    </footer>

</body>
</html>
