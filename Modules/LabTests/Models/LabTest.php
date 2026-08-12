<?php

declare(strict_types=1);

namespace Modules\LabTests\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Modules\Core\Models\BaseModel;
use Modules\LabTests\Enums\LabTestStatus;
use Modules\Patients\Models\Patient;
use Modules\Visits\Models\Visit;

class LabTest extends BaseModel
{
    protected $fillable = [
        'clinic_id',
        'patient_id',
        'visit_id',
        'test_name',
        'category',
        'price',
        'status',
        'sample_type',
        'collection_date',
        'result',
        'result_date',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'price' => 'decimal:2',
            'status' => LabTestStatus::class,
            'collection_date' => 'date',
            'result_date' => 'date',
        ];
    }

    public function patient(): BelongsTo
    {
        return $this->belongsTo(Patient::class);
    }

    public function visit(): BelongsTo
    {
        return $this->belongsTo(Visit::class);
    }

    public function scopeSearch(Builder $query, ?string $term): Builder
    {
        if (blank($term)) {
            return $query;
        }

        $term = trim($term);

        return $query->where(function (Builder $q) use ($term) {
            $q->where('test_name', 'like', "%{$term}%")
                ->orWhere('category', 'like', "%{$term}%")
                ->orWhereHas('patient', function (Builder $patient) use ($term) {
                    $patient->where('first_name', 'like', "%{$term}%")
                        ->orWhere('last_name', 'like', "%{$term}%")
                        ->orWhere('phone', 'like', "%{$term}%");
                });
        });
    }

    public function scopeForStatus(Builder $query, ?string $status): Builder
    {
        return blank($status) ? $query : $query->where('status', $status);
    }
}
