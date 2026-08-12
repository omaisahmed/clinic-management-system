<?php

declare(strict_types=1);

namespace Modules\Expenses\Providers;

use Modules\Core\Providers\ModuleServiceProvider;

final class ExpensesServiceProvider extends ModuleServiceProvider
{
    public function moduleName(): string
    {
        return 'Expenses';
    }

    public function moduleAlias(): string
    {
        return 'expenses';
    }
}
