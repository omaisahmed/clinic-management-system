<?php

declare(strict_types=1);

namespace Modules\MedicalRecords\Providers;

use Modules\Core\Providers\ModuleServiceProvider;
use Modules\MedicalRecords\Services\MedicalHistoryService;

final class MedicalRecordsServiceProvider extends ModuleServiceProvider
{
    public function moduleName(): string
    {
        return 'MedicalRecords';
    }

    public function moduleAlias(): string
    {
        return 'medical-records';
    }

    public function register(): void
    {
        parent::register();

        $this->app->singleton(MedicalHistoryService::class);
    }
}
