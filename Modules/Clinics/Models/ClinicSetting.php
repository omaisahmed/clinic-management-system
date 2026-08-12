<?php

declare(strict_types=1);

namespace Modules\Clinics\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Modules\Core\Models\BaseModel;

class ClinicSetting extends BaseModel
{
    protected $fillable = [
        'clinic_id',
        'group',
        'key',
        'value',
        'type',
        'is_encrypted',
    ];

    protected function casts(): array
    {
        return [
            'is_encrypted' => 'boolean',
        ];
    }

    public function clinic(): BelongsTo
    {
        return $this->belongsTo(Clinic::class);
    }
}
