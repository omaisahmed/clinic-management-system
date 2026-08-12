<?php

declare(strict_types=1);

namespace Modules\MedicalRecords\Services;

use Modules\MedicalRecords\Models\PatientAllergy;
use Modules\MedicalRecords\Models\PatientCondition;
use Modules\MedicalRecords\Models\PatientFamilyHistory;
use Modules\MedicalRecords\Models\PatientSocialHistory;
use Modules\MedicalRecords\Models\PatientSurgery;
use Modules\Patients\Models\Patient;

/**
 * Owns the structured medical history records attached to a patient.
 */
class MedicalHistoryService
{
    public function create(Patient $patient, string $type, array $data): mixed
    {
        return match ($type) {
            'allergies' => $patient->allergies()->create([
                'allergy' => $data['allergy'],
                'reaction' => $data['reaction'] ?? null,
                'severity' => $data['severity'] ?? 'mild',
                'notes' => $data['notes'] ?? null,
            ]),
            'conditions' => $patient->conditions()->create([
                'condition' => $data['condition'],
                'diagnosis_date' => $data['diagnosis_date'] ?? null,
                'status' => $data['status'] ?? 'active',
                'notes' => $data['notes'] ?? null,
            ]),
            'surgeries' => $patient->surgeries()->create([
                'surgery' => $data['surgery'],
                'date' => $data['date'] ?? null,
                'notes' => $data['notes'] ?? null,
            ]),
            'family' => $patient->familyHistories()->create([
                'condition' => $data['condition'],
                'relation' => $data['relation'] ?? null,
                'notes' => $data['notes'] ?? null,
            ]),
            'social' => $this->upsertSocial($patient, $data),
            default => throw new \InvalidArgumentException("Unknown medical history type: {$type}"),
        };
    }

    public function delete(string $type, mixed $id): bool
    {
        $model = match ($type) {
            'allergies' => PatientAllergy::class,
            'conditions' => PatientCondition::class,
            'surgeries' => PatientSurgery::class,
            'family' => PatientFamilyHistory::class,
            default => throw new \InvalidArgumentException("Unknown medical history type: {$type}"),
        };

        return (bool) $model::query()->findOrFail($id)->delete();
    }

    /**
     * @param  array<string, mixed>  $data
     */
    private function upsertSocial(Patient $patient, array $data): PatientSocialHistory
    {
        return PatientSocialHistory::query()->updateOrCreate(
            ['patient_id' => $patient->id],
            [
                'smoking' => (bool) ($data['smoking'] ?? false),
                'alcohol' => (bool) ($data['alcohol'] ?? false),
                'occupation' => $data['occupation'] ?? null,
                'notes' => $data['notes'] ?? null,
            ],
        );
    }
}
