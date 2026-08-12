<?php

declare(strict_types=1);

namespace Modules\LabTests\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Clinics\Models\Clinic;
use Modules\LabTests\Enums\LabTestStatus;
use Modules\LabTests\Models\LabTest;
use Modules\Patients\Models\Patient;
use Modules\Visits\Models\Visit;

class LabTestsSeeder extends Seeder
{
    public function run(): void
    {
        $clinicId = Clinic::query()->value('id');
        $patientIds = Patient::query()->pluck('id')->all();
        $visitIds = Visit::query()->pluck('id')->all();

        if ($patientIds === []) {
            return;
        }

        $catalog = [
            ['test_name' => 'Complete Blood Count', 'category' => 'Hematology', 'price' => 25.00, 'sample_type' => 'Blood', 'status' => LabTestStatus::Completed],
            ['test_name' => 'Blood Glucose (Fasting)', 'category' => 'Biochemistry', 'price' => 15.00, 'sample_type' => 'Blood', 'status' => LabTestStatus::Completed],
            ['test_name' => 'HbA1c', 'category' => 'Biochemistry', 'price' => 30.00, 'sample_type' => 'Blood', 'status' => LabTestStatus::Processing],
            ['test_name' => 'Urinalysis', 'category' => 'Urinalysis', 'price' => 18.00, 'sample_type' => 'Urine', 'status' => LabTestStatus::Requested],
            ['test_name' => 'Lipid Profile', 'category' => 'Biochemistry', 'price' => 35.00, 'sample_type' => 'Blood', 'status' => LabTestStatus::Completed],
            ['test_name' => 'Liver Function Test', 'category' => 'Biochemistry', 'price' => 40.00, 'sample_type' => 'Blood', 'status' => LabTestStatus::Processing],
            ['test_name' => 'Kidney Function Test', 'category' => 'Biochemistry', 'price' => 40.00, 'sample_type' => 'Blood', 'status' => LabTestStatus::Requested],
            ['test_name' => 'Malaria Rapid Test', 'category' => 'Microbiology', 'price' => 12.00, 'sample_type' => 'Blood', 'status' => LabTestStatus::Completed],
            ['test_name' => 'Stool Examination', 'category' => 'Microbiology', 'price' => 16.00, 'sample_type' => 'Stool', 'status' => LabTestStatus::Completed],
            ['test_name' => 'Thyroid Function Test', 'category' => 'Endocrinology', 'price' => 50.00, 'sample_type' => 'Blood', 'status' => LabTestStatus::Requested],
            ['test_name' => 'Pregnancy Test', 'category' => 'Immunology', 'price' => 10.00, 'sample_type' => 'Urine', 'status' => LabTestStatus::Completed],
            ['test_name' => 'Rapid Diagnostic Test - Dengue', 'category' => 'Immunology', 'price' => 20.00, 'sample_type' => 'Blood', 'status' => LabTestStatus::Processing],
        ];

        foreach ($catalog as $index => $test) {
            $resultReady = in_array($test['status'], [LabTestStatus::Completed], true);

            LabTest::query()->create([
                'clinic_id' => $clinicId,
                'patient_id' => $patientIds[array_rand($patientIds)],
                'visit_id' => $visitIds !== [] ? $visitIds[array_rand($visitIds)] : null,
                'test_name' => $test['test_name'],
                'category' => $test['category'],
                'price' => $test['price'],
                'status' => $test['status']->value,
                'sample_type' => $test['sample_type'],
                'collection_date' => $resultReady || $test['status'] !== LabTestStatus::Requested ? today()->subDays(random_int(0, 15))->toDateString() : null,
                'result' => $resultReady ? $this->resultFor($index) : null,
                'result_date' => $resultReady ? today()->subDays(random_int(0, 10))->toDateString() : null,
                'notes' => 'Seeded lab test.',
            ]);
        }
    }

    private function resultFor(int $index): string
    {
        $results = [
            'WBC 6.5, RBC 4.8, Hb 14.2, PLT 250k',
            'Fasting glucose 92 mg/dL',
            'HbA1c 5.8%',
            'Normal, clear, no protein',
            'Total cholesterol 180, LDL 110, HDL 55',
            'AST 24, ALT 28, ALP 80',
            'Creatinine 0.9, BUN 14',
            'Negative',
            'No ova or parasites detected',
            'TSH 2.1, T4 8.5',
            'Negative',
            'Negative',
        ];

        return $results[$index % count($results)];
    }
}
