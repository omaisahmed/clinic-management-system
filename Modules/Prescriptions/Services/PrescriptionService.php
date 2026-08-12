<?php

declare(strict_types=1);

namespace Modules\Prescriptions\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Modules\Prescriptions\Models\Prescription;

class PrescriptionService
{
    /**
     * @param  array<string, mixed>  $data
     * @param  array<int, array<string, mixed>>  $items
     */
    public function createWithItems(array $data, array $items): Prescription
    {
        $data['clinic_id'] = $data['clinic_id'] ?? current_clinic()?->id;
        $data['prescription_number'] = $this->generatePrescriptionNumber();

        return DB::transaction(function () use ($data, $items): Prescription {
            $prescription = Prescription::query()->create($data);

            $prescription->items()->createMany($items);

            return $prescription;
        });
    }

    /**
     * @param  array<string, mixed>  $data
     * @param  array<int, array<string, mixed>>  $items
     */
    public function updateWithItems(Prescription $prescription, array $data, array $items): Prescription
    {
        DB::transaction(function () use ($prescription, $data, $items): void {
            $prescription->update($data);

            $prescription->items()->delete();

            $prescription->items()->createMany($items);
        });

        return $prescription;
    }

    public function generatePrescriptionNumber(): string
    {
        $prefix = Str::upper((string) setting('prescription.prefix', 'RX'));

        return DB::transaction(function () use ($prefix): string {
            $last = Prescription::query()
                ->where('prescription_number', 'like', $prefix . '-%')
                ->orderByDesc('prescription_number')
                ->value('prescription_number');

            $sequence = 1;

            if ($last !== null) {
                $sequence = ((int) Str::afterLast($last, '-')) + 1;
            }

            return $prefix . '-' . str_pad((string) $sequence, 6, '0', STR_PAD_LEFT);
        });
    }
}
