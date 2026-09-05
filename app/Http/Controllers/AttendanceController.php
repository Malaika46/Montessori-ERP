<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Attendance;
use App\Models\Student;
use App\Models\Classroom;
use App\Models\Teacher;
use App\Models\ParentProfile;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class AttendanceController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        $roleName = $user->role ? $user->role->name : 'guest';

        $assignedStudents = collect();
        $assignedClassrooms = collect();
        $attendances = collect();

        if (in_array($roleName, ['superadmin', 'principal', 'admin'])) {
            $assignedClassrooms = Classroom::all();
            $assignedStudents = Student::with(['user', 'classroom'])->where('status', 'active')->get();
            $attendances = Attendance::with(['student.user', 'classroom'])->latest('date')->paginate(35);
        } elseif ($roleName === 'teacher') {
            $assignedClassroomIds = \App\Helpers\AcademicScopeHelper::getAssignedClassroomIds($user);

            $assignedClassrooms = Classroom::whereIn('id', $assignedClassroomIds)->get();
            $assignedStudents = Student::with(['user', 'classroom'])
                ->whereIn('classroom_id', $assignedClassroomIds)
                ->where('status', 'active')
                ->get();

            $attendances = Attendance::with(['student.user', 'classroom'])
                ->whereIn('classroom_id', $assignedClassroomIds)
                ->latest('date')
                ->paginate(35);
        } elseif ($roleName === 'student') {
            $studentProfile = Student::where('user_id', $user->id)->first();
            if ($studentProfile) {
                $assignedStudents = collect([$studentProfile]);
                $attendances = Attendance::with(['student.user', 'classroom'])
                    ->where('student_id', $studentProfile->id)
                    ->latest('date')
                    ->paginate(35);
            }
        } elseif ($roleName === 'parent') {
            $parentProfile = ParentProfile::where('user_id', $user->id)->first();
            $childIds = $parentProfile ? $parentProfile->children->pluck('id') : collect();
            $assignedStudents = Student::with(['user', 'classroom'])->whereIn('id', $childIds)->get();
            $attendances = Attendance::with(['student.user', 'classroom'])
                ->whereIn('student_id', $childIds)
                ->latest('date')
                ->paginate(35);
        }

        return view('modules.attendance', compact('assignedStudents', 'assignedClassrooms', 'attendances', 'roleName'));
    }

    public function storeBatch(Request $request)
    {
        $user = Auth::user();
        $roleName = $user->role ? $user->role->name : 'guest';

        if (!in_array($roleName, ['superadmin', 'principal', 'admin', 'teacher'])) {
            abort(403, 'Unauthorized to mark attendance.');
        }

        $validated = $request->validate([
            'date' => ['required', 'date'],
            'classroom_id' => ['nullable', 'exists:classrooms,id'],
            'attendance' => ['required', 'array'],
            'attendance.*.student_id' => ['required', 'exists:students,id'],
            'attendance.*.status' => ['required', 'in:present,absent,tardy,excused'],
            'attendance.*.remarks' => ['nullable', 'string', 'max:255'],
        ]);

        if ($roleName === 'teacher') {
            $teacher = Teacher::where('user_id', $user->id)->first();
            $classIdsFromPivot = $teacher ? $teacher->classrooms->pluck('id') : collect();
            $classIdsFromLead = Classroom::where('lead_teacher_id', $user->id)->pluck('id');
            $assignedClassroomIds = $classIdsFromPivot->merge($classIdsFromLead)->unique();

            foreach ($validated['attendance'] as $item) {
                $student = Student::find($item['student_id']);
                if (!$student || !$assignedClassroomIds->contains($student->classroom_id)) {
                    abort(403, "Unauthorized attendance entry for student {$item['student_id']}.");
                }
            }
        }

        DB::transaction(function () use ($validated, $user) {
            foreach ($validated['attendance'] as $item) {
                $student = Student::find($item['student_id']);
                Attendance::updateOrCreate(
                    [
                        'student_id' => $item['student_id'],
                        'date' => $validated['date'],
                    ],
                    [
                        'classroom_id' => $student->classroom_id,
                        'recorded_by_user_id' => $user->id,
                        'status' => strtolower($item['status']),
                        'remarks' => $item['remarks'] ?? null,
                    ]
                );
            }
        });

        return back()->with('success', 'Classroom attendance updated successfully!');
    }
}
