<?php

declare(strict_types=1);

namespace Modules\MedicalRecords\Enums;

enum ConditionStatus: string
{
    case Active = 'active';
    case Resolved = 'resolved';
    case Ongoing = 'ongoing';

    public function label(): string
    {
        return ucfirst($this->value);
    }

    public function color(): string
    {
        return match ($this) {
            self::Active => 'red',
            self::Ongoing => 'amber',
            self::Resolved => 'green',
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
