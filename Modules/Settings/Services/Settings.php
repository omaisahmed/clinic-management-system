<?php

declare(strict_types=1);

namespace Modules\Settings\Services;

use Illuminate\Contracts\Cache\Repository as Cache;
use Illuminate\Support\Facades\Crypt;
use Modules\Clinics\Models\Clinic;
use Modules\Clinics\Models\ClinicSetting;
use Modules\Clinics\Services\ClinicContext;
use Modules\Settings\Enums\SettingType;

/**
 * Reads and writes clinic settings.
 *
 * Values are cached per clinic. Encrypted values (API keys, credentials)
 * are stored encrypted in the database and only decrypted on read.
 */
class Settings
{
    /**
     * clinic.* keys are backed by columns on the clinics table.
     */
    public const CLINIC_COLUMNS = [
        'name',
        'tagline',
        'description',
        'phone',
        'whatsapp',
        'email',
        'website',
        'address',
        'city',
        'state',
        'country',
        'postal_code',
        'registration_number',
        'tax_number',
        'currency',
        'timezone',
    ];

    public function __construct(
        private readonly ClinicContext $clinicContext,
        private readonly Cache $cache,
    ) {
    }

    public function get(string $key, mixed $default = null): mixed
    {
        $clinic = $this->clinicContext->current();

        if (! $clinic) {
            return $default;
        }

        $settings = $this->settingsFor($clinic);

        if (! array_key_exists($key, $settings)) {
            return $default;
        }

        return $settings[$key]['value'];
    }

    /**
     * @return array<string, array{value: mixed, type: SettingType, is_encrypted: bool}>
     */
    public function all(?Clinic $clinic = null): array
    {
        return $this->settingsFor($clinic ?? $this->clinicContext->current());
    }

    public function set(string $key, mixed $value, SettingType $type = SettingType::String, bool $encrypted = false): void
    {
        $clinic = $this->clinicContext->current();

        if (! $clinic) {
            return;
        }

        $storedValue = $encrypted ? Crypt::encryptString((string) $value) : $value;

        ClinicSetting::query()->updateOrCreate(
            ['clinic_id' => $clinic->id, 'key' => $key],
            [
                'group' => str($key)->before('.')->value(),
                'value' => is_array($storedValue) ? json_encode($storedValue) : $storedValue,
                'type' => $type->value,
                'is_encrypted' => $encrypted,
            ],
        );

        $this->cache->forget($this->cacheKey($clinic));
    }

    public function flush(?Clinic $clinic = null): void
    {
        $clinic ??= $this->clinicContext->current();

        if ($clinic) {
            $this->cache->forget($this->cacheKey($clinic));
        }
    }

    /**
     * @param  array<string, mixed>  $values  keyed by setting key
     */
    public function setMany(array $values): void
    {
        foreach ($values as $key => $value) {
            $this->set($key, $value);
        }
    }

    /**
     * @return array<string, array{value: mixed, type: SettingType, is_encrypted: bool}>
     */
    private function settingsFor(?Clinic $clinic): array
    {
        if (! $clinic) {
            return [];
        }

        $key = $this->cacheKey($clinic);

        return $this->cache->remember($key, now()->addMinutes(15), function () use ($clinic): array {
            $settings = $this->clinicColumnSettings($clinic);

            $clinic->settings()->get()->each(function (ClinicSetting $setting) use (&$settings): void {
                $type = SettingType::tryFrom($setting->type) ?? SettingType::String;

                $settings[$setting->key] = [
                    'value' => $this->decodeValue($setting->value, $type, (bool) $setting->is_encrypted),
                    'type' => $type,
                    'is_encrypted' => (bool) $setting->is_encrypted,
                ];
            });

            return $settings;
        });
    }

    /**
     * Seed the settings bag with the clinic group, which lives in columns on
     * the clinics table rather than the clinic_settings table.
     *
     * @return array<string, array{value: mixed, type: SettingType, is_encrypted: bool}>
     */
    private function clinicColumnSettings(Clinic $clinic): array
    {
        $settings = [];

        foreach (self::CLINIC_COLUMNS as $column) {
            $settings['clinic.' . $column] = [
                'value' => $clinic->{$column},
                'type' => SettingType::String,
                'is_encrypted' => false,
            ];
        }

        return $settings;
    }

    private function decodeValue(?string $raw, SettingType $type, bool $encrypted): mixed
    {
        if ($raw === null) {
            return null;
        }

        $value = $encrypted ? Crypt::decryptString($raw) : $raw;

        return $type->decode($value);
    }

    private function cacheKey(Clinic $clinic): string
    {
        return 'cms.settings.' . $clinic->id;
    }
}
