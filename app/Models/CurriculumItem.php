<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CurriculumItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'classroom_id',
        'created_by_user_id',
        'title',
        'avenue',
        'age_group',
        'description',
        'learning_objectives',
        'status',
    ];

    public function classroom()
    {
        return $this->belongsTo(Classroom::class);
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by_user_id');
    }
}
