<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Observation;
use App\Models\Student;
use App\Models\Teacher;
use App\Models\ParentProfile;
use App\Models\Classroom;
use Illuminate\Support\Facades\Auth;

class ObservationController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        $roleName = $user->role ? $user->role->name : 'guest';

        $assignedStudents = collect();
        $assignedClassrooms = collect();
        $query = Observation::with(['student.user', 'teacher.user', 'classroom']);

        if (in_array($roleName, ['superadmin', 'principal', 'admin'])) {
            $assignedClassrooms = Classroom::all();
            $assignedStudents = Student::with(['user', 'classroom'])->where('status', 'active')->get();
        } elseif ($roleName === 'teacher') {
            $assignedClassroomIds = \App\Helpers\AcademicScopeHelper::getAssignedClassroomIds($user);

            $assignedClassrooms = Classroom::whereIn('id', $assignedClassroomIds)->get();
            $assignedStudents = Student::with(['user', 'classroom'])
                ->whereIn('classroom_id', $assignedClassroomIds)
                ->where('status', 'active')
                ->get();

            $query->whereIn('classroom_id', $assignedClassroomIds);
        } elseif ($roleName === 'student') {
            $studentProfile = Student::where('user_id', $user->id)->first();
            if ($studentProfile) {
                $assignedStudents = collect([$studentProfile]);
                $query->where('student_id', $studentProfile->id);
            }
        } elseif ($roleName === 'parent') {
            $parentProfile = ParentProfile::where('user_id', $user->id)->first();
            $childIds = $parentProfile ? $parentProfile->children->pluck('id') : collect();
            $assignedStudents = Student::with(['user', 'classroom'])->whereIn('id', $childIds)->get();
            $query->whereIn('student_id', $childIds)->where('is_family_visible', true);
        }

        $dbObservations = $query->latest('observed_at')->paginate(20);

        return view('modules.observations', compact('assignedStudents', 'assignedClassrooms', 'dbObservations', 'roleName'));
    }

    public function store(Request $request)
    {
        $user = Auth::user();
        $roleName = $user->role ? $user->role->name : 'guest';

        if (!in_array($roleName, ['superadmin', 'principal', 'admin', 'teacher'])) {
            abort(403, 'Unauthorized to add observation.');
        }

        $validated = $request->validate([
            'student_id' => ['required', 'exists:students,id'],
            'title' => ['required', 'string', 'max:255'],
            'avenue' => ['required', 'string'],
            'notes' => ['required', 'string'],
            'mastery_level' => ['required', 'in:Introduced,Working,Mastered'],
            'observed_at' => ['required', 'date'],
            'is_family_visible' => ['nullable'],
        ]);

        $student = Student::findOrFail($validated['student_id']);
        $teacher = Teacher::where('user_id', $user->id)->first();

        if ($roleName === 'teacher') {
            $classIdsFromPivot = $teacher ? $teacher->classrooms->pluck('id') : collect();
            $classIdsFromLead = Classroom::where('lead_teacher_id', $user->id)->pluck('id');
            $assignedClassroomIds = $classIdsFromPivot->merge($classIdsFromLead)->unique();

            if (!$assignedClassroomIds->contains($student->classroom_id)) {
                abort(403, 'Unauthorized to add observation for a student outside your assigned classroom.');
            }
        }

        Observation::create([
            'student_id' => $student->id,
            'teacher_id' => $teacher?->id,
            'classroom_id' => $student->classroom_id,
            'title' => $validated['title'],
            'avenue' => $validated['avenue'],
            'notes' => $validated['notes'],
            'mastery_level' => $validated['mastery_level'],
            'observed_at' => $validated['observed_at'],
            'is_family_visible' => $request->has('is_family_visible') || $request->input('is_family_visible') == '1',
        ]);

        return back()->with('success', 'Montessori observation note recorded and published successfully!');
    }
}
