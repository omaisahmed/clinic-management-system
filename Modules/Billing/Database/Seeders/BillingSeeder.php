<?php

declare(strict_types=1);

namespace Modules\Billing\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Billing\Enums\InvoiceStatus;
use Modules\Billing\Services\InvoiceService;
use Modules\Clinics\Models\Clinic;
use Modules\Patients\Models\Patient;
use Modules\Visits\Models\Visit;

class BillingSeeder extends Seeder
{
    public function run(): void
    {
        $clinicId = Clinic::query()->value('id');
        $patientIds = Patient::query()->pluck('id')->all();
        $visitIds = Visit::query()->pluck('id')->all();

        if ($patientIds === []) {
            return;
        }

        $service = app(InvoiceService::class);
        $statuses = [
            InvoiceStatus::Issued,
            InvoiceStatus::Issued,
            InvoiceStatus::Issued,
            InvoiceStatus::Issued,
            InvoiceStatus::Issued,
            InvoiceStatus::Issued,
            InvoiceStatus::Issued,
            InvoiceStatus::Issued,
            InvoiceStatus::Issued,
            InvoiceStatus::Issued,
            InvoiceStatus::Issued,
            InvoiceStatus::Cancelled,
        ];

        foreach ($statuses as $index => $status) {
            $issueDaysAgo = random_int(0, 20);

            $service->createWithItems(
                [
                    'clinic_id' => $clinicId,
                    'patient_id' => $patientIds[array_rand($patientIds)],
                    'visit_id' => $visitIds !== [] ? $visitIds[array_rand($visitIds)] : null,
                    'status' => $status->value,
                    'issue_date' => today()->subDays($issueDaysAgo)->toDateString(),
                    'due_date' => today()->subDays($issueDaysAgo)->addDays(7)->toDateString(),
                    'discount' => 0,
                    'tax' => 0,
                    'notes' => 'Seeded invoice.',
                ],
                $this->items($index),
            );
        }
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    private function items(int $index): array
    {
        $consultation = [
            'description' => 'General consultation',
            'quantity' => 1,
            'unit_price' => 50.00,
        ];

        $procedures = [
            ['description' => 'Blood test', 'unit_price' => 25.00],
            ['description' => 'Urinalysis', 'unit_price' => 18.00],
            ['description' => 'X-Ray', 'unit_price' => 60.00],
            ['description' => 'Ultrasound', 'unit_price' => 80.00],
            ['description' => 'ECG', 'unit_price' => 45.00],
            ['description' => 'Wound dressing', 'unit_price' => 20.00],
            ['description' => 'IV infusion', 'unit_price' => 30.00],
            ['description' => 'Physiotherapy session', 'unit_price' => 55.00],
        ];

        $items = [$consultation];
        $items[] = $procedures[$index % count($procedures)];

        if ($index % 3 === 0) {
            $items[] = ['description' => 'Medication dispensed', 'quantity' => 1, 'unit_price' => 12.50];
        }

        return $items;
    }
}
