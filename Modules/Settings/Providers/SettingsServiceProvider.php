<?php

declare(strict_types=1);

namespace Modules\Settings\Providers;

use Modules\Core\Providers\ModuleServiceProvider;
use Modules\Settings\Services\Settings;

final class SettingsServiceProvider extends ModuleServiceProvider
{
    public function moduleName(): string
    {
        return 'Settings';
    }

    public function moduleAlias(): string
    {
        return 'settings';
    }

    public function register(): void
    {
        parent::register();

        $this->app->singleton(Settings::class);
    }
}
