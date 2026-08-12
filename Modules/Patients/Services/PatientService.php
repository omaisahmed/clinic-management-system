<?php

declare(strict_types=1);

namespace Modules\Patients\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Modules\Patients\Models\Patient;

class PatientService
{
    /**
     * @param  array<string, mixed>  $data
     */
    public function create(array $data): Patient
    {
        $data['clinic_id'] = $data['clinic_id'] ?? current_clinic()?->id;
        $data['patient_number'] = $this->generatePatientNumber();

        return Patient::query()->create($data);
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public function update(Patient $patient, array $data): Patient
    {
        $patient->update($data);

        return $patient;
    }

    public function generatePatientNumber(): string
    {
        $prefix = Str::upper((string) setting('patient.prefix', 'PT'));

        return DB::transaction(function () use ($prefix): string {
            $last = Patient::query()
                ->withTrashed()
                ->where('patient_number', 'like', $prefix . '-%')
                ->orderByDesc('patient_number')
                ->value('patient_number');

            $sequence = 1;

            if ($last !== null) {
                $sequence = ((int) Str::afterLast($last, '-')) + 1;
            }

            return $prefix . '-' . str_pad((string) $sequence, 6, '0', STR_PAD_LEFT);
        });
    }
}
