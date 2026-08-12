<?php

declare(strict_types=1);

namespace Modules\Patients\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Patients\Models\Patient;

class PatientSeeder extends Seeder
{
    public function run(): void
    {
        for ($i = 0; $i < 25; $i++) {
            Patient::factory()->withMedicalHistory()->create();
        }
    }
}
