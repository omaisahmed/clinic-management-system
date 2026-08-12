<?php

declare(strict_types=1);

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Modules\Appointments\Database\Seeders\AppointmentsSeeder;
use Modules\Authentication\Database\Seeders\RolePermissionSeeder;
use Modules\Authentication\Database\Seeders\UserSeeder;
use Modules\Billing\Database\Seeders\BillingSeeder;
use Modules\Clinics\Database\Seeders\ClinicSeeder;
use Modules\Documents\Database\Seeders\DocumentsSeeder;
use Modules\Expenses\Database\Seeders\ExpensesSeeder;
use Modules\LabTests\Database\Seeders\LabTestsSeeder;
use Modules\Medicines\Database\Seeders\MedicinesSeeder;
use Modules\Patients\Database\Seeders\PatientSeeder;
use Modules\Payments\Database\Seeders\PaymentsSeeder;
use Modules\Prescriptions\Database\Seeders\PrescriptionsSeeder;
use Modules\Queue\Database\Seeders\QueueSeeder;
use Modules\Visits\Database\Seeders\VisitsSeeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        $this->call([
            ClinicSeeder::class,
            RolePermissionSeeder::class,
            UserSeeder::class,
            PatientSeeder::class,
            MedicinesSeeder::class,
            AppointmentsSeeder::class,
            VisitsSeeder::class,
            QueueSeeder::class,
            PrescriptionsSeeder::class,
            LabTestsSeeder::class,
            DocumentsSeeder::class,
            BillingSeeder::class,
            PaymentsSeeder::class,
            ExpensesSeeder::class,
        ]);
    }
}
