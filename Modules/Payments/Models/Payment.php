<?php

declare(strict_types=1);

namespace Modules\Payments\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Modules\Billing\Models\Invoice;
use Modules\Core\Models\BaseModel;
use Modules\Patients\Models\Patient;
use Modules\Payments\Enums\PaymentMethod;

class Payment extends BaseModel
{
    protected $fillable = [
        'clinic_id',
        'patient_id',
        'invoice_id',
        'amount',
        'method',
        'reference',
        'payment_date',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'amount' => 'decimal:2',
            'method' => PaymentMethod::class,
            'payment_date' => 'date',
        ];
    }

    public function patient(): BelongsTo
    {
        return $this->belongsTo(Patient::class);
    }

    public function invoice(): BelongsTo
    {
        return $this->belongsTo(Invoice::class);
    }

    public function scopeSearch($query, ?string $term)
    {
        if (blank($term)) {
            return $query;
        }

        $term = trim($term);

        return $query->where(function ($q) use ($term) {
            $q->where('reference', 'like', "%{$term}%")
                ->orWhereHas('patient', function ($pq) use ($term) {
                    $pq->where('first_name', 'like', "%{$term}%")
                        ->orWhere('last_name', 'like', "%{$term}%")
                        ->orWhere('phone', 'like', "%{$term}%");
                });
        });
    }

    public function scopeForMethod($query, ?string $method)
    {
        if (blank($method)) {
            return $query;
        }

        return $query->where('method', $method);
    }

    public function scopeBetweenDates($query, $from, $to)
    {
        return $query
            ->when($from, fn ($q) => $q->whereDate('payment_date', '>=', $from))
            ->when($to, fn ($q) => $q->whereDate('payment_date', '<=', $to));
    }
}
