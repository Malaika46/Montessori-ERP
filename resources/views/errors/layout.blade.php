<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('code') - @yield('title') - {{ config('app.name', 'Montessori ERP') }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
    <link href="{{ asset('css/montessori-theme.css') }}" rel="stylesheet">
    <style>
        body {
            background-color: var(--warm-cream);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .error-card {
            background-color: #FFFFFF;
            border: 1px solid var(--soft-beige);
            border-radius: 18px;
            box-shadow: 0 10px 30px rgba(38, 51, 43, 0.05);
            max-width: 500px;
            width: 100%;
            padding: 3rem 2rem;
            text-align: center;
        }
        .error-code {
            font-size: 4.5rem;
            font-weight: 700;
            color: var(--sage-primary);
            line-height: 1;
            margin-bottom: 0.5rem;
        }
    </style>
</head>
<body>
    <div class="error-card">
        <div class="error-code">@yield('code')</div>
        <h4 class="fw-bold text-dark mb-2">@yield('title')</h4>
        <p class="text-muted small mb-4">@yield('message')</p>
        <div class="d-flex justify-content-center gap-2">
            <a href="{{ route('landing') }}" class="btn btn-m-secondary"><i class="bi bi-house"></i> Home Page</a>
            @auth
                <a href="{{ route('dashboard') }}" class="btn btn-m-primary"><i class="bi bi-grid"></i> Dashboard</a>
            @else
                <a href="{{ route('auth.login') }}" class="btn btn-m-primary"><i class="bi bi-box-arrow-in-right"></i> Sign In</a>
            @endauth
        </div>
    </div>
</body>
</html>
