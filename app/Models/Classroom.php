<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Classroom extends Model
{
    use HasFactory;

    protected $fillable = [
        'campus_id',
        'name',
        'code',
        'age_group',
        'capacity',
        'lead_teacher_id',
        'description',
        'status',
    ];

    public function campus()
    {
        return $this->belongsTo(Campus::class);
    }

    public function leadTeacher()
    {
        return $this->belongsTo(User::class, 'lead_teacher_id');
    }

    public function students()
    {
        return $this->hasMany(Student::class);
    }

    public function teachers()
    {
        return $this->belongsToMany(Teacher::class, 'classroom_teacher');
    }

    public function learningPaths()
    {
        return $this->hasMany(LmsLearningPath::class);
    }
}
