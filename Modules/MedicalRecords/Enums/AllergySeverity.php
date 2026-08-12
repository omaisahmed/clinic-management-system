<?php

declare(strict_types=1);

namespace Modules\MedicalRecords\Enums;

enum AllergySeverity: string
{
    case Mild = 'mild';
    case Moderate = 'moderate';
    case Severe = 'severe';

    public function label(): string
    {
        return ucfirst($this->value);
    }

    public function color(): string
    {
        return match ($this) {
            self::Mild => 'amber',
            self::Moderate => 'blue',
            self::Severe => 'red',
        };
    }

    public static function choices(): array
    {
        $map = [];

        foreach (self::cases() as $case) {
            $map[$case->value] = $case->label();
        }

        return $map;
    }
}
