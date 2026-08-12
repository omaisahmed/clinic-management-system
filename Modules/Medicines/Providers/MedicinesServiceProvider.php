<?php

declare(strict_types=1);

namespace Modules\Medicines\Providers;

use Modules\Core\Providers\ModuleServiceProvider;

final class MedicinesServiceProvider extends ModuleServiceProvider
{
    public function moduleName(): string
    {
        return 'Medicines';
    }

    public function moduleAlias(): string
    {
        return 'medicines';
    }
}
