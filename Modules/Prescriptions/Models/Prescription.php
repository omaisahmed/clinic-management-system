<?php

declare(strict_types=1);

namespace Modules\Prescriptions\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Modules\Authentication\Models\User;
use Modules\Core\Models\BaseModel;
use Modules\Patients\Models\Patient;
use Modules\Prescriptions\Enums\PrescriptionStatus;
use Modules\Visits\Models\Visit;

class Prescription extends BaseModel
{
    protected $fillable = [
        'clinic_id',
        'patient_id',
        'visit_id',
        'doctor_id',
        'prescription_number',
        'status',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'status' => PrescriptionStatus::class,
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

    public function doctor(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(PrescriptionItem::class);
    }

    public function scopeSearch(Builder $query, ?string $term): Builder
    {
        if (blank($term)) {
            return $query;
        }

        $term = trim($term);

        return $query->where(function (Builder $q) use ($term) {
            $q->where('prescription_number', 'like', "%{$term}%")
                ->orWhereHas('patient', function (Builder $patient) use ($term) {
                    $patient->where('first_name', 'like', "%{$term}%")
                        ->orWhere('last_name', 'like', "%{$term}%")
                        ->orWhere('phone', 'like', "%{$term}%");
                });
        });
    }

    public function scopeForStatus(Builder $query, ?string $status): Builder
    {
        if (blank($status)) {
            return $query;
        }

        return $query->where('status', $status);
    }
}
