<?php

declare(strict_types=1);

namespace Modules\Visits\Enums;

enum VisitStatus: string
{
    case InProgress = 'in_progress';
    case Completed = 'completed';
    case Cancelled = 'cancelled';

    public function label(): string
    {
        return ucfirst(str_replace('_', ' ', $this->value));
    }

    public function color(): string
    {
        return match ($this) {
            self::InProgress => 'amber',
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
