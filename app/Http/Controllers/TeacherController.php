<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Role;
use App\Models\Teacher;
use App\Models\Campus;
use App\Models\Classroom;
use App\Models\AuditLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class TeacherController extends Controller
{
    /**
     * List all teachers with real DB filters.
     */
    public function index(Request $request)
    {
        $query = Teacher::with(['user', 'campus', 'classrooms']);

        if ($request->filled('campus_id')) {
            $query->where('campus_id', $request->campus_id);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('specialization', 'like', "%{$search}%")
                  ->orWhereHas('user', function($uq) use ($search) {
                      $uq->where('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%");
                  });
            });
        }

        $teachers = $query->latest()->paginate(15);
        $campuses = Campus::all();
        $classrooms = Classroom::all();
        $totalTeachers = Teacher::count();

        return view('modules.teachers', compact('teachers', 'campuses', 'classrooms', 'totalTeachers'));
    }

    /**
     * Create new Teacher account and profile.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'first_name' => ['required', 'string', 'max:128'],
            'last_name' => ['required', 'string', 'max:128'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8'],
            'campus_id' => ['nullable', 'exists:campuses,id'],
            'specialization' => ['nullable', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'max:64'],
            'classroom_ids' => ['nullable', 'array'],
            'classroom_ids.*' => ['exists:classrooms,id'],
        ]);

        return DB::transaction(function () use ($validated) {
            $teacherRole = Role::where('name', 'teacher')->firstOrFail();

            // 1. Create User Account
            $user = User::create([
                'first_name' => $validated['first_name'],
                'last_name' => $validated['last_name'],
                'name' => "{$validated['first_name']} {$validated['last_name']}",
                'email' => $validated['email'],
                'password' => Hash::make($validated['password']),
                'role_id' => $teacherRole->id,
                'status' => 'unverified',
            ]);

            // 2. Create Teacher Profile
            $teacher = Teacher::create([
                'user_id' => $user->id,
                'campus_id' => $validated['campus_id'] ?? Campus::first()?->id,
                'specialization' => $validated['specialization'] ?? 'Montessori Guide',
                'phone' => $validated['phone'] ?? null,
                'status' => 'active',
            ]);

            // 3. Attach Authorized Classrooms
            if (!empty($validated['classroom_ids'])) {
                $teacher->classrooms()->sync($validated['classroom_ids']);
            }

            // 4. Dispatch Email Verification
            $user->sendEmailVerificationNotification();

            // 5. Audit Log
            AuditLog::record('create_teacher', 'teachers', 'Teacher', $teacher->id, [
                'name' => $user->name,
                'email' => $user->email,
            ]);

            return back()->with('success', "Teacher '{$user->name}' created successfully! Verification code & link sent to {$user->email}.");
        });
    }

    /**
     * Show Teacher details.
     */
    public function show(Teacher $teacher)
    {
        $teacher->load(['user', 'campus', 'classrooms']);
        return view('modules.teacher-detail', compact('teacher'));
    }

    /**
     * Update Teacher record.
     */
    public function update(Request $request, Teacher $teacher)
    {
        $validated = $request->validate([
            'first_name' => ['required', 'string', 'max:128'],
            'last_name' => ['required', 'string', 'max:128'],
            'campus_id' => ['nullable', 'exists:campuses,id'],
            'specialization' => ['nullable', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'max:64'],
            'status' => ['required', 'string', 'in:active,inactive,suspended,archived'],
            'classroom_ids' => ['nullable', 'array'],
        ]);

        return DB::transaction(function () use ($validated, $teacher) {
            $teacher->user->update([
                'first_name' => $validated['first_name'],
                'last_name' => $validated['last_name'],
                'name' => "{$validated['first_name']} {$validated['last_name']}",
            ]);

            $teacher->update([
                'campus_id' => $validated['campus_id'],
                'specialization' => $validated['specialization'],
                'phone' => $validated['phone'],
                'status' => $validated['status'],
            ]);

            if (isset($validated['classroom_ids'])) {
                $teacher->classrooms()->sync($validated['classroom_ids']);
            }

            AuditLog::record('update_teacher', 'teachers', 'Teacher', $teacher->id, [
                'name' => $teacher->user->name,
                'status' => $validated['status'],
            ]);

            return back()->with('success', "Teacher '{$teacher->user->name}' updated successfully.");
        });
    }

    /**
     * Assign Classrooms to Teacher.
     */
    public function assignClassrooms(Request $request, Teacher $teacher)
    {
        $validated = $request->validate([
            'classroom_ids' => ['required', 'array'],
            'classroom_ids.*' => ['exists:classrooms,id'],
        ]);

        $teacher->classrooms()->sync($validated['classroom_ids']);

        AuditLog::record('assign_classrooms', 'teachers', 'Teacher', $teacher->id, [
            'teacher' => $teacher->user->name,
            'classrooms_count' => count($validated['classroom_ids']),
        ]);

        return back()->with('success', "Classrooms successfully assigned to teacher '{$teacher->user->name}'!");
    }

    /**
     * Archive/Delete Teacher.
     */
    public function destroy(Teacher $teacher)
    {
        AuditLog::record('archive_teacher', 'teachers', 'Teacher', $teacher->id, [
            'name' => $teacher->user->name,
        ]);

        $teacher->update(['status' => 'archived']);
        $teacher->delete();

        return back()->with('success', 'Teacher record archived successfully.');
    }
}
