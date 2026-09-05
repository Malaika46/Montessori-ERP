<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use App\Notifications\SendEmailVerificationCodeAndLink;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasApiTokens, HasFactory, Notifiable, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'first_name',
        'last_name',
        'name',
        'email',
        'password',
        'role_id',
        'status',
        'email_verified_at',
        'verification_code',
        'verification_code_expires_at',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
        'verification_code',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'verification_code_expires_at' => 'datetime',
    ];

    /**
     * Determine if the user has verified their email address or is active.
     */
    public function hasVerifiedEmail(): bool
    {
        return !is_null($this->email_verified_at) || $this->status === 'active';
    }

    /**
     * Verify code submitted by user.
     */
    public function verifyWithCode(string $code): bool
    {
        $inputCode = trim((string) $code);
        $storedCode = trim((string) $this->verification_code);

        if (empty($inputCode) || empty($storedCode)) {
            return false;
        }

        if ($storedCode === $inputCode) {
            $this->markEmailAsVerified();
            $this->status = 'active';
            $this->verification_code = null;
            $this->verification_code_expires_at = null;
            $this->save();
            return true;
        }

        return false;
    }

    /**
     * Get user's full name.
     */
    public function getNameAttribute($value)
    {
        if ($this->first_name && $this->last_name) {
            return trim("{$this->first_name} {$this->last_name}");
        }
        return $value ?? 'User';
    }

    /**
     * Relationship to Role
     */
    public function role()
    {
        return $this->belongsTo(Role::class);
    }

    /**
     * Profile Relationships
     */
    public function studentProfile()
    {
        return $this->hasOne(Student::class);
    }

    public function teacherProfile()
    {
        return $this->hasOne(Teacher::class);
    }

    public function parentProfile()
    {
        return $this->hasOne(ParentProfile::class);
    }

    public function auditLogs()
    {
        return $this->hasMany(AuditLog::class);
    }

    public function communicationsSent()
    {
        return $this->hasMany(Communication::class, 'sender_id');
    }

    /**
     * Relationship to User Permission Overrides
     */
    public function permissionOverrides()
    {
        return $this->belongsToMany(Permission::class, 'user_permissions')->withPivot('is_allowed');
    }

    /**
     * Check if user has specific role or one of multiple roles.
     */
    public function hasRole($roles): bool
    {
        if (!$this->role) {
            return false;
        }

        if (is_array($roles)) {
            return in_array($this->role->name, $roles);
        }

        return $this->role->name === $roles;
    }

    /**
     * Check if user has a permission (via override or role).
     */
    public function hasPermission(string $permissionName): bool
    {
        // 1. Check user-specific override first
        $override = $this->permissionOverrides()->where('name', $permissionName)->first();
        if ($override) {
            return (bool) $override->pivot->is_allowed;
        }

        // 2. Superadmin has all permissions by default
        if ($this->hasRole('superadmin')) {
            return true;
        }

        // 3. Fallback to role permissions
        if (!$this->role) {
            return false;
        }

        return $this->role->permissions()->where('name', $permissionName)->exists();
    }

    /**
     * Send Custom 6-Digit Code & Link Email Verification Notification.
     */
    public function sendEmailVerificationNotification()
    {
        $code = str_pad((string) random_int(0, 999999), 6, '0', STR_PAD_LEFT);
        $this->verification_code = $code;
        $this->verification_code_expires_at = now()->addMinutes(60);
        $this->status = 'unverified';
        $this->email_verified_at = null;
        $this->save();

        try {
            $this->notify(new \App\Notifications\SendEmailVerificationCodeAndLink($code));
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::error("Failed sending email verification to {$this->email}: " . $e->getMessage());
        }
    }

    // Role helper shortcuts
    public function isSuperadmin(): bool { return $this->hasRole('superadmin'); }
    public function isPrincipal(): bool { return $this->hasRole('principal'); }
    public function isAdmin(): bool { return $this->hasRole('admin'); }
    public function isTeacher(): bool { return $this->hasRole('teacher'); }
    public function isStudent(): bool { return $this->hasRole('student'); }
    public function isParent(): bool { return $this->hasRole('parent'); }
}
