<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password as PasswordRules;

class UserController extends Controller
{
    /**
     * Display list of users with roles & status.
     */
    public function index()
    {
        $users = User::with('role')->latest()->paginate(15);
        $roles = Role::all();
        $totalUsers = User::count();
        $activeUsers = User::where('status', 'active')->count();
        $unverifiedUsers = User::where('status', 'unverified')->count();

        return view('modules.users', compact('users', 'roles', 'totalUsers', 'activeUsers', 'unverifiedUsers'));
    }

    /**
     * Store new user & dispatch verification code & link.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'first_name' => ['required', 'string', 'max:128'],
            'last_name' => ['required', 'string', 'max:128'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'role_id' => ['required', 'exists:roles,id'],
            'password' => ['required', PasswordRules::defaults()],
        ]);

        $role = Role::findOrFail($validated['role_id']);
        $isStudent = ($role->name === 'student');

        $user = User::create([
            'first_name' => $validated['first_name'],
            'last_name' => $validated['last_name'],
            'name' => "{$validated['first_name']} {$validated['last_name']}",
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role_id' => $validated['role_id'],
            'status' => $isStudent ? 'active' : 'unverified',
            'email_verified_at' => $isStudent ? now() : null,
        ]);

        if (!$isStudent) {
            // Dispatch Email with 6-Digit Code AND Link automatically for Teachers, Parents, and Staff!
            $user->sendEmailVerificationNotification();
            return back()->with('success', "User '{$user->name}' created successfully! A 6-digit verification code & link have been dispatched to {$user->email}.");
        }

        return back()->with('success', "Student '{$user->name}' created successfully! Account is active.");
    }

    /**
     * Manually mark user email as verified by Admin/Superadmin.
     */
    public function verifyNow(User $user)
    {
        $user->forceFill([
            'email_verified_at' => now(),
            'status' => 'active',
            'verification_code' => null,
            'verification_code_expires_at' => null,
        ])->save();

        \App\Models\AuditLog::record('manual_email_verify', 'users', 'User', $user->id, [
            'email' => $user->email,
        ]);

        return back()->with('success', "Email for '{$user->name}' manually verified successfully!");
    }

    /**
     * Resend verification email code & link to user.
     */
    public function resendVerification(User $user)
    {
        $user->sendEmailVerificationNotification();

        return back()->with('success', "Verification code & link resent to {$user->email}. (Verification Code: {$user->verification_code})");
    }

    /**
     * Delete/Deactivate user.
     */
    public function destroy(User $user)
    {
        if ($user->id === auth()->id()) {
            return back()->with('error', 'You cannot delete your own account.');
        }

        $user->delete();
        return back()->with('success', 'User removed successfully.');
    }
}
