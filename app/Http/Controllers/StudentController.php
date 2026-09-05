<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Role;
use App\Models\Student;
use App\Models\Campus;
use App\Models\Classroom;
use App\Models\AuditLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class StudentController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();

        // Automatically ensure all users with 'student' role have a Student profile record
        $studentRoleId = \App\Models\Role::where('name', 'student')->value('id');
        if ($studentRoleId) {
            $studentUsersWithoutProfile = \App\Models\User::where('role_id', $studentRoleId)
                ->whereDoesntHave('studentProfile')
                ->get();
            foreach ($studentUsersWithoutProfile as $sUser) {
                Student::firstOrCreate([
                    'user_id' => $sUser->id,
                ], [
                    'campus_id' => $sUser->campus_id ?? 1,
                    'student_number' => 'STU-' . str_pad($sUser->id, 5, '0', STR_PAD_LEFT),
                    'status' => 'active',
                ]);
            }
        }

        $query = Student::with(['user', 'campus', 'classroom', 'parents.user']);
        $classrooms = Classroom::all();

        if ($user && $user->role) {
            $roleName = $user->role->name;

            if ($roleName === 'teacher') {
                $assignedClassroomIds = \App\Helpers\AcademicScopeHelper::getAssignedClassroomIds($user);
                $query->whereIn('classroom_id', $assignedClassroomIds);
                $classrooms = Classroom::whereIn('id', $assignedClassroomIds)->get();
            } elseif ($roleName === 'parent') {
                $childIds = \App\Helpers\AcademicScopeHelper::getAuthorizedStudentIds($user);
                $query->whereIn('id', $childIds);
            } elseif ($roleName === 'student') {
                $studentProfile = Student::where('user_id', $user->id)->first();
                $query->where('id', $studentProfile ? $studentProfile->id : 0);
            }
        }

        if ($request->filled('campus_id')) {
            $query->where('campus_id', $request->campus_id);
        }

        if ($request->filled('classroom_id')) {
            $query->where('classroom_id', $request->classroom_id);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('student_number', 'like', "%{$search}%")
                  ->orWhereHas('user', function($uq) use ($search) {
                      $uq->where('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%");
                  });
            });
        }

        $students = $query->latest()->paginate(15);
        $campuses = Campus::all();
        $totalStudents = $students->total();
        $activeStudents = (clone $query)->where('status', 'active')->count();

        return view('modules.students', compact('students', 'campuses', 'classrooms', 'totalStudents', 'activeStudents'));
    }

    /**
     * Create new Student account and profile.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'first_name' => ['required', 'string', 'max:128'],
            'last_name' => ['required', 'string', 'max:128'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8'],
            'campus_id' => ['nullable', 'exists:campuses,id'],
            'classroom_id' => ['nullable', 'exists:classrooms,id'],
            'date_of_birth' => ['nullable', 'date'],
            'gender' => ['nullable', 'string', 'in:Male,Female,Other'],
        ]);

        return DB::transaction(function () use ($validated, $request) {
            $studentRole = Role::where('name', 'student')->firstOrFail();

            // 1. Create User account (Students do not require email verification)
            $user = User::create([
                'first_name' => $validated['first_name'],
                'last_name' => $validated['last_name'],
                'name' => "{$validated['first_name']} {$validated['last_name']}",
                'email' => $validated['email'],
                'password' => Hash::make($validated['password']),
                'role_id' => $studentRole->id,
                'status' => 'active',
                'email_verified_at' => now(),
            ]);

            // 2. Generate Student Number
            $studentNumber = 'STU-' . str_pad((string)($user->id), 5, '0', STR_PAD_LEFT);

            // 3. Create Student Profile
            $student = Student::create([
                'user_id' => $user->id,
                'campus_id' => $validated['campus_id'] ?? Campus::first()?->id,
                'classroom_id' => $validated['classroom_id'] ?? null,
                'student_number' => $studentNumber,
                'date_of_birth' => $validated['date_of_birth'] ?? null,
                'gender' => $validated['gender'] ?? 'Male',
                'status' => 'active',
            ]);

            // 4. Record Audit Log
            AuditLog::record('create_student', 'students', 'Student', $student->id, [
                'name' => $user->name,
                'email' => $user->email,
                'student_number' => $studentNumber,
            ]);

            return back()->with('success', "Student '{$user->name}' ({$studentNumber}) created successfully! Account is active and ready for login.");
        });
    }

    /**
     * Show Student profile and complete authorized info.
     */
    public function show(Student $student)
    {
        $user = auth()->user();
        if ($user && $user->role) {
            $roleName = $user->role->name;

            if ($roleName === 'teacher') {
                $assignedClassroomIds = \App\Helpers\AcademicScopeHelper::getAssignedClassroomIds($user);

                if (!$assignedClassroomIds->contains($student->classroom_id)) {
                    abort(403, 'Unauthorized access to student outside your assigned classroom.');
                }
            } elseif ($roleName === 'parent') {
                $parentProfile = \App\Models\ParentProfile::where('user_id', $user->id)->first();
                $childIds = $parentProfile ? $parentProfile->children->pluck('id') : collect();
                if (!$childIds->contains($student->id)) {
                    abort(403, 'Unauthorized access to student record.');
                }
            } elseif ($roleName === 'student') {
                $studentProfile = Student::where('user_id', $user->id)->first();
                if ($studentProfile && $studentProfile->id !== $student->id) {
                    abort(403, 'Unauthorized access to another student record.');
                }
            }
        }

        $student->load(['user', 'campus', 'classroom', 'parents.user', 'lmsProgress.activity', 'lmsRewards']);
        return view('modules.student-detail', compact('student'));
    }

    /**
     * Update Student details and status.
     */
    public function update(Request $request, Student $student)
    {
        $validated = $request->validate([
            'first_name' => ['required', 'string', 'max:128'],
            'last_name' => ['required', 'string', 'max:128'],
            'campus_id' => ['nullable', 'exists:campuses,id'],
            'classroom_id' => ['nullable', 'exists:classrooms,id'],
            'status' => ['required', 'string', 'in:active,inactive,suspended,archived'],
        ]);

        return DB::transaction(function () use ($validated, $student) {
            $student->user->update([
                'first_name' => $validated['first_name'],
                'last_name' => $validated['last_name'],
                'name' => "{$validated['first_name']} {$validated['last_name']}",
            ]);

            $student->update([
                'campus_id' => $validated['campus_id'],
                'classroom_id' => $validated['classroom_id'],
                'status' => $validated['status'],
            ]);

            AuditLog::record('update_student', 'students', 'Student', $student->id, [
                'name' => $student->user->name,
                'status' => $validated['status'],
            ]);

            return back()->with('success', "Student record for '{$student->user->name}' updated successfully.");
        });
    }

    /**
     * Delete/Archive Student.
     */
    public function destroy(Student $student)
    {
        AuditLog::record('archive_student', 'students', 'Student', $student->id, [
            'name' => $student->user->name,
        ]);

        $student->update(['status' => 'archived']);
        $student->delete();

        return back()->with('success', 'Student record archived successfully.');
    }
}
