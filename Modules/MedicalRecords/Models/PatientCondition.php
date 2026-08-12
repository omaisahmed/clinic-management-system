<?php

declare(strict_types=1);

namespace Modules\MedicalRecords\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Modules\Core\Models\BaseModel;
use Modules\Patients\Models\Patient;

class PatientCondition extends BaseModel
{
    protected $fillable = [
        'patient_id',
        'condition',
        'diagnosis_date',
        'status',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'diagnosis_date' => 'date',
        ];
    }

    public function patient(): BelongsTo
    {
        return $this->belongsTo(Patient::class);
    }
}
