<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Role;
use App\Models\ParentProfile;
use App\Models\Student;
use App\Models\Campus;
use App\Models\AuditLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class ParentController extends Controller
{
    /**
     * List all parents with real DB filters.
     */
    public function index(Request $request)
    {
        // Automatically ensure all users with 'parent' role have a ParentProfile record
        $parentRoleId = \App\Models\Role::where('name', 'parent')->value('id');
        if ($parentRoleId) {
            $parentUsersWithoutProfile = \App\Models\User::where('role_id', $parentRoleId)
                ->whereDoesntHave('parentProfile')
                ->get();
            foreach ($parentUsersWithoutProfile as $pUser) {
                ParentProfile::firstOrCreate([
                    'user_id' => $pUser->id,
                ], [
                    'campus_id' => $pUser->campus_id ?? 1,
                    'status' => 'active',
                ]);
            }
        }

        $user = auth()->user();
        $query = ParentProfile::with(['user', 'campus', 'children.user']);
        $students = Student::with('user')->get();

        if ($user && $user->role) {
            $roleName = $user->role->name;

            if ($roleName === 'teacher') {
                $assignedClassroomIds = \App\Helpers\AcademicScopeHelper::getAssignedClassroomIds($user);
                $students = Student::whereIn('classroom_id', $assignedClassroomIds)->with('user')->get();
                // Show parents linked to teacher's classroom students or all active campus parents for linking
                $query->where(function($q) use ($assignedClassroomIds) {
                    $q->whereHas('children', function($cq) use ($assignedClassroomIds) {
                        $cq->whereIn('classroom_id', $assignedClassroomIds);
                    })->orWhereDoesntHave('children');
                });
            } elseif ($roleName === 'parent') {
                $query->where('user_id', $user->id);
            }
        }

        if ($request->filled('campus_id')) {
            $query->where('campus_id', $request->campus_id);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('phone', 'like', "%{$search}%")
                  ->orWhere('occupation', 'like', "%{$search}%")
                  ->orWhereHas('user', function($uq) use ($search) {
                      $uq->where('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%");
                  });
            });
        }

        $parents = $query->latest()->paginate(15);
        $campuses = Campus::all();
        $totalParents = $parents->total();

        return view('modules.parents', compact('parents', 'campuses', 'students', 'totalParents'));
    }

    /**
     * Create new Parent account and profile.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'first_name' => ['required', 'string', 'max:128'],
            'last_name' => ['required', 'string', 'max:128'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8'],
            'campus_id' => ['nullable', 'exists:campuses,id'],
            'phone' => ['nullable', 'string', 'max:64'],
            'occupation' => ['nullable', 'string', 'max:255'],
            'address' => ['nullable', 'string', 'max:255'],
            'student_ids' => ['nullable', 'array'],
            'student_ids.*' => ['exists:students,id'],
            'relationship_type' => ['nullable', 'string', 'in:Father,Mother,Guardian'],
        ]);

        return DB::transaction(function () use ($validated) {
            $parentRole = Role::where('name', 'parent')->firstOrFail();

            // 1. Create User Account (Unverified)
            $user = User::create([
                'first_name' => $validated['first_name'],
                'last_name' => $validated['last_name'],
                'name' => "{$validated['first_name']} {$validated['last_name']}",
                'email' => $validated['email'],
                'password' => Hash::make($validated['password']),
                'role_id' => $parentRole->id,
                'status' => 'unverified',
            ]);

            // 2. Create Parent Profile
            $parent = ParentProfile::create([
                'user_id' => $user->id,
                'campus_id' => $validated['campus_id'] ?? Campus::first()?->id,
                'phone' => $validated['phone'] ?? null,
                'occupation' => $validated['occupation'] ?? null,
                'address' => $validated['address'] ?? null,
                'status' => 'active',
            ]);

            // 3. Link Student(s) cleanly via pivot table
            if (!empty($validated['student_ids'])) {
                $pivotData = [];
                $rel = $validated['relationship_type'] ?? 'Parent';
                foreach ($validated['student_ids'] as $sId) {
                    $pivotData[$sId] = ['relationship_type' => $rel];
                }
                $parent->children()->sync($pivotData);
            }

            // 4. Dispatch Email Verification (Parent remains unverified until completing flow)
            $user->sendEmailVerificationNotification();

            // 5. Record Audit Log
            AuditLog::record('create_parent', 'parents', 'ParentProfile', $parent->id, [
                'name' => $user->name,
                'email' => $user->email,
            ]);

            return back()->with('success', "Parent '{$user->name}' created! Verification code & link dispatched to {$user->email}. Account remains unverified until user verifies.");
        });
    }

    /**
     * Link a parent to a student.
     */
    public function linkStudent(Request $request, ParentProfile $parent)
    {
        $validated = $request->validate([
            'student_id' => ['required', 'exists:students,id'],
            'relationship_type' => ['required', 'string', 'in:Father,Mother,Guardian'],
        ]);

        $parent->children()->syncWithoutDetaching([
            $validated['student_id'] => ['relationship_type' => $validated['relationship_type']]
        ]);

        AuditLog::record('link_parent_student', 'parents', 'ParentProfile', $parent->id, [
            'student_id' => $validated['student_id'],
            'relationship' => $validated['relationship_type'],
        ]);

        return back()->with('success', 'Student linked to parent successfully.');
    }

    /**
     * Update Parent details.
     */
    public function update(Request $request, ParentProfile $parent)
    {
        $validated = $request->validate([
            'first_name' => ['required', 'string', 'max:128'],
            'last_name' => ['required', 'string', 'max:128'],
            'phone' => ['nullable', 'string', 'max:64'],
            'occupation' => ['nullable', 'string', 'max:255'],
            'address' => ['nullable', 'string', 'max:255'],
            'status' => ['required', 'string', 'in:active,inactive,suspended,archived'],
        ]);

        return DB::transaction(function () use ($validated, $parent) {
            $parent->user->update([
                'first_name' => $validated['first_name'],
                'last_name' => $validated['last_name'],
                'name' => "{$validated['first_name']} {$validated['last_name']}",
            ]);

            $parent->update([
                'phone' => $validated['phone'],
                'occupation' => $validated['occupation'],
                'address' => $validated['address'],
                'status' => $validated['status'],
            ]);

            AuditLog::record('update_parent', 'parents', 'ParentProfile', $parent->id, [
                'name' => $parent->user->name,
                'status' => $validated['status'],
            ]);

            return back()->with('success', "Parent record '{$parent->user->name}' updated successfully.");
        });
    }

    /**
     * Archive/Delete Parent.
     */
    public function destroy(ParentProfile $parent)
    {
        AuditLog::record('archive_parent', 'parents', 'ParentProfile', $parent->id, [
            'name' => $parent->user->name,
        ]);

        $parent->update(['status' => 'archived']);
        $parent->delete();

        return back()->with('success', 'Parent record archived successfully.');
    }
}
