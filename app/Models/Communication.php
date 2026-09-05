<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Communication extends Model
{
    use HasFactory;

    protected $fillable = [
        'sender_id',
        'campus_id',
        'audience_type',
        'target_classroom_id',
        'subject',
        'message',
        'status',
    ];

    public function sender()
    {
        return $this->belongsTo(User::class, 'sender_id');
    }

    public function campus()
    {
        return $this->belongsTo(Campus::class);
    }

    public function targetClassroom()
    {
        return $this->belongsTo(Classroom::class, 'target_classroom_id');
    }

    public function recipients()
    {
        return $this->hasMany(CommunicationRecipient::class);
    }
}
