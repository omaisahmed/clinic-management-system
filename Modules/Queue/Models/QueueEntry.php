<?php

declare(strict_types=1);

namespace Modules\Queue\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Modules\Appointments\Models\Appointment;
use Modules\Core\Models\BaseModel;
use Modules\Patients\Models\Patient;
use Modules\Queue\Enums\QueueStatus;

class QueueEntry extends BaseModel
{
    protected $table = 'queue_tokens';

    protected $fillable = [
        'clinic_id',
        'patient_id',
        'doctor_id',
        'appointment_id',
        'token_number',
        'status',
        'entered_at',
        'called_at',
        'completed_at',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'entered_at' => 'datetime',
            'called_at' => 'datetime',
            'completed_at' => 'datetime',
            'status' => QueueStatus::class,
        ];
    }

    public function patient(): BelongsTo
    {
        return $this->belongsTo(Patient::class);
    }

    public function appointment(): BelongsTo
    {
        return $this->belongsTo(Appointment::class);
    }

    public function scopeForDate(Builder $query, ?string $date): Builder
    {
        if (blank($date)) {
            return $query;
        }

        return $query->whereDate('created_at', $date);
    }

    public function scopeForStatus(Builder $query, ?string $status): Builder
    {
        if (blank($status)) {
            return $query;
        }

        return $query->where('status', $status);
    }
}
