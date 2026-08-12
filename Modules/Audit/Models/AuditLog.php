<?php

declare(strict_types=1);

namespace Modules\Audit\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Modules\Authentication\Models\User;
use Modules\Core\Models\BaseModel;

class AuditLog extends BaseModel
{
    protected $fillable = [
        'clinic_id',
        'user_id',
        'action',
        'module',
        'entity_type',
        'entity_id',
        'ip_address',
        'user_agent',
        'changes',
    ];

    protected function casts(): array
    {
        return [
            'changes' => 'array',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function entity(): MorphTo
    {
        return $this->morphTo();
    }

    public function scopeRecent($query, int $limit = 50)
    {
        return $query->latest('created_at')->limit($limit);
    }
}
