<?php

declare(strict_types=1);

namespace Modules\Appointments\Providers;

use Modules\Core\Providers\ModuleServiceProvider;

final class AppointmentsServiceProvider extends ModuleServiceProvider
{
    public function moduleName(): string
    {
        return 'Appointments';
    }

    public function moduleAlias(): string
    {
        return 'appointments';
    }
}
