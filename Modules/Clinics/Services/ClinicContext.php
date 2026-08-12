<?php

declare(strict_types=1);

namespace Modules\Clinics\Services;

use Illuminate\Contracts\Cache\Repository as Cache;
use Illuminate\Support\Facades\Cache as CacheFacade;
use Modules\Clinics\Models\Clinic;

/**
 * Resolves the clinic the application currently operates for.
 *
 * The app starts in single-clinic mode: the first active clinic. The
 * resolution is cached so it can later be swapped for a tenant-scoped
 * resolver (subdomain / header / auth user) without touching callers.
 *
 * Only the clinic id is stored in the cache — a serialized Eloquent model
 * can otherwise unserialize into __PHP_Incomplete_Class before its class
 * is loaded on a fresh request.
 */
final class ClinicContext
{
    public const CACHE_KEY = 'cms.current_clinic_id';

    private ?Clinic $resolved = null;

    private bool $resolvedFlag = false;

    public function __construct(private readonly Cache $cache)
    {
    }

    public function current(): ?Clinic
    {
        if ($this->resolvedFlag) {
            return $this->resolved;
        }

        $id = $this->cache->remember(self::CACHE_KEY, now()->addHours(1), function (): ?string {
            return Clinic::query()->where('is_active', true)->orderBy('created_at')->value('id');
        });

        $this->resolved = $id ? Clinic::query()->find($id) : null;
        $this->resolvedFlag = true;

        return $this->resolved;
    }

    public function currentId(): ?string
    {
        return $this->current()?->id;
    }

    public function forget(): void
    {
        $this->cache->forget(self::CACHE_KEY);
        $this->resolved = null;
        $this->resolvedFlag = false;
    }

    public static function reset(): void
    {
        CacheFacade::forget(self::CACHE_KEY);
    }
}
