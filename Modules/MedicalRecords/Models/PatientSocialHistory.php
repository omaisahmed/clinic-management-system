<?php

declare(strict_types=1);

namespace Modules\MedicalRecords\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Modules\Core\Models\BaseModel;
use Modules\Patients\Models\Patient;

class PatientSocialHistory extends BaseModel
{
    protected $fillable = [
        'patient_id',
        'smoking',
        'alcohol',
        'occupation',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'smoking' => 'boolean',
            'alcohol' => 'boolean',
        ];
    }

    public function patient(): BelongsTo
    {
        return $this->belongsTo(Patient::class);
    }
}
