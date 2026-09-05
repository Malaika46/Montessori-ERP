<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AuditLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'action',
        'module',
        'target_type',
        'target_id',
        'details_json',
        'ip_address',
    ];

    protected $casts = [
        'details_json' => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Static helper method to record an audit log entry easily.
     */
    public static function record(string $action, string $module, ?string $targetType = null, ?int $targetId = null, ?array $details = null)
    {
        return static::create([
            'user_id' => auth()->id(),
            'action' => $action,
            'module' => $module,
            'target_type' => $targetType,
            'target_id' => $targetId,
            'details_json' => $details,
            'ip_address' => request()->ip(),
        ]);
    }
}
