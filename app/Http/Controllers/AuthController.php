<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Role;
use Illuminate\Auth\Events\Registered;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Validation\Rules\Password as PasswordRules;

class AuthController extends Controller
{
    /**
     * Show Sign In Page.
     */
    public function showLogin()
    {
        if (Auth::check()) {
            return $this->redirectBasedOnRole(Auth::user());
        }
        return view('auth.login');
    }

    /**
     * Handle Sign In Request.
     */
    public function handleLogin(Request $request)
    {
        $loginInput = trim($request->input('login', $request->input('email', '')));
        $password = $request->input('password', '');

        if (empty($loginInput) || empty($password)) {
            return back()->withErrors([
                'email' => 'Please provide an email or Student Roll No and password.',
            ])->onlyInput('email');
        }

        $remember = $request->boolean('remember');

        // 1. Verify User Exists by Email
        $user = User::where('email', $loginInput)->first();

        // 2. Fallback: Search by Student Roll Number
        if (!$user) {
            $user = User::whereHas('studentProfile', function($q) use ($loginInput) {
                $q->where('student_number', $loginInput)
                  ->orWhere('student_number', 'STU-' . str_pad($loginInput, 5, '0', STR_PAD_LEFT));
            })->first();
        }

        if (!$user || !Hash::check($password, $user->password)) {
            return back()->withErrors([
                'email' => 'The provided credentials do not match our records.',
            ])->onlyInput('email');
        }

        // 3. Check Account Status
        if ($user->status === 'suspended') {
            return back()->withErrors([
                'email' => 'Your account has been suspended. Please contact the administrator.',
            ])->onlyInput('email');
        }

        // 4. Authenticate User Session
        Auth::login($user, $remember);
        $request->session()->regenerate();

        // 5. Check Email Verification (Teachers & Parents must be verified, Students do not require email verification)
        if (!$user->isStudent() && !$user->hasVerifiedEmail()) {
            if (!$user->verification_code || ($user->verification_code_expires_at && now()->greaterThan($user->verification_code_expires_at))) {
                $user->sendEmailVerificationNotification();
            }
            return redirect()->route('verification.notice')->with('status', 'Account Verification Required: A 6-digit verification code has been sent to your email address (' . $user->email . '). Please enter the code below to complete sign in.');
        }

        // 6. Redirect based on backend database role
        return $this->redirectBasedOnRole($user);
    }

    /**
     * Show Registration Page.
     */
    public function showRegister()
    {
        if (Auth::check()) {
            return $this->redirectBasedOnRole(Auth::user());
        }
        $roles = Role::whereIn('name', ['teacher', 'student', 'parent'])->get();
        return view('auth.register', compact('roles'));
    }

    /**
     * Handle Registration Request.
     */
    public function handleRegister(Request $request)
    {
        $validated = $request->validate([
            'first_name' => ['required', 'string', 'max:128'],
            'last_name' => ['required', 'string', 'max:128'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'role' => ['required', 'string', 'exists:roles,name'],
            'password' => ['required', 'confirmed', PasswordRules::defaults()],
        ]);

        $role = Role::where('name', $validated['role'])->first();
        $isStudent = ($role->name === 'student');

        $user = User::create([
            'first_name' => $validated['first_name'],
            'last_name' => $validated['last_name'],
            'name' => "{$validated['first_name']} {$validated['last_name']}",
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role_id' => $role->id,
            'status' => $isStudent ? 'active' : 'unverified',
            'email_verified_at' => $isStudent ? now() : null,
        ]);

        Auth::login($user);

        if (!$isStudent) {
            // Send Email Verification (Dispatches Code + Link notification for Teachers & Parents)
            $user->sendEmailVerificationNotification();
            return redirect()->route('verification.notice')->with('status', 'Account created! A 6-digit verification code and verification link have been sent to your email address.');
        }

        return $this->redirectBasedOnRole($user)->with('success', 'Student account created successfully! Welcome to Montessori ERP.');
    }

    /**
     * Show Email Verification Notice.
     */
    public function showVerifyNotice()
    {
        if (Auth::check()) {
            $user = User::find(Auth::id());
            if ($user && ($user->hasVerifiedEmail() || $user->status === 'active')) {
                return $this->redirectBasedOnRole($user);
            }
        }
        return view('auth.verify-email');
    }

    /**
     * Handle 6-Digit Code Verification Submission.
     */
    public function verifyCode(Request $request)
    {
        $request->validate([
            'email' => ['nullable', 'string', 'email'],
            'code' => ['required', 'string'],
        ]);

        $targetUser = null;
        if (Auth::check()) {
            $targetUser = User::find(Auth::id());
        }
        
        if (!$targetUser && $request->filled('email')) {
            $targetUser = User::where('email', trim($request->email))->first();
        }

        if (!$targetUser) {
            return redirect()->route('login')->with('error', 'Please enter your email address or sign in to verify your account.');
        }

        // Handle already active or verified users
        if ($targetUser->hasVerifiedEmail() || $targetUser->status === 'active') {
            if (!$targetUser->email_verified_at) {
                $targetUser->email_verified_at = now();
            }
            $targetUser->status = 'active';
            $targetUser->save();

            Auth::login($targetUser);
            $request->session()->regenerate();

            return $this->redirectBasedOnRole($targetUser)->with('success', 'Account verified! Welcome back.');
        }

        if ($targetUser->verifyWithCode($request->code)) {
            // Re-login verified user to refresh session state
            Auth::login($targetUser);
            $request->session()->regenerate();

            return $this->redirectBasedOnRole($targetUser)->with('success', 'Email verified successfully! Your account is now active.');
        }

        return back()->withErrors(['code' => 'Invalid or expired 6-digit verification code. Please click Resend Verification Code below if needed.']);
    }

    /**
     * Handle Email Verification Link Click (Signed URL).
     */
    public function verifyEmail(EmailVerificationRequest $request)
    {
        $request->fulfill();

        $user = $request->user();
        $user->update([
            'status' => 'active',
            'verification_code' => null,
            'verification_code_expires_at' => null,
        ]);

        return $this->redirectBasedOnRole($user)->with('success', 'Your email address has been verified successfully via link! Account is now active.');
    }

    /**
     * Resend Verification Code & Link.
     */
    public function resendVerification(Request $request)
    {
        if ($request->user()->hasVerifiedEmail()) {
            return $this->redirectBasedOnRole($request->user());
        }

        $request->user()->sendEmailVerificationNotification();

        return back()->with('status', 'A new 6-digit verification code and link have been dispatched to your email inbox.');
    }

    /**
     * Show Forgot Password Form.
     */
    public function showForgotPassword()
    {
        return view('auth.forgot-password');
    }

    /**
     * Handle Password Reset Request Link.
     */
    public function handleForgotPassword(Request $request)
    {
        $request->validate(['email' => 'required|email']);

        $status = Password::sendResetLink($request->only('email'));

        if ($status === Password::RESET_LINK_SENT) {
            return back()->with('status', __($status));
        }

        return back()->withErrors(['email' => __($status)]);
    }

    /**
     * Show Reset Password Form.
     */
    public function showResetPassword(Request $request, $token = null)
    {
        return view('auth.reset-password', ['token' => $token, 'email' => $request->email]);
    }

    /**
     * Handle Reset Password Update.
     */
    public function handleResetPassword(Request $request)
    {
        $request->validate([
            'token' => 'required',
            'email' => 'required|email',
            'password' => ['required', 'confirmed', PasswordRules::defaults()],
        ]);

        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function ($user, $password) {
                $user->forceFill([
                    'password' => Hash::make($password)
                ])->save();
            }
        );

        if ($status === Password::PASSWORD_RESET) {
            return redirect()->route('login')->with('status', __($status));
        }

        return back()->withErrors(['email' => __($status)]);
    }

    /**
     * Handle Change Password for Authenticated User.
     */
    public function changePassword(Request $request)
    {
        $validated = $request->validate([
            'current_password' => ['required', 'string'],
            'password' => ['required', 'confirmed', PasswordRules::defaults()],
        ]);

        $user = Auth::user();

        if (!Hash::check($validated['current_password'], $user->password)) {
            return back()->withErrors(['current_password' => 'Your current password does not match our records.']);
        }

        $user->update([
            'password' => Hash::make($validated['password']),
        ]);

        return back()->with('success', 'Your password has been changed successfully.');
    }

    /**
     * Handle Sign Out.
     */
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('landing')->with('success', 'You have been signed out successfully.');
    }

    /**
     * Helper to redirect user to role-specific dashboard.
     */
    protected function redirectBasedOnRole($user)
    {
        $roleName = $user->role ? $user->role->name : 'superadmin';

        return match ($roleName) {
            'superadmin' => redirect()->route('dashboard.superadmin'),
            'principal'  => redirect()->route('dashboard.principal'),
            'admin'      => redirect()->route('dashboard.admin'),
            'teacher'    => redirect()->route('dashboard.teacher'),
            'student'    => redirect()->route('dashboard.student'),
            'parent'     => redirect()->route('dashboard.parent'),
            default      => redirect()->route('dashboard'),
        };
    }
}
