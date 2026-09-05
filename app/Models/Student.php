<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Student extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'campus_id',
        'classroom_id',
        'student_number',
        'date_of_birth',
        'gender',
        'status',
    ];

    protected $casts = [
        'date_of_birth' => 'date',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function campus()
    {
        return $this->belongsTo(Campus::class);
    }

    public function classroom()
    {
        return $this->belongsTo(Classroom::class);
    }

    public function parents()
    {
        return $this->belongsToMany(ParentProfile::class, 'parent_student', 'student_id', 'parent_id')
                    ->withPivot('relationship_type');
    }

    public function lmsProgress()
    {
        return $this->hasMany(LmsStudentProgress::class);
    }

    public function lmsRewards()
    {
        return $this->hasMany(LmsStudentReward::class);
    }

    public function attendances()
    {
        return $this->hasMany(Attendance::class);
    }

    public function observations()
    {
        return $this->hasMany(Observation::class);
    }
}
