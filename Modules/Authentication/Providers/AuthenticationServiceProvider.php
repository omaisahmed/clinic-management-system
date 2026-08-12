<?php

declare(strict_types=1);

namespace Modules\Authentication\Providers;

use Modules\Authentication\Models\User;
use Modules\Authentication\Policies\UserPolicy;
use Modules\Core\Providers\ModuleServiceProvider;

final class AuthenticationServiceProvider extends ModuleServiceProvider
{
    public function moduleName(): string
    {
        return 'Authentication';
    }

    public function moduleAlias(): string
    {
        return 'authentication';
    }

    public function boot(): void
    {
        parent::boot();

        \Illuminate\Support\Facades\Gate::policy(User::class, UserPolicy::class);

        \Illuminate\Support\Facades\Event::listen(
            \Illuminate\Auth\Events\Login::class,
            \Modules\Authentication\Listeners\LogAuthentication::class,
        );

        \Illuminate\Support\Facades\Event::listen(
            \Illuminate\Auth\Events\Logout::class,
            \Modules\Authentication\Listeners\LogAuthentication::class,
        );
    }
}
