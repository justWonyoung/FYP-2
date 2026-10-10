<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Schema;

class ActivityLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'action',
        'module',
        'description',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id', 'user_id');
    }

    public static function record(?User $user, string $action, string $module, string $description): void
    {
        if (! Schema::hasTable('activity_logs')) {
            return;
        }

        static::query()->create([
            'user_id' => $user?->getKey(),
            'action' => $action,
            'module' => $module,
            'description' => $description,
        ]);
    }
}
