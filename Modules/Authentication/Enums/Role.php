<?php

declare(strict_types=1);

namespace Modules\Authentication\Enums;

enum Role: string
{
    case SuperAdmin = 'super_admin';
    case ClinicAdmin = 'clinic_admin';
    case Doctor = 'doctor';
    case Receptionist = 'receptionist';
    case Nurse = 'nurse';
    case Pharmacist = 'pharmacist';
    case Accountant = 'accountant';
    case LabTechnician = 'lab_technician';

    public function label(): string
    {
        return match ($this) {
            self::SuperAdmin => 'Super Admin',
            self::ClinicAdmin => 'Clinic Admin',
            self::Doctor => 'Doctor',
            self::Receptionist => 'Receptionist',
            self::Nurse => 'Nurse',
            self::Pharmacist => 'Pharmacist',
            self::Accountant => 'Accountant',
            self::LabTechnician => 'Lab Technician',
        };
    }

    /**
     * The guard name used by spatie/laravel-permission.
     */
    public function guard(): string
    {
        return 'web';
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
