<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LmsStudentProgress extends Model
{
    use HasFactory;

    protected $table = 'lms_student_progress';

    protected $fillable = [
        'student_id',
        'activity_id',
        'status',
        'xp_earned',
        'score',
        'completed_at',
    ];

    protected $casts = [
        'completed_at' => 'datetime',
    ];

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function activity()
    {
        return $this->belongsTo(LmsActivity::class, 'activity_id');
    }
}
