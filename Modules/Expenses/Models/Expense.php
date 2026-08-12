<?php

declare(strict_types=1);

namespace Modules\Expenses\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Modules\Authentication\Models\User;
use Modules\Core\Models\BaseModel;
use Modules\Expenses\Enums\ExpensePaymentMethod;

class Expense extends BaseModel
{
    protected $fillable = [
        'clinic_id',
        'category_id',
        'description',
        'amount',
        'expense_date',
        'payment_method',
        'recorded_by',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'amount' => 'decimal:2',
            'expense_date' => 'date',
            'payment_method' => ExpensePaymentMethod::class,
        ];
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(ExpenseCategory::class, 'category_id');
    }

    public function recorder(): BelongsTo
    {
        return $this->belongsTo(User::class, 'recorded_by');
    }

    public function scopeSearch(Builder $query, ?string $term): Builder
    {
        if (blank($term)) {
            return $query;
        }

        $term = trim($term);

        return $query->where(function (Builder $q) use ($term) {
            $q->where('description', 'like', "%{$term}%")
                ->orWhereHas('category', function (Builder $cq) use ($term) {
                    $cq->where('name', 'like', "%{$term}%");
                });
        });
    }

    public function scopeForCategory(Builder $query, ?string $categoryId): Builder
    {
        return blank($categoryId) ? $query : $query->where('category_id', $categoryId);
    }

    public function scopeBetweenDates(Builder $query, $from, $to): Builder
    {
        return $query
            ->when($from, fn ($q) => $q->whereDate('expense_date', '>=', $from))
            ->when($to, fn ($q) => $q->whereDate('expense_date', '<=', $to));
    }
}
