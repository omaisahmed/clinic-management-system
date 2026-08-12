<?php

declare(strict_types=1);

namespace Modules\LabTests\Providers;

use Modules\Core\Providers\ModuleServiceProvider;

final class LabTestsServiceProvider extends ModuleServiceProvider
{
    public function moduleName(): string
    {
        return 'LabTests';
    }

    public function moduleAlias(): string
    {
        return 'lab_tests';
    }
}
