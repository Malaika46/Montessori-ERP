<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ModuleController extends Controller
{
    protected array $modules = [
        'students' => [
            'title' => 'Student Management',
            'subtitle' => 'Comprehensive Montessori student records, family linking, and developmental tracking.',
            'icon' => 'bi bi-people-fill'
        ],
        'parents' => [
            'title' => 'Parent Portal Directory',
            'subtitle' => 'Parent contacts, guardianship linkages, emergency profiles, and communication preferences.',
            'icon' => 'bi bi-person-heart'
        ],
        'classrooms' => [
            'title' => 'Classrooms & Environments',
            'subtitle' => 'Mixed-age Montessori prepared environments (Toddler, Primary, Lower/Upper Elementary).',
            'icon' => 'bi bi-door-open-fill'
        ],
        'curriculum' => [
            'title' => 'Montessori Curriculum',
            'subtitle' => 'Practical Life, Sensorial, Mathematics, Language, and Cultural Area material maps.',
            'icon' => 'bi bi-journal-bookmark-fill'
        ],
        'lessons' => [
            'title' => 'Lesson Planning & Tracking',
            'subtitle' => 'Three-period lesson planning, individual presentations, and small group scheduling.',
            'icon' => 'bi bi-calendar-event-fill'
        ],
        'observations' => [
            'title' => 'Observation & Mastery Engine',
            'subtitle' => 'Record qualitative focus notes, choice patterns, and progress stages (Introduced, Working, Mastered).',
            'icon' => 'bi bi-eye-fill'
        ],
        'lms' => [
            'title' => 'Gamified Student LMS',
            'subtitle' => 'Child-friendly interactive digital exploration, badge achievements, and story quests.',
            'icon' => 'bi bi-controller'
        ],
        'assessments' => [
            'title' => 'Assessments & Narrative Reports',
            'subtitle' => 'Generate holistic progress summaries, developmental rubrics, and parent reports.',
            'icon' => 'bi bi-file-earmark-bar-graph-fill'
        ],
        'attendance' => [
            'title' => 'Attendance & Gate Pass',
            'subtitle' => 'Daily check-in logs, tardy tracking, QR pickup passes, and safety alerts.',
            'icon' => 'bi bi-clock-history'
        ],
        'communication' => [
            'title' => 'School Communication',
            'subtitle' => 'Campus announcements, direct messaging, newsletters, and event notifications.',
            'icon' => 'bi bi-chat-dots-fill'
        ],
        'inventory' => [
            'title' => 'Material & Inventory',
            'subtitle' => 'Montessori physical material catalog, condition tracking, replenishment, and asset logs.',
            'icon' => 'bi bi-box-seam-fill'
        ],
        'fees' => [
            'title' => 'Student Fee Management',
            'subtitle' => 'Tuition structures, fee challans, payment vouchers, and ledger history.',
            'icon' => 'bi bi-receipt'
        ],
        'finance' => [
            'title' => 'School Finance',
            'subtitle' => 'Income/expense accounting, budget forecasting, and campus financial reporting.',
            'icon' => 'bi bi-wallet2'
        ],
        'staff' => [
            'title' => 'Staff & Guide Directory',
            'subtitle' => 'Teacher credentials, Montessori certifications, assignments, and personnel files.',
            'icon' => 'bi bi-person-badge-fill'
        ],
        'payroll' => [
            'title' => 'HR & Staff Payroll',
            'subtitle' => 'Salary structures, leave management, allowances, and monthly payroll processing.',
            'icon' => 'bi bi-cash-stack'
        ],
        'users' => [
            'title' => 'User Management & Roles',
            'subtitle' => 'Role-based access control (Superadmin, Principal, Teacher, Student, Parent).',
            'icon' => 'bi bi-shield-lock-fill'
        ],
        'campuses' => [
            'title' => 'Multi-Campus Directory',
            'subtitle' => 'Configure multiple school branches, location metadata, and campus directors.',
            'icon' => 'bi bi-buildings-fill'
        ],
        'logs' => [
            'title' => 'System Audit Logs',
            'subtitle' => 'Track administrative actions, data changes, security events, and session history.',
            'icon' => 'bi bi-list-check'
        ],
        'settings' => [
            'title' => 'System Settings',
            'subtitle' => 'General school settings, branding customization, email gateways, and system backup.',
            'icon' => 'bi bi-gear-fill'
        ]
    ];

    public function show($moduleKey)
    {
        $meta = $this->modules[$moduleKey] ?? [
            'title' => ucfirst($moduleKey),
            'subtitle' => 'Navigation foundation prepared.',
            'icon' => 'bi bi-grid'
        ];

        $user = auth()->user();
        $assignedStudents = collect();
        $assignedClassrooms = collect();
        $currentChild = null;

        if ($user && $user->role) {
            $roleName = $user->role->name;

            if ($roleName === 'teacher') {
                $assignedClassroomIds = \App\Helpers\AcademicScopeHelper::getAssignedClassroomIds($user);
                $assignedClassrooms = \App\Models\Classroom::whereIn('id', $assignedClassroomIds)->get();
                $assignedStudents = \App\Models\Student::whereIn('classroom_id', $assignedClassroomIds)->with('user')->get();
            } elseif ($roleName === 'parent') {
                $parentProfile = \App\Models\ParentProfile::where('user_id', $user->id)->first();
                $assignedStudents = $parentProfile ? $parentProfile->children()->with(['user', 'classroom'])->get() : collect();
                $currentChild = $assignedStudents->first();
                $assignedClassrooms = $assignedStudents->pluck('classroom')->filter();
            } elseif ($roleName === 'student') {
                $studentProfile = \App\Models\Student::where('user_id', $user->id)->with(['user', 'classroom'])->first();
                if ($studentProfile) {
                    $assignedStudents = collect([$studentProfile]);
                    $currentChild = $studentProfile;
                    if ($studentProfile->classroom) {
                        $assignedClassrooms = collect([$studentProfile->classroom]);
                    }
                }
            } else {
                $assignedClassrooms = \App\Models\Classroom::all();
                $assignedStudents = \App\Models\Student::with('user')->get();
            }
        }

        $meta['assignedStudents'] = $assignedStudents;
        $meta['assignedClassrooms'] = $assignedClassrooms;
        $meta['currentChild'] = $currentChild;

        if (view()->exists("modules.{$moduleKey}")) {
            return view("modules.{$moduleKey}", $meta);
        }

        return view('module-placeholder', $meta);
    }
}
