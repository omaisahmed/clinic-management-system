<?php

declare(strict_types=1);

namespace Modules\Billing\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Modules\Billing\Enums\InvoiceStatus;
use Modules\Core\Models\BaseModel;
use Modules\Patients\Models\Patient;
use Modules\Payments\Models\Payment;
use Modules\Visits\Models\Visit;

class Invoice extends BaseModel
{
    protected $fillable = [
        'clinic_id',
        'patient_id',
        'visit_id',
        'invoice_number',
        'status',
        'issue_date',
        'due_date',
        'subtotal',
        'discount',
        'tax',
        'total',
        'paid_amount',
        'due_amount',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'status' => InvoiceStatus::class,
            'issue_date' => 'date',
            'due_date' => 'date',
            'subtotal' => 'decimal:2',
            'discount' => 'decimal:2',
            'tax' => 'decimal:2',
            'total' => 'decimal:2',
            'paid_amount' => 'decimal:2',
            'due_amount' => 'decimal:2',
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

    public function items(): HasMany
    {
        return $this->hasMany(InvoiceItem::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    public function scopeSearch(Builder $query, ?string $term): Builder
    {
        if (blank($term)) {
            return $query;
        }

        $term = trim($term);

        return $query->where(function (Builder $q) use ($term) {
            $q->where('invoice_number', 'like', "%{$term}%")
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
