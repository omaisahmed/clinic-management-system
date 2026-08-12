<?php

declare(strict_types=1);

namespace Modules\Appointments\Enums;

enum AppointmentStatus: string
{
    case Scheduled = 'scheduled';
    case Confirmed = 'confirmed';
    case CheckedIn = 'checked_in';
    case Waiting = 'waiting';
    case InConsultation = 'in_consultation';
    case Completed = 'completed';
    case Cancelled = 'cancelled';
    case NoShow = 'no_show';

    public function label(): string
    {
        return str_replace('_', ' ', ucfirst((string) $this->value));
    }

    public function color(): string
    {
        return match ($this) {
            self::Scheduled => 'blue',
            self::Confirmed => 'primary',
            self::CheckedIn => 'teal',
            self::Waiting => 'amber',
            self::InConsultation => 'purple',
            self::Completed => 'green',
            self::Cancelled => 'red',
            self::NoShow => 'gray',
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
