<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LmsActivity extends Model
{
    use HasFactory;

    protected $fillable = [
        'learning_path_id',
        'teacher_id',
        'title',
        'type',
        'xp_points',
        'config_json',
        'is_published',
        'status',
    ];

    protected $casts = [
        'config_json' => 'array',
        'is_published' => 'boolean',
    ];

    public function learningPath()
    {
        return $this->belongsTo(LmsLearningPath::class, 'learning_path_id');
    }

    public function teacher()
    {
        return $this->belongsTo(User::class, 'teacher_id');
    }

    public function studentProgress()
    {
        return $this->hasMany(LmsStudentProgress::class, 'activity_id');
    }
}
