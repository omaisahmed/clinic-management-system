<?php

declare(strict_types=1);

namespace Modules\Patients\Enums;

enum BloodGroup: string
{
    case APositive = 'A+';
    case ANegative = 'A-';
    case BPositive = 'B+';
    case BNegative = 'B-';
    case ABPositive = 'AB+';
    case ABNegative = 'AB-';
    case OPositive = 'O+';
    case ONegative = 'O-';

    public static function choices(): array
    {
        $map = [];

        foreach (self::cases() as $case) {
            $map[$case->value] = $case->value;
        }

        return $map;
    }
}
