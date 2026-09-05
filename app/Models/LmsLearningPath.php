<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LmsLearningPath extends Model
{
    use HasFactory;

    protected $fillable = [
        'teacher_id',
        'campus_id',
        'classroom_id',
        'title',
        'description',
        'montessori_domain',
        'is_published',
        'status',
    ];

    protected $casts = [
        'is_published' => 'boolean',
    ];

    public function teacher()
    {
        return $this->belongsTo(User::class, 'teacher_id');
    }

    public function campus()
    {
        return $this->belongsTo(Campus::class);
    }

    public function classroom()
    {
        return $this->belongsTo(Classroom::class);
    }

    public function activities()
    {
        return $this->hasMany(LmsActivity::class, 'learning_path_id');
    }
}
