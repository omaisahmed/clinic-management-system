<?php

declare(strict_types=1);

namespace Modules\Appointments\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Modules\Appointments\Enums\AppointmentStatus;
use Modules\Appointments\Enums\AppointmentType;
use Modules\Authentication\Models\User;
use Modules\Core\Models\BaseModel;
use Modules\Patients\Models\Patient;

class Appointment extends BaseModel
{
    protected $fillable = [
        'clinic_id',
        'patient_id',
        'doctor_id',
        'appointment_type',
        'status',
        'appointment_date',
        'start_time',
        'duration',
        'reason',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'appointment_type' => AppointmentType::class,
            'status' => AppointmentStatus::class,
            'appointment_date' => 'date',
        ];
    }

    public function patient(): BelongsTo
    {
        return $this->belongsTo(Patient::class);
    }

    public function doctor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'doctor_id');
    }

    public function scopeSearch($query, ?string $term)
    {
        if (blank($term)) {
            return $query;
        }

        $term = trim($term);

        return $query->where(function ($q) use ($term) {
            $q->whereHas('patient', fn ($p) => $p->where('first_name', 'like', "%{$term}%")
                ->orWhere('last_name', 'like', "%{$term}%")
                ->orWhere('phone', 'like', "%{$term}%"))
                ->orWhere('reason', 'like', "%{$term}%");
        });
    }

    public function scopeForStatus($query, ?string $status)
    {
        if (blank($status)) {
            return $query;
        }

        return $query->where('status', $status);
    }

    public function scopeForDate($query, ?string $date)
    {
        if (blank($date)) {
            return $query;
        }

        return $query->whereDate('appointment_date', $date);
    }
}
