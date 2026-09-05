<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\CurriculumItem;
use App\Models\Classroom;
use App\Models\AuditLog;
use App\Helpers\AcademicScopeHelper;
use Illuminate\Support\Facades\DB;

class CurriculumController extends Controller
{
    /**
     * Display Montessori Curriculum Management & Scope.
     */
    public function index(Request $request)
    {
        $user = auth()->user();
        $query = CurriculumItem::with(['classroom', 'createdBy']);

        $classrooms = collect();
        if ($user && $user->role) {
            $roleName = $user->role->name;

            if ($roleName === 'teacher') {
                $assignedClassroomIds = AcademicScopeHelper::getAssignedClassroomIds($user);
                $query->where(function($q) use ($assignedClassroomIds) {
                    $q->whereIn('classroom_id', $assignedClassroomIds)
                      ->orWhereNull('classroom_id');
                });
                $classrooms = Classroom::whereIn('id', $assignedClassroomIds)->get();
            } elseif ($roleName === 'parent') {
                $childIds = AcademicScopeHelper::getAuthorizedStudentIds($user);
                $classroomIds = \App\Models\Student::whereIn('id', $childIds)->pluck('classroom_id')->filter()->toArray();
                $query->where(function($q) use ($classroomIds) {
                    $q->whereIn('classroom_id', $classroomIds)
                      ->orWhereNull('classroom_id');
                })->where('status', 'active');
                $classrooms = Classroom::whereIn('id', $classroomIds)->get();
            } elseif ($roleName === 'student') {
                $studentProfile = \App\Models\Student::where('user_id', $user->id)->first();
                $classId = $studentProfile ? $studentProfile->classroom_id : null;
                $query->where(function($q) use ($classId) {
                    if ($classId) {
                        $q->where('classroom_id', $classId);
                    }
                    $q->orWhereNull('classroom_id');
                })->where('status', 'active');
                if ($classId) {
                    $classrooms = Classroom::where('id', $classId)->get();
                }
            } else {
                $classrooms = Classroom::all();
            }
        }

        // Seed standard default Montessori items if database is empty
        if (CurriculumItem::count() === 0) {
            $defaultItems = [
                [
                    'classroom_id' => null,
                    'created_by_user_id' => $user ? $user->id : null,
                    'title' => 'Dry Pouring & Spooning Exercises',
                    'avenue' => 'Practical Life',
                    'age_group' => '3 - 4 Years',
                    'description' => 'Development of fine motor control, concentration, hand-eye coordination, and left-to-right visual tracking.',
                    'learning_objectives' => 'Independence, pincer grasp readiness, focus build-up.',
                    'status' => 'active',
                ],
                [
                    'classroom_id' => null,
                    'created_by_user_id' => $user ? $user->id : null,
                    'title' => 'Pink Tower & Broad Stairs',
                    'avenue' => 'Sensorial',
                    'age_group' => '3 - 4.5 Years',
                    'description' => 'Visual discrimination of three-dimensional size, volume, spatial relationship, and preparation for decimal system.',
                    'learning_objectives' => 'Size grading, vocabulary of comparison (big/small), motor control.',
                    'status' => 'active',
                ],
                [
                    'classroom_id' => null,
                    'created_by_user_id' => $user ? $user->id : null,
                    'title' => 'Number Rods & Sandpaper Numerals',
                    'avenue' => 'Mathematics',
                    'age_group' => '3.5 - 5 Years',
                    'description' => 'Associating visual symbols with concrete quantity (1-10) using tactile muscle memory tracing.',
                    'learning_objectives' => 'Tactile numeral recognition, 1-to-1 correspondence, quantity sequencing.',
                    'status' => 'active',
                ],
                [
                    'classroom_id' => null,
                    'created_by_user_id' => $user ? $user->id : null,
                    'title' => 'Phonetic Object Trays & Movable Alphabet',
                    'avenue' => 'Language',
                    'age_group' => '4 - 5.5 Years',
                    'description' => 'Analyzing phonemes in three-letter CVC words and constructing words independently prior to mechanical writing.',
                    'learning_objectives' => 'Phonics synthesis, early word building, reading readiness.',
                    'status' => 'active',
                ],
                [
                    'classroom_id' => null,
                    'created_by_user_id' => $user ? $user->id : null,
                    'title' => 'Puzzle Maps of World Continents',
                    'avenue' => 'Cultural',
                    'age_group' => '4.5 - 6 Years',
                    'description' => 'Sensory exploration of physical geography, continent names, ocean biomes, and cultural awareness.',
                    'learning_objectives' => 'Spatial map skills, continent identification, fine motor placement.',
                    'status' => 'active',
                ],
            ];
            foreach ($defaultItems as $dItem) {
                CurriculumItem::create($dItem);
            }
        }

        // Apply Search Filter
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%")
                  ->orWhere('learning_objectives', 'like', "%{$search}%");
            });
        }

        // Apply Avenue Filter
        if ($request->filled('avenue')) {
            $query->where('avenue', $request->avenue);
        }

        // Apply Classroom Filter
        if ($request->filled('classroom_id')) {
            $query->where('classroom_id', $request->classroom_id);
        }

        $curriculumItems = $query->latest()->paginate(12);

        // Stats summary
        $totalCount = CurriculumItem::count();
        $avenues = ['Practical Life', 'Sensorial', 'Mathematics', 'Language', 'Cultural'];
        $avenueCounts = [];
        foreach ($avenues as $av) {
            $avenueCounts[$av] = CurriculumItem::where('avenue', $av)->count();
        }

        return view('modules.curriculum', compact('curriculumItems', 'classrooms', 'totalCount', 'avenueCounts'));
    }

    /**
     * Store new Curriculum Item.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'avenue' => ['required', 'string', 'in:Practical Life,Sensorial,Mathematics,Language,Cultural'],
            'classroom_id' => ['nullable', 'exists:classrooms,id'],
            'age_group' => ['nullable', 'string', 'max:100'],
            'description' => ['required', 'string'],
            'learning_objectives' => ['nullable', 'string'],
            'status' => ['required', 'string', 'in:active,archived'],
        ]);

        $validated['created_by_user_id'] = auth()->id();

        $item = CurriculumItem::create($validated);

        AuditLog::record('created_curriculum_item', 'curriculum', 'CurriculumItem', $item->id, [
            'title' => $item->title,
            'avenue' => $item->avenue,
        ]);

        return redirect()->route('curriculum.index')->with('success', 'Montessori Curriculum item added successfully!');
    }

    /**
     * Update existing Curriculum Item.
     */
    public function update(Request $request, CurriculumItem $curriculumItem)
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'avenue' => ['required', 'string', 'in:Practical Life,Sensorial,Mathematics,Language,Cultural'],
            'classroom_id' => ['nullable', 'exists:classrooms,id'],
            'age_group' => ['nullable', 'string', 'max:100'],
            'description' => ['required', 'string'],
            'learning_objectives' => ['nullable', 'string'],
            'status' => ['required', 'string', 'in:active,archived'],
        ]);

        $curriculumItem->update($validated);

        AuditLog::record('updated_curriculum_item', 'curriculum', 'CurriculumItem', $curriculumItem->id, [
            'title' => $curriculumItem->title,
        ]);

        return redirect()->route('curriculum.index')->with('success', 'Montessori Curriculum item updated successfully!');
    }

    /**
     * Delete Curriculum Item.
     */
    public function destroy(CurriculumItem $curriculumItem)
    {
        $title = $curriculumItem->title;
        $curriculumItem->delete();

        AuditLog::record('deleted_curriculum_item', 'curriculum', 'CurriculumItem', $curriculumItem->id, [
            'title' => $title,
        ]);

        return redirect()->route('curriculum.index')->with('success', 'Curriculum item removed successfully!');
    }
}
