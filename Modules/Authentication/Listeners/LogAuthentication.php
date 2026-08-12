<?php

declare(strict_types=1);

namespace Modules\Authentication\Listeners;

use Illuminate\Auth\Events\Login;
use Illuminate\Auth\Events\Logout;
use Modules\Audit\Facades\Audit;

class LogAuthentication
{
    public function handle(Login|Logout $event): void
    {
        $action = $event instanceof Login ? 'User Login' : 'User Logout';

        Audit::record(
            action: $action,
            module: 'authentication',
            userId: $event->user?->getKey(),
            changes: [
                'ip' => request()->ip(),
                'user_agent' => request()->userAgent(),
            ],
        );
    }
}
