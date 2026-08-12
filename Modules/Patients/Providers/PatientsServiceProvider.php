<?php

declare(strict_types=1);

namespace Modules\Patients\Providers;

use Illuminate\Support\Facades\Gate;
use Modules\Core\Providers\ModuleServiceProvider;
use Modules\Patients\Models\Patient;
use Modules\Patients\Policies\PatientPolicy;
use Modules\Patients\Services\PatientService;

final class PatientsServiceProvider extends ModuleServiceProvider
{
    public function moduleName(): string
    {
        return 'Patients';
    }

    public function moduleAlias(): string
    {
        return 'patients';
    }

    public function register(): void
    {
        parent::register();

        $this->app->singleton(PatientService::class);
    }

    public function boot(): void
    {
        parent::boot();

        Gate::policy(Patient::class, PatientPolicy::class);
    }
}
