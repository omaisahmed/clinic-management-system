<?php

declare(strict_types=1);

namespace Modules\Clinics\Providers;

use Modules\Clinics\Services\ClinicContext;
use Modules\Core\Providers\ModuleServiceProvider;

final class ClinicsServiceProvider extends ModuleServiceProvider
{
    public function moduleName(): string
    {
        return 'Clinics';
    }

    public function moduleAlias(): string
    {
        return 'clinics';
    }

    public function register(): void
    {
        parent::register();

        $this->app->singleton(ClinicContext::class);
    }
}
