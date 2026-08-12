<?php

declare(strict_types=1);

namespace Modules\Visits\Providers;

use Modules\Core\Providers\ModuleServiceProvider;
use Modules\Visits\Services\VisitService;

final class VisitsServiceProvider extends ModuleServiceProvider
{
    public function moduleName(): string
    {
        return 'Visits';
    }

    public function moduleAlias(): string
    {
        return 'visits';
    }

    public function register(): void
    {
        parent::register();

        $this->app->singleton(VisitService::class);
    }
}
