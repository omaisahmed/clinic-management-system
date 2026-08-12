<?php

declare(strict_types=1);

namespace Modules\Patients\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Modules\Core\Models\BaseModel;

class PatientContact extends BaseModel
{
    protected $fillable = [
        'patient_id',
        'name',
        'relationship',
        'phone',
        'is_primary',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'is_primary' => 'boolean',
        ];
    }

    public function patient(): BelongsTo
    {
        return $this->belongsTo(Patient::class);
    }
}
