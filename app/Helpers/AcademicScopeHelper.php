<?php

namespace App\Helpers;

use App\Models\User;
use App\Models\Teacher;
use App\Models\Student;
use App\Models\ParentProfile;
use App\Models\Classroom;
use Illuminate\Support\Collection;

class AcademicScopeHelper
{
    /**
     * Get assigned classroom IDs for the authenticated user based on role.
     */
    public static function getAssignedClassroomIds(?User $user): Collection
    {
        if (!$user) {
            return collect();
        }

        $roleName = $user->role ? $user->role->name : 'guest';

        if (in_array($roleName, ['superadmin', 'principal', 'admin'])) {
            return Classroom::pluck('id');
        }

        if ($roleName === 'teacher') {
            $teacher = Teacher::where('user_id', $user->id)->first();
            if (!$teacher) {
                $teacher = Teacher::create([
                    'user_id' => $user->id,
                    'campus_id' => $user->campus_id ?? 1,
                    'specialization' => 'Montessori Guide',
                    'status' => 'active',
                ]);
            }

            $classIdsFromPivot = $teacher->classrooms->pluck('id');
            $classIdsFromLead = Classroom::where('lead_teacher_id', $user->id)->pluck('id');
            $assignedClassroomIds = $classIdsFromPivot->merge($classIdsFromLead)->unique();

            if ($assignedClassroomIds->isEmpty()) {
                $allClasses = Classroom::where('status', 'active')->pluck('id');
                if ($allClasses->isNotEmpty()) {
                    $teacher->classrooms()->syncWithoutDetaching($allClasses);
                    $assignedClassroomIds = $allClasses;
                }
            }

            return $assignedClassroomIds;
        }

        if ($roleName === 'student') {
            $student = Student::where('user_id', $user->id)->first();
            return $student && $student->classroom_id ? collect([$student->classroom_id]) : collect();
        }

        if ($roleName === 'parent') {
            $parent = ParentProfile::where('user_id', $user->id)->first();
            if ($parent) {
                return $parent->children->pluck('classroom_id')->filter()->unique();
            }
            return collect();
        }

        return collect();
    }

    /**
     * Get authorized student IDs for the authenticated user.
     */
    public static function getAuthorizedStudentIds(?User $user): Collection
    {
        if (!$user) {
            return collect();
        }

        $roleName = $user->role ? $user->role->name : 'guest';

        if (in_array($roleName, ['superadmin', 'principal', 'admin'])) {
            return Student::pluck('id');
        }

        if ($roleName === 'teacher') {
            $classroomIds = self::getAssignedClassroomIds($user);
            return Student::whereIn('classroom_id', $classroomIds)->pluck('id');
        }

        if ($roleName === 'student') {
            $student = Student::where('user_id', $user->id)->first();
            return $student ? collect([$student->id]) : collect();
        }

        if ($roleName === 'parent') {
            $parent = ParentProfile::where('user_id', $user->id)->first();
            return $parent ? $parent->children->pluck('id') : collect();
        }

        return collect();
    }
}
