<?php

declare(strict_types=1);

namespace Modules\Core\Models;

use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Model;

/**
 * Shared base model for all domain models.
 *
 * Uses ULIDs so entities can be referenced safely and UUID-style keys
 * make future multi-clinic/SaaS migrations less painful.
 *
 * Soft deletes are opted into per-model (add the SoftDeletes trait)
 * since not every table warrants them.
 */
abstract class BaseModel extends Model
{
    use HasUlids;

    protected $guarded = [];

    public function newUniqueId(): string
    {
        return (string) \Illuminate\Support\Str::ulid();
    }

    public function uniqueIds(): array
    {
        return ['id'];
    }
}
