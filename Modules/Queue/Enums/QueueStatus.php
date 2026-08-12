<?php

declare(strict_types=1);

namespace Modules\Queue\Enums;

enum QueueStatus: string
{
    case Waiting = 'waiting';
    case InProgress = 'in_progress';
    case Completed = 'completed';
    case Skipped = 'skipped';
    case Cancelled = 'cancelled';

    public function label(): string
    {
        return ucfirst(str_replace('_', ' ', (string) $this->value));
    }

    public function color(): string
    {
        return match ($this) {
            self::Waiting => 'blue',
            self::InProgress => 'amber',
            self::Completed => 'green',
            self::Skipped => 'gray',
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
