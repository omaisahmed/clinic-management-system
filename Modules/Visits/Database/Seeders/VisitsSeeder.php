<?php

declare(strict_types=1);

namespace Modules\Visits\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Appointments\Models\Appointment;
use Modules\Authentication\Models\User;
use Modules\Clinics\Models\Clinic;
use Modules\Patients\Models\Patient;
use Modules\Visits\Enums\VisitStatus;
use Modules\Visits\Services\VisitService;

class VisitsSeeder extends Seeder
{
    public function run(): void
    {
        $clinicId = Clinic::query()->value('id');
        $doctorIds = User::query()->role('doctor')->pluck('id')->all();
        $patientIds = Patient::query()->pluck('id')->all();
        $completedAppointmentIds = Appointment::query()
            ->where('status', 'completed')
            ->pluck('id')
            ->all();

        if ($patientIds === [] || $doctorIds === []) {
            return;
        }

        $service = app(VisitService::class);

        for ($i = 0; $i < 20; $i++) {
            $daysAgo = random_int(0, 25);
            $status = $daysAgo === 0 ? VisitStatus::InProgress : VisitStatus::Completed;

            $service->create([
                'clinic_id' => $clinicId,
                'patient_id' => $patientIds[array_rand($patientIds)],
                'doctor_id' => $doctorIds[array_rand($doctorIds)],
                'appointment_id' => $completedAppointmentIds !== [] ? $completedAppointmentIds[array_rand($completedAppointmentIds)] : null,
                'visit_date' => today()->subDays($daysAgo)->toDateString(),
                'status' => $status->value,
                'chief_complaint' => $this->complaints()[$i % count($this->complaints())],
                'diagnosis' => $this->diagnoses()[$i % count($this->diagnoses())],
                'notes' => 'Seeded visit.',
                'temperature' => round(36.0 + random_int(0, 25) / 10, 1),
                'blood_pressure' => random_int(100, 160) . '/' . random_int(60, 100),
                'heart_rate' => random_int(60, 110),
                'respiratory_rate' => random_int(12, 22),
                'weight' => round(50 + random_int(0, 40) + random_int(0, 9) / 10, 1),
                'height' => round(150 + random_int(0, 30), 1),
            ]);
        }
    }

    /**
     * @return string[]
     */
    private function complaints(): array
    {
        return [
            'Fever for three days',
            'Persistent dry cough',
            'Sharp headache',
            'Chest tightness',
            'Abdominal pain',
            'Fatigue and dizziness',
            'Skin rash on arms',
            'Joint pain',
            'Sore throat',
            'Shortness of breath',
            'Lower back pain',
            'Earache',
            'Nausea and vomiting',
            'Palpitations',
            'Swollen ankles',
            'Recurring migraines',
            'Eye redness',
            'Insomnia',
            'Leg cramps',
            'Loss of appetite',
        ];
    }

    /**
     * @return string[]
     */
    private function diagnoses(): array
    {
        return [
            'Viral infection',
            'Upper respiratory tract infection',
            'Migraine',
            'Hypertension',
            'Gastroenteritis',
            'Anemia',
            'Allergic dermatitis',
            'Osteoarthritis',
            'Pharyngitis',
            'Asthma',
            'Musculoskeletal strain',
            'Otitis media',
            'Food poisoning',
            'Cardiac arrhythmia',
            'Edema',
            'Tension headache',
            'Conjunctivitis',
            'Sleep disorder',
            'Electrolyte imbalance',
            'Peptic ulcer disease',
        ];
    }
}
