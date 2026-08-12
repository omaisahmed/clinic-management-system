<?php

declare(strict_types=1);

namespace Modules\Clinics\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Modules\Clinics\Services\ClinicContext;
use Symfony\Component\HttpFoundation\Response;

/**
 * Apply the resolved clinic's timezone so dates render in the clinic's
 * local time across the entire application.
 */
class ApplyClinicTimezone
{
    public function __construct(private readonly ClinicContext $clinicContext)
    {
    }

    public function handle(Request $request, Closure $next): Response
    {
        try {
            $clinic = $this->clinicContext->current();
        } catch (\Throwable) {
            $clinic = null;
        }

        if ($clinic?->timezone) {
            date_default_timezone_set($clinic->timezone);
            config(['app.timezone' => $clinic->timezone]);
        }

        return $next($request);
    }
}
