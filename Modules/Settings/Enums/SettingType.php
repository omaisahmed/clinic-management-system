<?php

declare(strict_types=1);

namespace Modules\Settings\Enums;

enum SettingType: string
{
    case String = 'string';
    case Textarea = 'textarea';
    case Integer = 'integer';
    case Float = 'float';
    case Boolean = 'boolean';
    case Color = 'color';
    case Json = 'json';

    public function decode(string $value): mixed
    {
        return match ($this) {
            self::Integer => (int) $value,
            self::Float => (float) $value,
            self::Boolean => filter_var($value, FILTER_VALIDATE_BOOLEAN),
            self::Json => json_decode($value, true),
            self::String, self::Textarea, self::Color => $value,
        };
    }

    public function encode(mixed $value): string
    {
        return match ($this) {
            self::Integer => (string) (int) $value,
            self::Float => (string) (float) $value,
            self::Boolean => $value ? '1' : '0',
            self::Json => is_array($value) ? json_encode($value) : (string) $value,
            self::String, self::Textarea, self::Color => (string) $value,
        };
    }

    /**
     * Input types for the settings forms.
     */
    public function inputType(): string
    {
        return match ($this) {
            self::Boolean => 'checkbox',
            self::Integer, self::Float => 'number',
            self::Color => 'color',
            self::Textarea => 'textarea',
            default => 'text',
        };
    }
}
