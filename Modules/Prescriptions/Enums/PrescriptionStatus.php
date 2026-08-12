<?php

declare(strict_types=1);

namespace Modules\Prescriptions\Enums;

enum PrescriptionStatus: string
{
    case Active = 'active';
    case Issued = 'issued';
    case Completed = 'completed';
    case Cancelled = 'cancelled';

    public function label(): string
    {
        return ucfirst($this->value);
    }

    public function color(): string
    {
        return match ($this) {
            self::Active => 'primary',
            self::Issued => 'blue',
            self::Completed => 'green',
            self::Cancelled => 'red',
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
