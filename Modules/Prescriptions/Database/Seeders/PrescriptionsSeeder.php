<?php

declare(strict_types=1);

namespace Modules\Prescriptions\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Authentication\Models\User;
use Modules\Clinics\Models\Clinic;
use Modules\Medicines\Models\Medicine;
use Modules\Patients\Models\Patient;
use Modules\Prescriptions\Enums\PrescriptionStatus;
use Modules\Prescriptions\Services\PrescriptionService;
use Modules\Visits\Models\Visit;

class PrescriptionsSeeder extends Seeder
{
    public function run(): void
    {
        $clinicId = Clinic::query()->value('id');
        $doctorIds = User::query()->role('doctor')->pluck('id')->all();
        $patientIds = Patient::query()->pluck('id')->all();
        $visitIds = Visit::query()->pluck('id')->all();
        $medicineIds = Medicine::query()->pluck('id')->all();

        if ($patientIds === [] || $doctorIds === [] || $visitIds === [] || $medicineIds === []) {
            return;
        }

        $service = app(PrescriptionService::class);
        $statuses = [
            PrescriptionStatus::Active,
            PrescriptionStatus::Active,
            PrescriptionStatus::Issued,
            PrescriptionStatus::Completed,
            PrescriptionStatus::Active,
            PrescriptionStatus::Completed,
            PrescriptionStatus::Issued,
            PrescriptionStatus::Cancelled,
            PrescriptionStatus::Active,
            PrescriptionStatus::Completed,
        ];

        foreach ($statuses as $status) {
            $service->createWithItems(
                [
                    'clinic_id' => $clinicId,
                    'patient_id' => $patientIds[array_rand($patientIds)],
                    'visit_id' => $visitIds[array_rand($visitIds)],
                    'doctor_id' => $doctorIds[array_rand($doctorIds)],
                    'status' => $status->value,
                    'notes' => 'Seeded prescription.',
                ],
                $this->items($medicineIds),
            );
        }
    }

    /**
     * @param  int[]  $medicineIds
     * @return array<int, array<string, mixed>>
     */
    private function items(array $medicineIds): array
    {
        $count = random_int(1, 3);
        $items = [];

        for ($i = 0; $i < $count; $i++) {
            $medicineId = $medicineIds[array_rand($medicineIds)];
            $medicine = Medicine::query()->find($medicineId);

            $items[] = [
                'medicine_id' => $medicineId,
                'name' => $medicine->name,
                'dosage' => random_int(1, 2) . ' ' . $medicine->strength,
                'frequency' => $this->frequencies()[random_int(0, 3)],
                'duration' => random_int(3, 14) . ' days',
                'instructions' => 'Take as directed.',
            ];
        }

        return $items;
    }

    /**
     * @return string[]
     */
    private function frequencies(): array
    {
        return [
            'Once daily',
            'Twice daily',
            'Three times daily',
            'Every 8 hours',
        ];
    }
}
