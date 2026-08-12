<?php

declare(strict_types=1);

namespace Modules\Visits\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Modules\Visits\Models\Visit;

class VisitService
{
    /**
     * @param  array<string, mixed>  $data
     */
    public function create(array $data): Visit
    {
        $data['clinic_id'] = $data['clinic_id'] ?? current_clinic()?->id;
        $data['visit_number'] = $this->generateVisitNumber();

        return Visit::query()->create($data);
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public function update(Visit $visit, array $data): Visit
    {
        $visit->update($data);

        return $visit;
    }

    public function generateVisitNumber(): string
    {
        $prefix = Str::upper((string) setting('visit.prefix', 'V'));

        return DB::transaction(function () use ($prefix): string {
            $last = Visit::query()
                ->where('visit_number', 'like', $prefix . '-%')
                ->orderByDesc('visit_number')
                ->value('visit_number');

            $sequence = 1;

            if ($last !== null) {
                $sequence = ((int) Str::afterLast($last, '-')) + 1;
            }

            return $prefix . '-' . str_pad((string) $sequence, 6, '0', STR_PAD_LEFT);
        });
    }
}
