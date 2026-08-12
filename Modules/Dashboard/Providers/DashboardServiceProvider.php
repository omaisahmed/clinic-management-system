<?php

declare(strict_types=1);

namespace Modules\Dashboard\Providers;

use Modules\Core\Providers\ModuleServiceProvider;
use Modules\Dashboard\Services\DashboardService;

final class DashboardServiceProvider extends ModuleServiceProvider
{
    public function moduleName(): string
    {
        return 'Dashboard';
    }

    public function moduleAlias(): string
    {
        return 'dashboard';
    }

    public function register(): void
    {
        parent::register();

        $this->app->singleton(DashboardService::class);
    }
}
