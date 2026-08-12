<?php

declare(strict_types=1);

namespace Modules\Documents\Providers;

use Modules\Core\Providers\ModuleServiceProvider;

final class DocumentsServiceProvider extends ModuleServiceProvider
{
    public function moduleName(): string
    {
        return 'Documents';
    }

    public function moduleAlias(): string
    {
        return 'documents';
    }
}
