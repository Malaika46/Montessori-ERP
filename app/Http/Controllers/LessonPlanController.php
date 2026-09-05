<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\LessonPlan;
use App\Models\Classroom;
use App\Models\AuditLog;
use App\Helpers\AcademicScopeHelper;

class LessonPlanController extends Controller
{
    /**
     * Display Montessori Lesson Planning & Scheduling.
     */
    public function index(Request $request)
    {
        $user = auth()->user();
        $query = LessonPlan::with(['classroom', 'teacher']);

        $classrooms = collect();
        if ($user && $user->role) {
            $roleName = $user->role->name;

            if ($roleName === 'teacher') {
                $assignedClassroomIds = AcademicScopeHelper::getAssignedClassroomIds($user);
                $query->whereIn('classroom_id', $assignedClassroomIds);
                $classrooms = Classroom::whereIn('id', $assignedClassroomIds)->get();
            } elseif ($roleName === 'parent') {
                $childIds = AcademicScopeHelper::getAuthorizedStudentIds($user);
                $classroomIds = \App\Models\Student::whereIn('id', $childIds)->pluck('classroom_id')->filter()->toArray();
                $query->whereIn('classroom_id', $classroomIds);
                $classrooms = Classroom::whereIn('id', $classroomIds)->get();
            } elseif ($roleName === 'student') {
                $studentProfile = \App\Models\Student::where('user_id', $user->id)->first();
                $classId = $studentProfile ? $studentProfile->classroom_id : null;
                if ($classId) {
                    $query->where('classroom_id', $classId);
                    $classrooms = Classroom::where('id', $classId)->get();
                } else {
                    $query->whereRaw('1 = 0');
                }
            } else {
                $classrooms = Classroom::all();
            }
        }

        // Seed default lesson plans if none exist for classrooms
        if (LessonPlan::count() === 0) {
            $allClasses = Classroom::all();
            $teacher = \App\Models\Teacher::first();
            foreach ($allClasses as $c) {
                $defaultLessons = [
                    [
                        'classroom_id' => $c->id,
                        'teacher_id' => $teacher ? $teacher->id : null,
                        'title' => 'Introduction to Cylinder Blocks (Block 1)',
                        'avenue' => 'Sensorial',
                        'description' => 'Individual presentation on visual height and diameter ordering.',
                        'materials_needed' => 'Cylinder Block Set 1 (Knobbed Cylinders)',
                        'scheduled_date' => now()->format('Y-m-d'),
                        'status' => 'planned',
                    ],
                    [
                        'classroom_id' => $c->id,
                        'teacher_id' => $teacher ? $teacher->id : null,
                        'title' => 'Sandpaper Letter Tracing ("m", "a", "s")',
                        'avenue' => 'Language',
                        'description' => 'Three-period lesson for tactile sound association and muscular preparation for writing.',
                        'materials_needed' => 'Sandpaper Letters, Felt Mat',
                        'scheduled_date' => now()->addDays(1)->format('Y-m-d'),
                        'status' => 'planned',
                    ],
                    [
                        'classroom_id' => $c->id,
                        'teacher_id' => $teacher ? $teacher->id : null,
                        'title' => 'Spindle Box (Concept of Zero & Quantities 0-9)',
                        'avenue' => 'Mathematics',
                        'description' => 'Loose quantity counting and introducing zero as an empty compartment.',
                        'materials_needed' => 'Spindle Box, 45 Wooden Spindles, Ribbon Bands',
                        'scheduled_date' => now()->addDays(2)->format('Y-m-d'),
                        'status' => 'planned',
                    ]
                ];
                foreach ($defaultLessons as $dLesson) {
                    LessonPlan::create($dLesson);
                }
            }
        }

        // Search & Filters
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%")
                  ->orWhere('materials_needed', 'like', "%{$search}%");
            });
        }

        if ($request->filled('avenue')) {
            $query->where('avenue', $request->avenue);
        }

        if ($request->filled('classroom_id')) {
            $query->where('classroom_id', $request->classroom_id);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $lessonPlans = $query->orderBy('scheduled_date', 'asc')->paginate(12);

        $avenues = ['Practical Life', 'Sensorial', 'Mathematics', 'Language', 'Cultural'];
        $avenueCounts = [];
        foreach ($avenues as $av) {
            $avenueCounts[$av] = LessonPlan::where('avenue', $av)->count();
        }

        return view('modules.lessons', compact('lessonPlans', 'classrooms', 'avenueCounts'));
    }

    /**
     * Store new Lesson Plan.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'classroom_id' => ['required', 'exists:classrooms,id'],
            'title' => ['required', 'string', 'max:255'],
            'avenue' => ['required', 'string', 'in:Practical Life,Sensorial,Mathematics,Language,Cultural'],
            'description' => ['nullable', 'string'],
            'materials_needed' => ['nullable', 'string'],
            'scheduled_date' => ['required', 'date'],
            'status' => ['required', 'string', 'in:planned,presented,completed'],
        ]);

        $user = auth()->user();
        if ($user && $user->role && $user->role->name === 'teacher') {
            $teacher = \App\Models\Teacher::where('user_id', $user->id)->first();
            $validated['teacher_id'] = $teacher ? $teacher->id : null;
        }

        $plan = LessonPlan::create($validated);

        AuditLog::record('created_lesson_plan', 'lessons', 'LessonPlan', $plan->id, [
            'title' => $plan->title,
            'scheduled_date' => $plan->scheduled_date,
        ]);

        return redirect()->route('lessons.index')->with('success', 'Lesson Plan created successfully!');
    }

    /**
     * Update existing Lesson Plan.
     */
    public function update(Request $request, LessonPlan $lessonPlan)
    {
        $validated = $request->validate([
            'classroom_id' => ['required', 'exists:classrooms,id'],
            'title' => ['required', 'string', 'max:255'],
            'avenue' => ['required', 'string', 'in:Practical Life,Sensorial,Mathematics,Language,Cultural'],
            'description' => ['nullable', 'string'],
            'materials_needed' => ['nullable', 'string'],
            'scheduled_date' => ['required', 'date'],
            'status' => ['required', 'string', 'in:planned,presented,completed'],
        ]);

        $lessonPlan->update($validated);

        AuditLog::record('updated_lesson_plan', 'lessons', 'LessonPlan', $lessonPlan->id, [
            'title' => $lessonPlan->title,
        ]);

        return redirect()->route('lessons.index')->with('success', 'Lesson Plan updated successfully!');
    }

    /**
     * Delete Lesson Plan.
     */
    public function destroy(LessonPlan $lessonPlan)
    {
        $title = $lessonPlan->title;
        $lessonPlan->delete();

        AuditLog::record('deleted_lesson_plan', 'lessons', 'LessonPlan', $lessonPlan->id, [
            'title' => $title,
        ]);

        return redirect()->route('lessons.index')->with('success', 'Lesson Plan deleted successfully!');
    }
}
