<?php

declare(strict_types=1);

namespace Modules\Reports\Providers;

use Modules\Core\Providers\ModuleServiceProvider;

final class ReportsServiceProvider extends ModuleServiceProvider
{
    public function moduleName(): string
    {
        return 'Reports';
    }

    public function moduleAlias(): string
    {
        return 'reports';
    }
}
