<?php

declare(strict_types=1);

namespace Modules\Patients\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\Patients\Enums\BloodGroup;
use Modules\Patients\Enums\Gender;
use Modules\Patients\Enums\MaritalStatus;
use Modules\Patients\Models\Patient;
use Modules\Patients\Services\PatientService;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\Modules\Patients\Models\Patient>
 */
class PatientFactory extends Factory
{
    protected $model = Patient::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $gender = $this->faker->randomElement(Gender::cases())->value;

        return [
            'clinic_id' => current_clinic()?->id,
            'patient_number' => app(PatientService::class)->generatePatientNumber(),
            'first_name' => $this->faker->firstName(),
            'last_name' => $this->faker->lastName(),
            'gender' => $gender,
            'date_of_birth' => $this->faker->dateTimeBetween('-80 years', '-1 year')->format('Y-m-d'),
            'blood_group' => $this->faker->randomElement(BloodGroup::cases())->value,
            'phone' => $this->faker->unique()->numerify('+1###-###-####'),
            'whatsapp' => $this->faker->numerify('+1###-###-####'),
            'email' => $this->faker->unique()->safeEmail(),
            'cnic' => $this->faker->numerify('#####-#######-#'),
            'address' => $this->faker->streetAddress(),
            'city' => $this->faker->city(),
            'country' => 'Pakistan',
            'emergency_contact' => $this->faker->name(),
            'emergency_contact_phone' => $this->faker->numerify('+1###-###-####'),
            'occupation' => $this->faker->jobTitle(),
            'marital_status' => $this->faker->randomElement(MaritalStatus::cases())->value,
            'photo_path' => null,
            'notes' => $this->faker->optional(0.3)->sentence(),
        ];
    }

    /**
     * @return \Illuminate\Database\Eloquent\Factories\Factory<static>
     */
    public function withMedicalHistory(): Factory
    {
        return $this->afterCreating(function (Patient $patient): void {
            $faker = $this->faker;

            $patient->allergies()->createMany([
                ['allergy' => $faker->randomElement(['Penicillin', 'Sulfa drugs', 'Latex', 'Peanuts', 'Codeine']), 'reaction' => $faker->randomElement(['Hives', 'Swelling', 'Rash']), 'severity' => 'mild'],
                ['allergy' => $faker->randomElement(['Aspirin', 'Dust', 'Pollen', 'Shellfish']), 'reaction' => $faker->randomElement(['Rash', 'Itching']), 'severity' => $faker->randomElement(['moderate', 'severe'])],
            ]);

            $patient->conditions()->createMany([
                ['condition' => $faker->randomElement(['Hypertension', 'Diabetes Type 2', 'Asthma']), 'status' => 'active', 'diagnosis_date' => $faker->dateTimeBetween('-5 years', 'now')->format('Y-m-d')],
                ['condition' => $faker->randomElement(['Migraine', 'Arthritis']), 'status' => 'ongoing'],
            ]);

            $patient->surgeries()->create([
                'surgery' => $faker->randomElement(['Appendectomy', 'Tonsillectomy', 'C-section']),
                'date' => $faker->dateTimeBetween('-10 years', '-1 year')->format('Y-m-d'),
            ]);

            $patient->familyHistories()->create([
                'condition' => $faker->randomElement(['Heart disease', 'Diabetes', 'Cancer']),
                'relation' => $faker->randomElement(['Father', 'Mother', 'Sibling']),
            ]);

            $patient->socialHistory()->create([
                'smoking' => $faker->boolean(25),
                'alcohol' => $faker->boolean(20),
                'occupation' => $faker->jobTitle(),
            ]);
        });
    }
}
