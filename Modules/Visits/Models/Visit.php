<?php

declare(strict_types=1);

namespace Modules\Visits\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Modules\Appointments\Models\Appointment;
use Modules\Authentication\Models\User;
use Modules\Clinics\Models\Clinic;
use Modules\Core\Models\BaseModel;
use Modules\Patients\Models\Patient;
use Modules\Visits\Enums\VisitStatus;

class Visit extends BaseModel
{
    protected $fillable = [
        'clinic_id',
        'patient_id',
        'doctor_id',
        'appointment_id',
        'visit_number',
        'visit_date',
        'status',
        'chief_complaint',
        'diagnosis',
        'notes',
        'temperature',
        'blood_pressure',
        'heart_rate',
        'respiratory_rate',
        'weight',
        'height',
    ];

    protected function casts(): array
    {
        return [
            'visit_date' => 'date',
            'status' => VisitStatus::class,
            'temperature' => 'decimal:2',
            'weight' => 'decimal:2',
            'height' => 'decimal:2',
        ];
    }

    public function clinic(): BelongsTo
    {
        return $this->belongsTo(Clinic::class);
    }

    public function patient(): BelongsTo
    {
        return $this->belongsTo(Patient::class);
    }

    public function doctor(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function appointment(): BelongsTo
    {
        return $this->belongsTo(Appointment::class);
    }

    public function scopeSearch(Builder $query, ?string $term): Builder
    {
        if (blank($term)) {
            return $query;
        }

        $term = trim($term);

        return $query->where(function (Builder $q) use ($term) {
            $q->where('chief_complaint', 'like', "%{$term}%")
                ->orWhereHas('patient', function (Builder $patient) use ($term) {
                    $patient->where('first_name', 'like', "%{$term}%")
                        ->orWhere('last_name', 'like', "%{$term}%")
                        ->orWhere('phone', 'like', "%{$term}%");
                });
        });
    }
}
