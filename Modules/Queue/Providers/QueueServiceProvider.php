<?php

declare(strict_types=1);

namespace Modules\Queue\Providers;

use Modules\Core\Providers\ModuleServiceProvider;
use Modules\Queue\Services\QueueService;

final class QueueServiceProvider extends ModuleServiceProvider
{
    public function moduleName(): string
    {
        return 'Queue';
    }

    public function moduleAlias(): string
    {
        return 'queue';
    }

    public function register(): void
    {
        parent::register();

        $this->app->singleton(QueueService::class);
    }
}
