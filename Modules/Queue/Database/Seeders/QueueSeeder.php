<?php

declare(strict_types=1);

namespace Modules\Queue\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Appointments\Models\Appointment;
use Modules\Authentication\Models\User;
use Modules\Clinics\Models\Clinic;
use Modules\Patients\Models\Patient;
use Modules\Queue\Enums\QueueStatus;
use Modules\Queue\Models\QueueEntry;
use Modules\Queue\Services\QueueService;

class QueueSeeder extends Seeder
{
    public function run(): void
    {
        $clinicId = Clinic::query()->value('id');
        $doctorIds = User::query()->role('doctor')->pluck('id')->all();
        $patientIds = Patient::query()->pluck('id')->all();
        $scheduledAppointments = Appointment::query()
            ->whereIn('status', ['scheduled', 'waiting', 'checked_in'])
            ->get();

        if ($patientIds === [] || $doctorIds === []) {
            return;
        }

        $service = app(QueueService::class);
        $statuses = [
            QueueStatus::Waiting,
            QueueStatus::Waiting,
            QueueStatus::Waiting,
            QueueStatus::Waiting,
            QueueStatus::InProgress,
            QueueStatus::Completed,
            QueueStatus::Completed,
            QueueStatus::Waiting,
        ];

        foreach ($statuses as $index => $status) {
            $appointment = $scheduledAppointments->isNotEmpty() ? $scheduledAppointments->random() : null;

            QueueEntry::query()->create([
                'clinic_id' => $clinicId,
                'patient_id' => $patientIds[array_rand($patientIds)],
                'doctor_id' => $doctorIds[array_rand($doctorIds)],
                'appointment_id' => $appointment?->id,
                'token_number' => $service->issueToken($clinicId),
                'status' => $status->value,
                'entered_at' => now()->subMinutes(random_int(5, 120)),
                'called_at' => $status === QueueStatus::InProgress || $status === QueueStatus::Completed ? now()->subMinutes(random_int(1, 60)) : null,
                'completed_at' => $status === QueueStatus::Completed ? now()->subMinutes(random_int(1, 45)) : null,
                'notes' => 'Seeded queue entry.',
            ]);
        }
    }
}
