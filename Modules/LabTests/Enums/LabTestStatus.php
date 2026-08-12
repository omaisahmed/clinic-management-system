<?php

declare(strict_types=1);

namespace Modules\LabTests\Enums;

enum LabTestStatus: string
{
    case Requested = 'requested';
    case Collected = 'collected';
    case Processing = 'processing';
    case Completed = 'completed';
    case Cancelled = 'cancelled';

    public function label(): string
    {
        return ucfirst(str_replace('_', ' ', $this->value));
    }

    public function color(): string
    {
        return match ($this) {
            self::Requested => 'blue',
            self::Collected => 'amber',
            self::Processing => 'purple',
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
