<?php

declare(strict_types=1);

namespace Modules\Audit\Services;

use Illuminate\Database\Eloquent\Model;
use Modules\Audit\Models\AuditLog;

/**
 * Central audit logging.
 *
 * Records sensitive actions without ever capturing the values of medical
 * fields — only a description of what changed and by whom.
 */
class AuditService
{
    /**
     * @param  array<string, mixed>|null  $changes
     */
    public function record(
        string $action,
        ?string $module = null,
        Model|string|null $entity = null,
        ?array $changes = null,
        ?int $userId = null,
    ): AuditLog {
        [$entityType, $entityId] = $this->resolveEntity($entity);

        return AuditLog::query()->create([
            'clinic_id' => current_clinic()?->id,
            'user_id' => $userId ?? auth()->id(),
            'action' => $action,
            'module' => $module,
            'entity_type' => $entityType,
            'entity_id' => $entityId,
            'ip_address' => request()->ip(),
            'user_agent' => substr((string) request()->userAgent(), 0, 500),
            'changes' => $this->sanitize($changes),
        ]);
    }

    /**
     * @return array{string|null, string|null}
     */
    private function resolveEntity(Model|string|null $entity): array
    {
        if ($entity instanceof Model) {
            return [$entity::class, (string) $entity->getKey()];
        }

        if (is_string($entity) && $entity !== '') {
            return [null, $entity];
        }

        return [null, null];
    }

    /**
     * @param  array<string, mixed>|null  $changes
     * @return array<string, mixed>|null
     */
    private function sanitize(?array $changes): ?array
    {
        if ($changes === null) {
            return null;
        }

        $sanitized = [];

        foreach ($changes as $key => $value) {
            if (is_array($value) || is_bool($value) || is_null($value) || is_scalar($value)) {
                if (is_string($value)) {
                    $value = mb_substr($value, 0, 2000);
                }

                $sanitized[$key] = $value;
            }
        }

        return $sanitized;
    }
}
