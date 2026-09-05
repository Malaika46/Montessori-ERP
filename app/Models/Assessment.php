<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Assessment extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_id',
        'classroom_id',
        'teacher_id',
        'title',
        'evaluation_period',
        'term_name',
        'practical_life_score',
        'practical_life_status',
        'sensorial_score',
        'sensorial_status',
        'mathematics_score',
        'mathematics_status',
        'language_score',
        'language_status',
        'cultural_score',
        'cultural_status',
        'overall_score',
        'overall_summary',
        'status',
        'published_at',
    ];

    protected $casts = [
        'published_at' => 'date',
    ];

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function classroom()
    {
        return $this->belongsTo(Classroom::class);
    }

    public function teacher()
    {
        return $this->belongsTo(Teacher::class);
    }
}
