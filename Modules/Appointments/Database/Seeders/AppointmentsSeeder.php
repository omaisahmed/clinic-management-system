<?php

declare(strict_types=1);

namespace Modules\Appointments\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Appointments\Enums\AppointmentStatus;
use Modules\Appointments\Enums\AppointmentType;
use Modules\Appointments\Models\Appointment;
use Modules\Authentication\Models\User;
use Modules\Clinics\Models\Clinic;
use Modules\Patients\Models\Patient;

class AppointmentsSeeder extends Seeder
{
    public function run(): void
    {
        $clinicId = Clinic::query()->value('id');
        $doctorIds = User::query()->role('doctor')->pluck('id')->all();
        $patientIds = Patient::query()->pluck('id')->all();

        if ($patientIds === [] || $doctorIds === []) {
            return;
        }

        $types = AppointmentType::cases();
        $statuses = [
            AppointmentStatus::Completed,
            AppointmentStatus::Completed,
            AppointmentStatus::Completed,
            AppointmentStatus::Completed,
            AppointmentStatus::Completed,
            AppointmentStatus::Completed,
            AppointmentStatus::CheckedIn,
            AppointmentStatus::Waiting,
            AppointmentStatus::Scheduled,
            AppointmentStatus::Scheduled,
            AppointmentStatus::Scheduled,
            AppointmentStatus::Scheduled,
            AppointmentStatus::Cancelled,
            AppointmentStatus::NoShow,
            AppointmentStatus::Completed,
        ];

        foreach ($statuses as $index => $status) {
            $daysAgo = random_int(5, 30);
            $type = $types[array_rand($types)];

            Appointment::query()->create([
                'clinic_id' => $clinicId,
                'patient_id' => $patientIds[array_rand($patientIds)],
                'doctor_id' => $doctorIds[array_rand($doctorIds)],
                'appointment_type' => $type->value,
                'status' => $status->value,
                'appointment_date' => $status === AppointmentStatus::Scheduled || $status === AppointmentStatus::Waiting || $status === AppointmentStatus::CheckedIn
                    ? today()->addDays(random_int(0, 6))->toDateString()
                    : today()->subDays($daysAgo)->toDateString(),
                'start_time' => sprintf('%02d:%02d', random_int(8, 17), random_int(0, 59)),
                'duration' => 15,
                'reason' => $this->reasons()[$index % count($this->reasons())],
                'notes' => 'Seeded appointment for demo data.',
            ]);
        }
    }

    /**
     * @return string[]
     */
    private function reasons(): array
    {
        return [
            'Routine check-up',
            'Fever and cough',
            'Headache',
            'Blood pressure review',
            'Diabetes follow-up',
            'Skin rash',
            'Stomach pain',
            'Annual physical',
            'Sore throat',
            'Back pain',
            'Allergy symptoms',
            'Vaccination',
            'Eye irritation',
            'Follow-up lab results',
            'Chest congestion',
        ];
    }
}
