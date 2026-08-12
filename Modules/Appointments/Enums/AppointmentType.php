<?php

declare(strict_types=1);

namespace Modules\Appointments\Enums;

enum AppointmentType: string
{
    case NewConsultation = 'new_consultation';
    case FollowUp = 'follow_up';
    case Emergency = 'emergency';
    case Procedure = 'procedure';
    case Lab = 'lab';
    case Other = 'other';

    public function label(): string
    {
        return match ($this) {
            self::NewConsultation => 'New Consultation',
            self::FollowUp => 'Follow-up',
            self::Emergency => 'Emergency',
            self::Procedure => 'Procedure',
            self::Lab => 'Lab',
            self::Other => 'Other',
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
