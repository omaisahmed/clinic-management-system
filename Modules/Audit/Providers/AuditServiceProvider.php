<?php

declare(strict_types=1);

namespace Modules\Audit\Providers;

use Modules\Core\Providers\ModuleServiceProvider;

final class AuditServiceProvider extends ModuleServiceProvider
{
    public function moduleName(): string
    {
        return 'Audit';
    }

    public function moduleAlias(): string
    {
        return 'audit';
    }
}
