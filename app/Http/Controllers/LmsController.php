<?php

namespace App\Http\Controllers;

use App\Models\LmsLearningPath;
use App\Models\LmsActivity;
use App\Models\LmsStudentProgress;
use App\Models\LmsStudentReward;
use App\Models\Classroom;
use App\Models\Student;
use App\Models\AuditLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class LmsController extends Controller
{
    /**
     * Display Gamified LMS with organization-wide Superadmin monitoring access.
     */
    public function index(Request $request)
    {
        $user = auth()->user();
        $roleName = $user && $user->role ? $user->role->name : 'guest';

        $query = LmsLearningPath::with(['teacher', 'classroom', 'activities.studentProgress']);

        if ($roleName === 'teacher') {
            $assignedClassroomIds = \App\Helpers\AcademicScopeHelper::getAssignedClassroomIds($user);
            $query->where(function($q) use ($assignedClassroomIds) {
                $q->whereIn('classroom_id', $assignedClassroomIds)
                  ->orWhereNull('classroom_id');
            });
        } elseif ($roleName === 'student') {
            $studentProfile = Student::where('user_id', $user->id)->first();
            $classId = $studentProfile ? $studentProfile->classroom_id : 0;
            $query->where('is_published', true)->where(function($q) use ($classId) {
                $q->where('classroom_id', $classId)->orWhereNull('classroom_id');
            });
        } elseif ($roleName === 'parent') {
            $parentProfile = \App\Models\ParentProfile::where('user_id', $user->id)->first();
            $childClassIds = $parentProfile ? $parentProfile->children->pluck('classroom_id')->filter() : collect();
            $query->where('is_published', true)->where(function($q) use ($childClassIds) {
                $q->whereIn('classroom_id', $childClassIds)->orWhereNull('classroom_id');
            });
        }

        if ($request->filled('montessori_domain')) {
            $query->where('montessori_domain', $request->montessori_domain);
        }

        if ($request->filled('classroom_id')) {
            $query->where('classroom_id', $request->classroom_id);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $learningPaths = $query->latest()->paginate(15);
        $classrooms = \App\Helpers\AcademicScopeHelper::getAssignedClassroomIds($user);
        $classrooms = Classroom::whereIn('id', $classrooms)->get();
        $totalPaths = LmsLearningPath::count();
        $totalActivities = LmsActivity::count();
        $totalXpGranted = LmsStudentProgress::sum('xp_earned');

        return view('modules.lms', compact('learningPaths', 'classrooms', 'totalPaths', 'totalActivities', 'totalXpGranted'));
    }

    /**
     * Create new LMS Learning Path (Teacher or Superadmin).
     */
    public function storePath(Request $request)
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'montessori_domain' => ['required', 'string', 'in:Practical Life,Sensorial,Language,Mathematics,Cultural'],
            'classroom_id' => ['nullable', 'exists:classrooms,id'],
        ]);

        return DB::transaction(function () use ($validated) {
            $user = auth()->user();

            $path = LmsLearningPath::create([
                'teacher_id' => $user->id,
                'campus_id' => $user->campus_id ?? null,
                'classroom_id' => $validated['classroom_id'] ?? null,
                'title' => $validated['title'],
                'description' => $validated['description'] ?? null,
                'montessori_domain' => $validated['montessori_domain'],
                'is_published' => true,
                'status' => 'active',
            ]);

            AuditLog::record('create_lms_path', 'lms', 'LmsLearningPath', $path->id, [
                'title' => $path->title,
                'domain' => $path->montessori_domain,
            ]);

            return back()->with('success', "Learning Path '{$path->title}' created successfully.");
        });
    }

    /**
     * Create new Activity under Learning Path.
     */
    public function storeActivity(Request $request, LmsLearningPath $path)
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'type' => ['required', 'string', 'in:quiz,game,exercise,skill_node,challenge'],
            'xp_points' => ['required', 'integer', 'min:5', 'max:500'],
        ]);

        return DB::transaction(function () use ($validated, $path) {
            $user = auth()->user();

            $activity = LmsActivity::create([
                'learning_path_id' => $path->id,
                'teacher_id' => $path->teacher_id ?? $user->id,
                'title' => $validated['title'],
                'type' => $validated['type'],
                'xp_points' => $validated['xp_points'],
                'is_published' => true,
                'status' => 'active',
            ]);

            AuditLog::record('create_lms_activity', 'lms', 'LmsActivity', $activity->id, [
                'title' => $activity->title,
                'path_id' => $path->id,
            ]);

            return back()->with('success', "Activity '{$activity->title}' added to '{$path->title}'.");
        });
    }

    /**
     * Toggle Publication Status (Superadmin publish / unpublish).
     */
    public function togglePublish(LmsLearningPath $path)
    {
        $path->update([
            'is_published' => !$path->is_published,
        ]);

        AuditLog::record('toggle_publish_lms_path', 'lms', 'LmsLearningPath', $path->id, [
            'title' => $path->title,
            'is_published' => $path->is_published,
        ]);

        $statusText = $path->is_published ? 'Published' : 'Unpublished';
        return back()->with('success', "Learning Path '{$path->title}' is now {$statusText}.");
    }

    /**
     * Archive Learning Path.
     */
    public function destroyPath(LmsLearningPath $path)
    {
        AuditLog::record('archive_lms_path', 'lms', 'LmsLearningPath', $path->id, [
            'title' => $path->title,
        ]);

        $path->update(['status' => 'archived']);

        return back()->with('success', "Learning Path '{$path->title}' archived successfully.");
    }
}
