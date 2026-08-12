<?php

declare(strict_types=1);

namespace Modules\Prescriptions\Providers;

use Modules\Core\Providers\ModuleServiceProvider;
use Modules\Prescriptions\Services\PrescriptionService;

final class PrescriptionsServiceProvider extends ModuleServiceProvider
{
    public function moduleName(): string
    {
        return 'Prescriptions';
    }

    public function moduleAlias(): string
    {
        return 'prescriptions';
    }

    public function register(): void
    {
        parent::register();

        $this->app->singleton(PrescriptionService::class);
    }
}
