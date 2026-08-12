<?php

declare(strict_types=1);

/**
 * Shared helpers available to every module.
 *
 * Kept deliberately small — prefer services, contracts and facades over
 * scattering logic through global helpers.
 */

use Modules\Clinics\Services\ClinicContext;
use Modules\Settings\Services\Settings;

if (! function_exists('module_path')) {
    /**
     * Absolute path to a module directory.
     */
    function module_path(string $module, string $path = ''): string
    {
        return base_path('Modules/' . $module) . ($path !== '' ? '/' . ltrim($path, '/') : '');
    }
}

if (! function_exists('current_clinic')) {
    /**
     * The clinic the application is currently operating for.
     */
    function current_clinic(): ?\Modules\Clinics\Models\Clinic
    {
        return app(ClinicContext::class)->current();
    }
}

if (! function_exists('setting')) {
    /**
     * Read a setting value for the current clinic.
     *
     * Usage: setting('clinic.name') or setting('clinic.name', 'Fallback').
     */
    function setting(string $key, mixed $default = null): mixed
    {
        return app(Settings::class)->get($key, $default);
    }
}

if (! function_exists('money')) {
    /**
     * Format a number using the current clinic currency.
     */
    function money(float|int|string|null $amount): string
    {
        $currency = setting('clinic.currency', 'USD');

        return number_format((float) $amount, 2) . ' ' . $currency;
    }
}
