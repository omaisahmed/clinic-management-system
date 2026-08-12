<?php

declare(strict_types=1);

namespace Modules\Payments\Providers;

use Modules\Core\Providers\ModuleServiceProvider;

final class PaymentsServiceProvider extends ModuleServiceProvider
{
    public function moduleName(): string
    {
        return 'Payments';
    }

    public function moduleAlias(): string
    {
        return 'payments';
    }
}
