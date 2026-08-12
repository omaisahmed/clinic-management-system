<?php

declare(strict_types=1);

namespace Modules\Core\Providers;

use Illuminate\Support\ServiceProvider;

/**
 * Base service provider for every module.
 *
 * Automatically wires up the module's migrations, views, translations,
 * routes and configuration so individual modules only declare where
 * their pieces live.
 */
abstract class ModuleServiceProvider extends ServiceProvider
{
    /**
     * The human readable module name, also used as the directory name.
     */
    abstract public function moduleName(): string;

    /**
     * A short namespace alias used for views/translations/config, e.g. "patients".
     */
    abstract public function moduleAlias(): string;

    public function boot(): void
 {
        $base = $this->moduleBasePath();

        $this->loadMigrationsFrom($base . '/Database/migrations');

        if (is_dir($base . '/Resources/views')) {
            $this->loadViewsFrom($base . '/Resources/views', $this->moduleAlias());
        }

        if (is_dir($base . '/Resources/lang')) {
            $this->loadTranslationsFrom($base . '/Resources/lang', $this->moduleAlias());
        }

        $routes = $base . '/routes/web.php';
        if (is_file($routes)) {
            $this->loadRoutesFrom($routes);
        }

        $config = $base . '/config.php';
        if (is_file($config)) {
            $this->mergeConfigFrom($config, $this->moduleAlias());
        }

        $commands = $base . '/Database/Commands';
        if (is_dir($commands)) {
            $this->commands(array_map(
                static fn (string $file): string => (string) realpath($commands . '/' . $file),
                array_filter(scandir($commands), static fn (string $file): bool => str_ends_with($file, '.php')),
            ));
        }
    }

    public function moduleBasePath(): string
    {
        return base_path('Modules/' . $this->moduleName());
    }

    public function modulePath(string $path = ''): string
    {
        return $this->moduleBasePath() . ($path !== '' ? '/' . ltrim($path, '/') : '');
    }
}
