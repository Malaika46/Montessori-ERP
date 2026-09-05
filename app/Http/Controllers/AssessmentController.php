<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Assessment;
use App\Models\Student;
use App\Models\Classroom;
use App\Models\Teacher;
use App\Models\AuditLog;
use App\Helpers\AcademicScopeHelper;

class AssessmentController extends Controller
{
    /**
     * Display Child Assessment & Evaluation Center.
     */
    public function index(Request $request)
    {
        $user = auth()->user();
        $query = Assessment::with(['student.user', 'classroom', 'teacher.user']);

        $students = collect();
        $classrooms = collect();

        if ($user && $user->role) {
            $roleName = $user->role->name;

            if ($roleName === 'teacher') {
                $assignedClassroomIds = AcademicScopeHelper::getAssignedClassroomIds($user);
                $studentIds = AcademicScopeHelper::getAuthorizedStudentIds($user);

                $query->whereIn('student_id', $studentIds);
                $students = Student::with('user')->whereIn('id', $studentIds)->get();
                $classrooms = Classroom::whereIn('id', $assignedClassroomIds)->get();
            } elseif ($roleName === 'parent') {
                $childIds = AcademicScopeHelper::getAuthorizedStudentIds($user);
                $query->whereIn('student_id', $childIds);
                $students = Student::with('user')->whereIn('id', $childIds)->get();
                $classrooms = Classroom::whereIn('id', $students->pluck('classroom_id')->filter())->get();
            } elseif ($roleName === 'student') {
                $studentProfile = Student::where('user_id', $user->id)->first();
                $studentId = $studentProfile ? $studentProfile->id : 0;
                $query->where('student_id', $studentId);
                $students = Student::with('user')->where('id', $studentId)->get();
            } else {
                $students = Student::with('user')->get();
                $classrooms = Classroom::all();
            }
        }

        // Auto-seed default assessment report cards for students if table is empty
        if (Assessment::count() === 0) {
            $seedStudents = Student::with(['classroom', 'user'])->limit(5)->get();
            $teacher = Teacher::first();

            foreach ($seedStudents as $st) {
                Assessment::create([
                    'student_id' => $st->id,
                    'classroom_id' => $st->classroom_id,
                    'teacher_id' => $teacher ? $teacher->id : null,
                    'title' => '2026 First Term Montessori Narrative Progress Card',
                    'evaluation_period' => 'Jan 2026 - Jun 2026',
                    'term_name' => 'Term 1',
                    'practical_life_score' => 95,
                    'practical_life_status' => 'Mastered',
                    'sensorial_score' => 90,
                    'sensorial_status' => 'Mastered',
                    'mathematics_score' => 82,
                    'mathematics_status' => 'Working',
                    'language_score' => 85,
                    'language_status' => 'Introduced',
                    'cultural_score' => 88,
                    'cultural_status' => 'Mastered',
                    'overall_score' => 88,
                    'overall_summary' => "{$st->first_name} has shown remarkable concentration and graceful social integration throughout Term 1. Demonstrates strong independent decision-making during the work cycle.",
                    'status' => 'released',
                    'published_at' => '2026-07-05',
                ]);
            }
        }

        // Filters
        if ($request->filled('student_id')) {
            $query->where('student_id', $request->student_id);
        }

        if ($request->filled('classroom_id')) {
            $query->where('classroom_id', $request->classroom_id);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $assessments = $query->orderBy('created_at', 'desc')->paginate(10);

        // Stats calculation
        $releasedCount = Assessment::where('status', 'released')->count();
        $avgScore = round(Assessment::avg('overall_score') ?? 88);

        return view('modules.assessments', compact('assessments', 'students', 'classrooms', 'releasedCount', 'avgScore'));
    }

    /**
     * Store new Assessment Report Card.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'student_id' => ['required', 'exists:students,id'],
            'title' => ['required', 'string', 'max:255'],
            'evaluation_period' => ['required', 'string', 'max:100'],
            'term_name' => ['required', 'string', 'max:100'],
            'practical_life_score' => ['required', 'integer', 'min:0', 'max:100'],
            'practical_life_status' => ['required', 'string', 'in:Introduced,Working,Mastered'],
            'sensorial_score' => ['required', 'integer', 'min:0', 'max:100'],
            'sensorial_status' => ['required', 'string', 'in:Introduced,Working,Mastered'],
            'mathematics_score' => ['required', 'integer', 'min:0', 'max:100'],
            'mathematics_status' => ['required', 'string', 'in:Introduced,Working,Mastered'],
            'language_score' => ['required', 'integer', 'min:0', 'max:100'],
            'language_status' => ['required', 'string', 'in:Introduced,Working,Mastered'],
            'cultural_score' => ['required', 'integer', 'min:0', 'max:100'],
            'cultural_status' => ['required', 'string', 'in:Introduced,Working,Mastered'],
            'overall_summary' => ['required', 'string'],
            'status' => ['required', 'string', 'in:draft,review,released'],
        ]);

        $student = Student::findOrFail($validated['student_id']);
        $validated['classroom_id'] = $student->classroom_id;

        $user = auth()->user();
        if ($user && $user->role && $user->role->name === 'teacher') {
            $teacher = Teacher::where('user_id', $user->id)->first();
            $validated['teacher_id'] = $teacher ? $teacher->id : null;
        }

        // Average score calculation
        $scores = [
            $validated['practical_life_score'],
            $validated['sensorial_score'],
            $validated['mathematics_score'],
            $validated['language_score'],
            $validated['cultural_score'],
        ];
        $validated['overall_score'] = round(array_sum($scores) / count($scores));

        if ($validated['status'] === 'released') {
            $validated['published_at'] = now()->format('Y-m-d');
        }

        $assessment = Assessment::create($validated);

        AuditLog::record('created_assessment', 'assessments', 'Assessment', $assessment->id, [
            'student_id' => $assessment->student_id,
            'title' => $assessment->title,
        ]);

        return redirect()->route('assessments.index')->with('success', "Assessment Report Card created successfully for {$student->first_name}!");
    }

    /**
     * Update existing Assessment.
     */
    public function update(Request $request, Assessment $assessment)
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'evaluation_period' => ['required', 'string', 'max:100'],
            'term_name' => ['required', 'string', 'max:100'],
            'practical_life_score' => ['required', 'integer', 'min:0', 'max:100'],
            'practical_life_status' => ['required', 'string', 'in:Introduced,Working,Mastered'],
            'sensorial_score' => ['required', 'integer', 'min:0', 'max:100'],
            'sensorial_status' => ['required', 'string', 'in:Introduced,Working,Mastered'],
            'mathematics_score' => ['required', 'integer', 'min:0', 'max:100'],
            'mathematics_status' => ['required', 'string', 'in:Introduced,Working,Mastered'],
            'language_score' => ['required', 'integer', 'min:0', 'max:100'],
            'language_status' => ['required', 'string', 'in:Introduced,Working,Mastered'],
            'cultural_score' => ['required', 'integer', 'min:0', 'max:100'],
            'cultural_status' => ['required', 'string', 'in:Introduced,Working,Mastered'],
            'overall_summary' => ['required', 'string'],
            'status' => ['required', 'string', 'in:draft,review,released'],
        ]);

        $scores = [
            $validated['practical_life_score'],
            $validated['sensorial_score'],
            $validated['mathematics_score'],
            $validated['language_score'],
            $validated['cultural_score'],
        ];
        $validated['overall_score'] = round(array_sum($scores) / count($scores));

        if ($validated['status'] === 'released' && !$assessment->published_at) {
            $validated['published_at'] = now()->format('Y-m-d');
        }

        $assessment->update($validated);

        AuditLog::record('updated_assessment', 'assessments', 'Assessment', $assessment->id, [
            'student_id' => $assessment->student_id,
            'title' => $assessment->title,
        ]);

        return redirect()->route('assessments.index')->with('success', 'Assessment Report Card updated successfully!');
    }

    /**
     * Delete Assessment.
     */
    public function destroy(Assessment $assessment)
    {
        $title = $assessment->title;
        $assessment->delete();

        AuditLog::record('deleted_assessment', 'assessments', 'Assessment', $assessment->id, [
            'title' => $title,
        ]);

        return redirect()->route('assessments.index')->with('success', 'Assessment Report Card deleted successfully!');
    }

    /**
     * Download or Print PDF Report Card.
     */
    public function downloadPdf(Assessment $assessment)
    {
        $assessment->load(['student.user', 'student.parents.user', 'classroom', 'teacher.user']);

        return view('modules.pdf-report-card', compact('assessment'));
    }
}
