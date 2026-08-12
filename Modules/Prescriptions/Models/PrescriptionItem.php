<?php

declare(strict_types=1);

namespace Modules\Prescriptions\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Modules\Core\Models\BaseModel;
use Modules\Medicines\Models\Medicine;

class PrescriptionItem extends BaseModel
{
    protected $table = 'prescription_items';

    protected $fillable = [
        'prescription_id',
        'medicine_id',
        'name',
        'dosage',
        'frequency',
        'duration',
        'instructions',
    ];

    public function prescription(): BelongsTo
    {
        return $this->belongsTo(Prescription::class);
    }

    public function medicine(): BelongsTo
    {
        return $this->belongsTo(Medicine::class);
    }
}
