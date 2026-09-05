<?php

namespace App\Http\Controllers;

use App\Models\Classroom;
use App\Models\Campus;
use App\Models\User;
use App\Models\Teacher;
use App\Models\Student;
use App\Models\AuditLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ClassroomController extends Controller
{
    /**
     * List all classrooms with real DB query.
     */
    public function index(Request $request)
    {
        $user = auth()->user();
        $query = Classroom::with(['campus', 'leadTeacher', 'students', 'teachers.user']);
        $students = Student::with('user')->get();

        if ($user && $user->role) {
            $roleName = $user->role->name;

            if ($roleName === 'teacher') {
                $assignedClassroomIds = \App\Helpers\AcademicScopeHelper::getAssignedClassroomIds($user);
                $query->whereIn('id', $assignedClassroomIds);
                $students = Student::whereIn('classroom_id', $assignedClassroomIds)->with('user')->get();
            } elseif ($roleName === 'parent') {
                $parentProfile = \App\Models\ParentProfile::where('user_id', $user->id)->first();
                $classIds = $parentProfile ? $parentProfile->children->pluck('classroom_id')->filter() : collect();
                $query->whereIn('id', $classIds);
                $students = $parentProfile ? $parentProfile->children()->with('user')->get() : collect();
            } elseif ($roleName === 'student') {
                $studentProfile = Student::where('user_id', $user->id)->first();
                $query->where('id', $studentProfile ? $studentProfile->classroom_id : 0);
            }
        }

        if ($request->filled('campus_id')) {
            $query->where('campus_id', $request->campus_id);
        }

        if ($request->filled('age_group')) {
            $query->where('age_group', $request->age_group);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $classrooms = $query->latest()->paginate(15);
        $campuses = Campus::all();
        $teachers = Teacher::with('user')->get();
        $totalClassrooms = $classrooms->total();

        return view('modules.classrooms', compact('classrooms', 'campuses', 'teachers', 'students', 'totalClassrooms'));
    }

    /**
     * Store new Classroom.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'code' => ['required', 'string', 'max:64', 'unique:classrooms,code'],
            'age_group' => ['required', 'string', 'max:128'],
            'campus_id' => ['nullable', 'exists:campuses,id'],
            'capacity' => ['required', 'integer', 'min:1'],
            'lead_teacher_id' => ['nullable', 'exists:users,id'],
            'description' => ['nullable', 'string'],
            'status' => ['required', 'string', 'in:active,inactive,archived'],
        ]);

        return DB::transaction(function () use ($validated) {
            $classroom = Classroom::create($validated);

            AuditLog::record('create_classroom', 'classrooms', 'Classroom', $classroom->id, [
                'name' => $classroom->name,
                'code' => $classroom->code,
                'age_group' => $classroom->age_group,
            ]);

            return back()->with('success', "Classroom '{$classroom->name}' ({$classroom->code}) created successfully.");
        });
    }

    /**
     * Show Classroom detail.
     */
    public function show(Classroom $classroom)
    {
        $classroom->load(['campus', 'leadTeacher', 'students.user', 'teachers.user', 'learningPaths']);
        return view('modules.classroom-detail', compact('classroom'));
    }

    /**
     * Update Classroom.
     */
    public function update(Request $request, Classroom $classroom)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'code' => ['required', 'string', 'max:64', 'unique:classrooms,code,' . $classroom->id],
            'age_group' => ['required', 'string', 'max:128'],
            'campus_id' => ['nullable', 'exists:campuses,id'],
            'capacity' => ['required', 'integer', 'min:1'],
            'lead_teacher_id' => ['nullable', 'exists:users,id'],
            'description' => ['nullable', 'string'],
            'status' => ['required', 'string', 'in:active,inactive,archived'],
        ]);

        return DB::transaction(function () use ($validated, $classroom) {
            $classroom->update($validated);

            AuditLog::record('update_classroom', 'classrooms', 'Classroom', $classroom->id, [
                'name' => $classroom->name,
                'status' => $classroom->status,
            ]);

            return back()->with('success', "Classroom '{$classroom->name}' updated successfully.");
        });
    }

    /**
     * Assign teacher to classroom.
     */
    public function assignTeacher(Request $request, Classroom $classroom)
    {
        $validated = $request->validate([
            'teacher_id' => ['required', 'exists:teachers,id'],
            'is_lead' => ['nullable', 'boolean'],
        ]);

        $teacher = Teacher::findOrFail($validated['teacher_id']);
        $teacher->classrooms()->syncWithoutDetaching([$classroom->id]);

        if (!empty($validated['is_lead'])) {
            $classroom->update(['lead_teacher_id' => $teacher->user_id]);
        }

        AuditLog::record('assign_teacher_classroom', 'classrooms', 'Classroom', $classroom->id, [
            'teacher_name' => $teacher->user->name,
            'classroom' => $classroom->name,
        ]);

        return back()->with('success', "Teacher '{$teacher->user->name}' assigned to '{$classroom->name}'.");
    }

    /**
     * Enroll student into classroom.
     */
    public function enrollStudent(Request $request, Classroom $classroom)
    {
        $validated = $request->validate([
            'student_id' => ['required', 'exists:students,id'],
        ]);

        $student = Student::findOrFail($validated['student_id']);
        $student->update(['classroom_id' => $classroom->id]);

        AuditLog::record('enroll_student_classroom', 'classrooms', 'Classroom', $classroom->id, [
            'student_name' => $student->user->name,
            'classroom' => $classroom->name,
        ]);

        return back()->with('success', "Student '{$student->user->name}' enrolled into '{$classroom->name}'.");
    }

    /**
     * Archive Classroom.
     */
    public function destroy(Classroom $classroom)
    {
        AuditLog::record('archive_classroom', 'classrooms', 'Classroom', $classroom->id, [
            'name' => $classroom->name,
        ]);

        $classroom->update(['status' => 'archived']);

        return back()->with('success', 'Classroom archived successfully.');
    }
}
