<?php

declare(strict_types=1);

namespace Modules\Audit\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @method static \Modules\Audit\Models\AuditLog record(string $action, ?string $module = null, \Illuminate\Database\Eloquent\Model|string|null $entity = null, ?array $changes = null, ?int $userId = null)
 *
 * @see \Modules\Audit\Services\AuditService
 */
class Audit extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return \Modules\Audit\Services\AuditService::class;
    }
}
