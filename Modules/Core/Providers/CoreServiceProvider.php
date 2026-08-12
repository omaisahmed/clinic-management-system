<?php

declare(strict_types=1);

namespace Modules\Core\Providers;

use Illuminate\Support\ServiceProvider;

final class CoreServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        require_once __DIR__ . '/../Support/helpers.php';
    }

    public function boot(): void
    {
        //
    }
}
