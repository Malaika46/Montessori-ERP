<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Official Report Card - {{ $assessment->student ? $assessment->student->first_name . ' ' . $assessment->student->last_name : 'Student' }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f8f9fa;
            color: #1a252c;
            padding: 30px 0;
        }
        .report-card-container {
            max-width: 850px;
            background: #ffffff;
            border-radius: 12px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.08);
            border: 2px solid #1c3b2b;
            overflow: hidden;
            margin: 0 auto;
        }
        .report-header {
            background: linear-gradient(135deg, #1c3b2b 0%, #12281d 100%);
            color: #ffffff;
            padding: 30px;
            border-bottom: 4px solid #d4af37;
        }
        .school-title {
            font-size: 1.6rem;
            font-weight: 800;
            letter-spacing: 1px;
            color: #d4af37;
            text-transform: uppercase;
        }
        .badge-verified {
            background-color: #d4af37;
            color: #12281d;
            font-weight: 700;
            padding: 6px 14px;
            border-radius: 20px;
            font-size: 0.82rem;
            text-transform: uppercase;
        }
        .student-info-box {
            background-color: #f3f6f4;
            border-left: 4px solid #1c3b2b;
            padding: 18px 24px;
            border-radius: 6px;
        }
        .avenue-card {
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            padding: 16px;
            background-color: #ffffff;
        }
        .progress-bar-montessori {
            height: 10px;
            border-radius: 5px;
        }
        .summary-quote-box {
            background: #fdfbf7;
            border: 1px solid #e9dfc4;
            border-left: 5px solid #d4af37;
            padding: 20px;
            border-radius: 6px;
            font-style: italic;
        }
        .seal-stamp {
            width: 90px;
            height: 90px;
            border: 2px dashed #d4af37;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #1c3b2b;
            font-weight: 700;
            font-size: 0.72rem;
            text-align: center;
            transform: rotate(-10deg);
        }
        @media print {
            body { background: white; padding: 0; }
            .no-print { display: none !important; }
            .report-card-container { box-shadow: none; border: 1px solid #333; }
        }
    </style>
</head>
<body>

<div class="container no-print mb-4 text-center">
    <button onclick="window.print()" class="btn btn-lg btn-success px-4 shadow">
        <i class="bi bi-printer-fill me-2"></i> Download / Print Official PDF Report Card
    </button>
    <a href="{{ route('assessments.index') }}" class="btn btn-lg btn-outline-secondary ms-2">
        <i class="bi bi-arrow-left me-1"></i> Back to Evaluation Center
    </a>
</div>

<div class="report-card-container">
    <!-- Header -->
    <div class="report-header text-center position-relative">
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-2">
            <span class="badge-verified"><i class="bi bi-shield-check"></i> Official Verified Report</span>
            <span class="small text-white-50">Evaluation Term: {{ $assessment->term_name }} ({{ $assessment->evaluation_period }})</span>
        </div>
        <h2 class="school-title mb-1">Montessori ERP International Academy</h2>
        <p class="mb-0 text-white-50 small">Child Assessment & Comprehensive Developmental Narrative Progress Card</p>
    </div>

    <div class="p-4 p-md-5">
        <!-- Title & Published Date -->
        <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2 border-bottom pb-3">
            <div>
                <h4 class="fw-bold text-dark mb-0">{{ $assessment->title }}</h4>
                <small class="text-muted">Issued Date: {{ $assessment->published_at ? $assessment->published_at->format('F d, Y') : date('F d, Y') }}</small>
            </div>
            <div class="text-end">
                <span class="fs-4 fw-bold text-success">{{ $assessment->overall_score }}%</span>
                <div class="small text-muted">Overall Mastery Grade</div>
            </div>
        </div>

        <!-- Student Profile Box -->
        <div class="student-info-box mb-4">
            <div class="row g-3">
                <div class="col-6 col-md-3">
                    <div class="small text-muted text-uppercase fw-semibold">Student Name</div>
                    <div class="fw-bold text-dark">{{ $assessment->student ? $assessment->student->first_name . ' ' . $assessment->student->last_name : 'N/A' }}</div>
                </div>
                <div class="col-6 col-md-3">
                    <div class="small text-muted text-uppercase fw-semibold">Classroom Environment</div>
                    <div class="fw-bold text-dark">{{ $assessment->classroom ? $assessment->classroom->name : 'N/A' }}</div>
                </div>
                <div class="col-6 col-md-3">
                    <div class="small text-muted text-uppercase fw-semibold">Lead Directress</div>
                    <div class="fw-bold text-dark">{{ $assessment->teacher && $assessment->teacher->user ? $assessment->teacher->user->name : 'Directress / Staff' }}</div>
                </div>
                <div class="col-6 col-md-3">
                    <div class="small text-muted text-uppercase fw-semibold">Roll Number / ID</div>
                    <div class="fw-bold text-dark">MNT-{{ str_pad($assessment->student_id, 4, '0', STR_PAD_LEFT) }}</div>
                </div>
            </div>
        </div>

        <!-- 5 Montessori Avenues Assessment Grid -->
        <h5 class="fw-bold text-dark mb-3"><i class="bi bi-grid-3x3-gap-fill text-success me-2"></i> Developmental Rubric Assessment</h5>

        <div class="row g-3 mb-4">
            <!-- Practical Life -->
            <div class="col-md-6">
                <div class="avenue-card">
                    <div class="d-flex justify-content-between align-items-center mb-1">
                        <span class="fw-semibold text-dark"><i class="bi bi-hand-index-fill text-success me-1"></i> Practical Life</span>
                        <span class="badge bg-success-subtle text-success border border-success-subtle fw-bold">{{ $assessment->practical_life_status }} ({{ $assessment->practical_life_score }}%)</span>
                    </div>
                    <div class="progress progress-bar-montessori bg-light">
                        <div class="progress-bar bg-success" style="width: {{ $assessment->practical_life_score }}%"></div>
                    </div>
                </div>
            </div>

            <!-- Sensorial -->
            <div class="col-md-6">
                <div class="avenue-card">
                    <div class="d-flex justify-content-between align-items-center mb-1">
                        <span class="fw-semibold text-dark"><i class="bi bi-eye-fill text-primary me-1"></i> Sensorial Area</span>
                        <span class="badge bg-primary-subtle text-primary border border-primary-subtle fw-bold">{{ $assessment->sensorial_status }} ({{ $assessment->sensorial_score }}%)</span>
                    </div>
                    <div class="progress progress-bar-montessori bg-light">
                        <div class="progress-bar bg-primary" style="width: {{ $assessment->sensorial_score }}%"></div>
                    </div>
                </div>
            </div>

            <!-- Mathematics -->
            <div class="col-md-6">
                <div class="avenue-card">
                    <div class="d-flex justify-content-between align-items-center mb-1">
                        <span class="fw-semibold text-dark"><i class="bi bi-calculator-fill text-warning me-1"></i> Mathematics</span>
                        <span class="badge bg-warning-subtle text-dark border border-warning-subtle fw-bold">{{ $assessment->mathematics_status }} ({{ $assessment->mathematics_score }}%)</span>
                    </div>
                    <div class="progress progress-bar-montessori bg-light">
                        <div class="progress-bar bg-warning" style="width: {{ $assessment->mathematics_score }}%"></div>
                    </div>
                </div>
            </div>

            <!-- Language -->
            <div class="col-md-6">
                <div class="avenue-card">
                    <div class="d-flex justify-content-between align-items-center mb-1">
                        <span class="fw-semibold text-dark"><i class="bi bi-chat-quote-fill text-info me-1"></i> Language & Phonics</span>
                        <span class="badge bg-info-subtle text-info border border-info-subtle fw-bold">{{ $assessment->language_status }} ({{ $assessment->language_score }}%)</span>
                    </div>
                    <div class="progress progress-bar-montessori bg-light">
                        <div class="progress-bar bg-info" style="width: {{ $assessment->language_score }}%"></div>
                    </div>
                </div>
            </div>

            <!-- Cultural -->
            <div class="col-12">
                <div class="avenue-card">
                    <div class="d-flex justify-content-between align-items-center mb-1">
                        <span class="fw-semibold text-dark"><i class="bi bi-globe-americas text-danger me-1"></i> Cultural & World Studies</span>
                        <span class="badge bg-danger-subtle text-danger border border-danger-subtle fw-bold">{{ $assessment->cultural_status }} ({{ $assessment->cultural_score }}%)</span>
                    </div>
                    <div class="progress progress-bar-montessori bg-light">
                        <div class="progress-bar bg-danger" style="width: {{ $assessment->cultural_score }}%"></div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Directress Narrative Summary -->
        <h5 class="fw-bold text-dark mb-2"><i class="bi bi-chat-left-text-fill text-primary me-2"></i> Directress Overall Narrative Summary</h5>
        <div class="summary-quote-box mb-5">
            "{{ $assessment->overall_summary }}"
        </div>

        <!-- Signatures & Verification Seal -->
        <div class="d-flex justify-content-between align-items-end pt-4 border-top">
            <div>
                <div class="seal-stamp">
                    OFFICIAL<br>MONTESSORI<br>SEAL
                </div>
            </div>
            <div class="text-center" style="width: 180px;">
                <div class="border-bottom border-dark pb-1 mb-1 fw-bold text-dark">
                    {{ $assessment->teacher && $assessment->teacher->user ? $assessment->teacher->user->name : 'Lead Directress' }}
                </div>
                <div class="small text-muted">Lead Directress Signature</div>
            </div>
            <div class="text-center" style="width: 180px;">
                <div class="border-bottom border-dark pb-1 mb-1 fw-bold text-dark">
                    Campus Principal
                </div>
                <div class="small text-muted">Campus Administrator</div>
            </div>
        </div>

    </div>
</div>

</body>
</html>
