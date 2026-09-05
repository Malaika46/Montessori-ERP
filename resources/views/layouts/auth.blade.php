<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Authentication') - {{ config('app.name', 'Montessori ERP') }}</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
    <link href="{{ asset('css/montessori-theme.css') }}" rel="stylesheet">

    <style>
        .auth-container {
            min-height: 100vh;
            display: flex;
        }
        .auth-brand-side {
            background-color: var(--sage-primary);
            color: #FFFFFF;
            padding: 3.5rem;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            position: relative;
            overflow: hidden;
        }
        .auth-brand-side::after {
            content: '';
            position: absolute;
            bottom: -50px;
            right: -50px;
            width: 300px;
            height: 300px;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.05);
        }
        .auth-form-side {
            background-color: var(--warm-cream);
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2.5rem 1.5rem;
        }
        .auth-card {
            background: #FFFFFF;
            border: 1px solid var(--soft-beige);
            border-radius: 16px;
            box-shadow: 0 10px 30px rgba(38, 51, 43, 0.04);
            width: 100%;
            max-width: 460px;
            padding: 2.5rem;
        }
    </style>
</head>
<body>
    <div class="container-fluid p-0">
        <div class="row g-0 auth-container">
            <!-- Left Branding Side -->
            <div class="col-lg-5 col-xl-6 d-none d-lg-flex auth-brand-side">
                <div>
                    <div class="d-flex align-items-center gap-3 mb-4">
                        <div class="bg-white text-dark rounded-3 d-flex align-items-center justify-content-center" style="width: 48px; height: 48px; font-size: 1.5rem;">
                            <i class="bi bi-flower2 text-success"></i>
                        </div>
                        <span class="fs-4 fw-bold text-white tracking-tight">Montessori ERP</span>
                    </div>
                </div>

                <div class="my-auto py-5">
                    <h2 class="fw-bold text-white display-6 mb-3">Empowering Montessori Education</h2>
                    <p class="lead text-white-50 fs-6" style="max-width: 480px;">
                        Integrated child-centered observation, progress tracking, multi-campus administration, and parent partnership in one calm platform.
                    </p>
                    
                    <div class="d-flex gap-3 mt-4">
                        <div class="bg-white bg-opacity-10 rounded-3 p-3 border border-white border-opacity-10 text-white flex-fill">
                            <div class="fs-4 fw-bold mb-1"><i class="bi bi-eye"></i></div>
                            <div class="small fw-medium">Observation Tracking</div>
                        </div>
                        <div class="bg-white bg-opacity-10 rounded-3 p-3 border border-white border-opacity-10 text-white flex-fill">
                            <div class="fs-4 fw-bold mb-1"><i class="bi bi-shield-check"></i></div>
                            <div class="small fw-medium">Multi-Role Security</div>
                        </div>
                    </div>
                </div>

                <div class="small text-white-50">
                    &copy; {{ date('Y') }} Montessori ERP. Production School Platform.
                </div>
            </div>

            <!-- Right Form Side -->
            <div class="col-lg-7 col-xl-6 auth-form-side">
                <div class="auth-card">
                    @yield('content')
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
