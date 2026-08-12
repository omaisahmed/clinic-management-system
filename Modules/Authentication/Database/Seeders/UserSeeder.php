<?php

declare(strict_types=1);

namespace Modules\Authentication\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Modules\Authentication\Enums\Role;
use Modules\Authentication\Models\User;
use Modules\Clinics\Models\Clinic;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $clinic = Clinic::query()->first();

        $users = [
            ['name' => 'Clinic Admin', 'email' => 'admin@admin.com', 'role' => Role::ClinicAdmin],
            ['name' => 'Dr. Ahmed Khan', 'email' => 'doctor@example.com', 'role' => Role::Doctor],
            ['name' => 'Receptionist', 'email' => 'reception@example.com', 'role' => Role::Receptionist],
            ['name' => 'Nurse', 'email' => 'nurse@example.com', 'role' => Role::Nurse],
            ['name' => 'Pharmacist', 'email' => 'pharmacist@example.com', 'role' => Role::Pharmacist],
            ['name' => 'Accountant', 'email' => 'accountant@example.com', 'role' => Role::Accountant],
            ['name' => 'Lab Technician', 'email' => 'lab@example.com', 'role' => Role::LabTechnician],
        ];

        foreach ($users as $user) {
            $model = User::query()->firstOrCreate(
                ['email' => $user['email']],
                [
                    'name' => $user['name'],
                    'clinic_id' => $clinic?->id,
                    'password' => Hash::make('password'),
                    'email_verified_at' => now(),
                    'is_active' => true,
                ]
            );

            if (! $model->hasRole($user['role']->value)) {
                $model->assignRole($user['role']->value);
            }
        }
    }
}
