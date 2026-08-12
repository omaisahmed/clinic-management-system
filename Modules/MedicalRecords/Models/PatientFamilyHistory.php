<?php

declare(strict_types=1);

namespace Modules\MedicalRecords\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Modules\Core\Models\BaseModel;
use Modules\Patients\Models\Patient;

class PatientFamilyHistory extends BaseModel
{
    protected $fillable = [
        'patient_id',
        'condition',
        'relation',
        'notes',
    ];

    public function patient(): BelongsTo
    {
        return $this->belongsTo(Patient::class);
    }
}
