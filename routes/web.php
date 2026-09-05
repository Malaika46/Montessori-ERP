<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ModuleController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\TeacherController;
use App\Http\Controllers\ParentController;
use App\Http\Controllers\ClassroomController;
use App\Http\Controllers\CommunicationController;
use App\Http\Controllers\LmsController;
use App\Http\Controllers\AuditLogController;
use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\ObservationController;
use App\Http\Controllers\CurriculumController;
use App\Http\Controllers\LessonPlanController;
use App\Http\Controllers\AssessmentController;

/*
|--------------------------------------------------------------------------
| Web Routes - Montessori ERP Production Architecture
|--------------------------------------------------------------------------
*/

// ==========================================
// 1. PUBLIC ROUTES
// ==========================================
Route::get('/', [HomeController::class, 'index'])->name('landing');

// Guest Only Authentication Routes
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'handleLogin'])->name('auth.login.post');
    Route::get('/register', [AuthController::class, 'showRegister'])->name('auth.register');
    Route::post('/register', [AuthController::class, 'handleRegister'])->name('auth.register.post');
    Route::get('/forgot-password', [AuthController::class, 'showForgotPassword'])->name('auth.forgot-password');
    Route::post('/forgot-password', [AuthController::class, 'handleForgotPassword'])->name('auth.forgot-password.post');
    Route::get('/reset-password/{token?}', [AuthController::class, 'showResetPassword'])->name('auth.reset-password');
    Route::post('/reset-password', [AuthController::class, 'handleResetPassword'])->name('auth.reset-password.post');
});

// Alias for auth.login & home fallback
Route::get('/auth/login', fn() => redirect()->route('login'))->name('auth.login');
Route::get('/home', fn() => redirect()->route('dashboard'))->name('home');

// ==========================================
// 2. EMAIL VERIFICATION & AUTH ACTIONS
// ==========================================
Route::post('/email/verify-code', [AuthController::class, 'verifyCode'])->name('verification.code');

Route::middleware('auth')->group(function () {
    Route::get('/email/verify', [AuthController::class, 'showVerifyNotice'])->name('verification.notice');
    Route::get('/email/verify/{id}/{hash}', [AuthController::class, 'verifyEmail'])->middleware(['signed'])->name('verification.verify');
    Route::post('/email/verification-notification', [AuthController::class, 'resendVerification'])->middleware(['throttle:6,1'])->name('verification.send');
    Route::post('/logout', [AuthController::class, 'logout'])->name('auth.logout');
    Route::post('/change-password', [AuthController::class, 'changePassword'])->name('auth.change-password');
});

// ==========================================
// 3. PROTECTED ROLE DASHBOARDS
// ==========================================
Route::middleware(['auth', 'verified'])->group(function () {
    // Central dashboard router based on user database role
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Role-specific Dashboards protected by backend role middleware
    Route::get('/dashboard/superadmin', [DashboardController::class, 'superadmin'])->middleware('role:superadmin')->name('dashboard.superadmin');
    Route::get('/dashboard/principal', [DashboardController::class, 'principal'])->middleware('role:superadmin,principal')->name('dashboard.principal');
    Route::get('/dashboard/admin', [DashboardController::class, 'admin'])->middleware('role:superadmin,principal,admin')->name('dashboard.admin');
    Route::get('/dashboard/teacher', [DashboardController::class, 'teacher'])->middleware('role:superadmin,principal,admin,teacher')->name('dashboard.teacher');
    Route::get('/dashboard/student', [DashboardController::class, 'student'])->middleware('role:superadmin,student')->name('dashboard.student');
    Route::get('/dashboard/parent', [DashboardController::class, 'parent'])->middleware('role:superadmin,parent')->name('dashboard.parent');
});

// ==========================================
// 4. PROTECTED SUPERADMIN & CORE ERP MODULE ROUTES
// ==========================================
Route::middleware(['auth', 'verified'])->group(function () {

    // 1. STUDENTS MANAGEMENT (SUPERADMIN & AUTHORIZED STAFF & TEACHER & PARENT)
    Route::get('/students', [StudentController::class, 'index'])->middleware('role:superadmin,principal,admin,teacher,parent')->name('students.index');
    Route::post('/students', [StudentController::class, 'store'])->middleware('role:superadmin,principal,admin,teacher')->name('students.store');
    Route::get('/students/{student}', [StudentController::class, 'show'])->middleware('role:superadmin,principal,admin,teacher,parent')->name('students.show');
    Route::put('/students/{student}', [StudentController::class, 'update'])->middleware('role:superadmin,principal,admin,teacher')->name('students.update');
    Route::delete('/students/{student}', [StudentController::class, 'destroy'])->middleware('role:superadmin,principal,admin')->name('students.destroy');

    // 2. TEACHERS MANAGEMENT (SUPERADMIN & AUTHORIZED STAFF)
    Route::get('/teachers', [TeacherController::class, 'index'])->middleware('role:superadmin,principal,admin')->name('teachers.index');
    Route::post('/teachers', [TeacherController::class, 'store'])->middleware('role:superadmin,principal,admin')->name('teachers.store');
    Route::post('/teachers/{teacher}/assign-classrooms', [TeacherController::class, 'assignClassrooms'])->middleware('role:superadmin,principal,admin')->name('teachers.assign-classrooms');
    Route::get('/teachers/{teacher}', [TeacherController::class, 'show'])->middleware('role:superadmin,principal,admin')->name('teachers.show');
    Route::put('/teachers/{teacher}', [TeacherController::class, 'update'])->middleware('role:superadmin,principal,admin')->name('teachers.update');
    Route::delete('/teachers/{teacher}', [TeacherController::class, 'destroy'])->middleware('role:superadmin,principal,admin')->name('teachers.destroy');

    // 3. PARENTS MANAGEMENT (SUPERADMIN & AUTHORIZED STAFF & TEACHER)
    Route::get('/parents', [ParentController::class, 'index'])->middleware('role:superadmin,principal,admin,teacher')->name('parents.index');
    Route::post('/parents', [ParentController::class, 'store'])->middleware('role:superadmin,principal,admin,teacher')->name('parents.store');
    Route::post('/parents/{parent}/link-student', [ParentController::class, 'linkStudent'])->middleware('role:superadmin,principal,admin,teacher')->name('parents.link-student');
    Route::put('/parents/{parent}', [ParentController::class, 'update'])->middleware('role:superadmin,principal,admin,teacher')->name('parents.update');
    Route::delete('/parents/{parent}', [ParentController::class, 'destroy'])->middleware('role:superadmin,principal,admin')->name('parents.destroy');

    // 4. CLASSROOMS MANAGEMENT (SUPERADMIN & AUTHORIZED STAFF)
    Route::get('/classrooms', [ClassroomController::class, 'index'])->middleware('role:superadmin,principal,admin,teacher')->name('classrooms.index');
    Route::post('/classrooms', [ClassroomController::class, 'store'])->middleware('role:superadmin,principal,admin,teacher')->name('classrooms.store');
    Route::get('/classrooms/{classroom}', [ClassroomController::class, 'show'])->middleware('role:superadmin,principal,admin,teacher')->name('classrooms.show');
    Route::post('/classrooms/{classroom}/assign-teacher', [ClassroomController::class, 'assignTeacher'])->middleware('role:superadmin,principal,admin,teacher')->name('classrooms.assign-teacher');
    Route::post('/classrooms/{classroom}/enroll-student', [ClassroomController::class, 'enrollStudent'])->middleware('role:superadmin,principal,admin,teacher')->name('classrooms.enroll-student');
    Route::put('/classrooms/{classroom}', [ClassroomController::class, 'update'])->middleware('role:superadmin,principal,admin,teacher')->name('classrooms.update');
    Route::delete('/classrooms/{classroom}', [ClassroomController::class, 'destroy'])->middleware('role:superadmin,principal,admin,teacher')->name('classrooms.destroy');

    // 5. SCHOOL COMMUNICATION (SUPERADMIN MONITORING & BROADCAST)
    Route::get('/communication', [CommunicationController::class, 'index'])->middleware('role:superadmin,principal,admin,teacher,parent')->name('communication.index');
    Route::post('/communication', [CommunicationController::class, 'store'])->middleware('role:superadmin,principal,admin,teacher')->name('communication.store');
    Route::get('/communication/{communication}', [CommunicationController::class, 'show'])->middleware('role:superadmin,principal,admin,teacher,parent')->name('communication.show');

    // 6. GAMIFIED LMS (SUPERADMIN MONITORING & CONTENT MANAGEMENT)
    Route::get('/gamified-lms', [LmsController::class, 'index'])->middleware('role:superadmin,principal,admin,teacher,student')->name('lms.index');
    Route::post('/gamified-lms/paths', [LmsController::class, 'storePath'])->middleware('role:superadmin,principal,teacher')->name('lms.paths.store');
    Route::post('/gamified-lms/paths/{path}/activities', [LmsController::class, 'storeActivity'])->middleware('role:superadmin,principal,teacher')->name('lms.activities.store');
    Route::post('/gamified-lms/paths/{path}/toggle-publish', [LmsController::class, 'togglePublish'])->middleware('role:superadmin,principal,teacher')->name('lms.paths.toggle-publish');
    Route::delete('/gamified-lms/paths/{path}', [LmsController::class, 'destroyPath'])->middleware('role:superadmin,principal,teacher')->name('lms.paths.destroy');

    // 7. SYSTEM AUDIT LOGS
    Route::get('/audit-logs', [AuditLogController::class, 'index'])->middleware('role:superadmin,principal')->name('audit-logs.index');
    Route::get('/logs', fn() => redirect()->route('audit-logs.index'))->name('logs.index');

    // USER MANAGEMENT
    Route::get('/users', [UserController::class, 'index'])->middleware('role:superadmin,principal,admin')->name('users.index');
    Route::post('/users', [UserController::class, 'store'])->middleware('role:superadmin,principal,admin')->name('users.store');
    Route::post('/users/{user}/verify-now', [UserController::class, 'verifyNow'])->middleware('role:superadmin,principal,admin')->name('users.verify-now');
    Route::post('/users/{user}/resend-verification', [UserController::class, 'resendVerification'])->middleware('role:superadmin,principal,admin')->name('users.resend-verification');
    Route::delete('/users/{user}', [UserController::class, 'destroy'])->middleware('role:superadmin,principal,admin')->name('users.destroy');

    // DYNAMIC MODULE ROUTER
    Route::get('/modules/{moduleKey}', [ModuleController::class, 'show'])->name('modules.show');

    // MONTESSORI CURRICULUM MANAGEMENT
    Route::get('/curriculum', [CurriculumController::class, 'index'])->middleware('role:superadmin,principal,admin,teacher,student,parent')->name('curriculum.index');
    Route::post('/curriculum', [CurriculumController::class, 'store'])->middleware('role:superadmin,principal,admin,teacher')->name('curriculum.store');
    Route::put('/curriculum/{curriculumItem}', [CurriculumController::class, 'update'])->middleware('role:superadmin,principal,admin,teacher')->name('curriculum.update');
    Route::delete('/curriculum/{curriculumItem}', [CurriculumController::class, 'destroy'])->middleware('role:superadmin,principal,admin,teacher')->name('curriculum.destroy');
    // MONTESSORI LESSON PLANNING
    Route::get('/lesson-planning', [LessonPlanController::class, 'index'])->middleware('role:superadmin,principal,admin,teacher,student,parent')->name('lessons.index');
    Route::post('/lesson-planning', [LessonPlanController::class, 'store'])->middleware('role:superadmin,principal,admin,teacher')->name('lessons.store');
    Route::put('/lesson-planning/{lessonPlan}', [LessonPlanController::class, 'update'])->middleware('role:superadmin,principal,admin,teacher')->name('lessons.update');
    Route::delete('/lesson-planning/{lessonPlan}', [LessonPlanController::class, 'destroy'])->middleware('role:superadmin,principal,admin,teacher')->name('lessons.destroy');
    Route::get('/observations', [ObservationController::class, 'index'])->middleware('role:superadmin,principal,admin,teacher,student,parent')->name('observations.index');
    Route::post('/observations', [ObservationController::class, 'store'])->middleware('role:superadmin,principal,admin,teacher')->name('observations.store');
    // CHILD ASSESSMENT & EVALUATION CENTER
    Route::get('/assessments-reports', [AssessmentController::class, 'index'])->middleware('role:superadmin,principal,admin,teacher,student,parent')->name('assessments.index');
    Route::post('/assessments-reports', [AssessmentController::class, 'store'])->middleware('role:superadmin,principal,admin,teacher')->name('assessments.store');
    Route::put('/assessments-reports/{assessment}', [AssessmentController::class, 'update'])->middleware('role:superadmin,principal,admin,teacher')->name('assessments.update');
    Route::delete('/assessments-reports/{assessment}', [AssessmentController::class, 'destroy'])->middleware('role:superadmin,principal,admin,teacher')->name('assessments.destroy');
    Route::get('/assessments-reports/{assessment}/pdf', [AssessmentController::class, 'downloadPdf'])->middleware('role:superadmin,principal,admin,teacher,student,parent')->name('assessments.pdf');
    Route::get('/attendance', [AttendanceController::class, 'index'])->middleware('role:superadmin,principal,admin,teacher,student,parent')->name('attendance.index');
    Route::post('/attendance/batch', [AttendanceController::class, 'storeBatch'])->middleware('role:superadmin,principal,admin,teacher')->name('attendance.store-batch');
    Route::get('/inventory', fn() => app(ModuleController::class)->show('inventory'))->middleware('role:superadmin,principal,admin')->name('inventory.index');
    Route::get('/fees', fn() => app(ModuleController::class)->show('fees'))->middleware('role:superadmin,principal,admin,parent')->name('fees.index');
    Route::get('/finance', fn() => app(ModuleController::class)->show('finance'))->middleware('role:superadmin,principal,admin')->name('finance.index');
    Route::get('/hr', fn() => app(ModuleController::class)->show('staff'))->middleware('role:superadmin,principal,admin')->name('staff.index');
    Route::get('/campuses', fn() => app(ModuleController::class)->show('campuses'))->middleware('role:superadmin,principal')->name('campuses.index');
    Route::get('/settings', fn() => app(ModuleController::class)->show('settings'))->middleware('role:superadmin,principal,admin')->name('settings.index');
});
