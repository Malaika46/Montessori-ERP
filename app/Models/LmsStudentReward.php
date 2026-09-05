<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LmsStudentReward extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_id',
        'badge_name',
        'badge_icon',
        'streak_count',
        'total_xp',
    ];

    public function student()
    {
        return $this->belongsTo(Student::class);
    }
}
