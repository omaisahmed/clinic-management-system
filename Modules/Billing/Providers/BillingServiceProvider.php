<?php

declare(strict_types=1);

namespace Modules\Billing\Providers;

use Modules\Billing\Services\InvoiceService;
use Modules\Core\Providers\ModuleServiceProvider;

final class BillingServiceProvider extends ModuleServiceProvider
{
    public function moduleName(): string
    {
        return 'Billing';
    }

    public function moduleAlias(): string
    {
        return 'billing';
    }

    public function register(): void
    {
        parent::register();

        $this->app->singleton(InvoiceService::class);
    }
}
