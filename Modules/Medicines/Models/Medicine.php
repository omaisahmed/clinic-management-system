<?php

declare(strict_types=1);

namespace Modules\Medicines\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Modules\Clinics\Models\Clinic;
use Modules\Core\Models\BaseModel;

class Medicine extends BaseModel
{
    protected $fillable = [
        'clinic_id',
        'name',
        'generic_name',
        'category',
        'brand',
        'strength',
        'unit',
        'stock',
        'reorder_level',
        'cost_price',
        'selling_price',
        'expiry_date',
        'description',
    ];

    protected function casts(): array
    {
        return [
            'cost_price' => 'decimal:2',
            'selling_price' => 'decimal:2',
            'expiry_date' => 'date',
        ];
    }

    public function clinic(): BelongsTo
    {
        return $this->belongsTo(Clinic::class);
    }

    public function getIsLowStockAttribute(): bool
    {
        return $this->stock !== null && $this->reorder_level !== null && $this->stock <= $this->reorder_level;
    }

    public function scopeSearch(Builder $query, ?string $term): Builder
    {
        if (blank($term)) {
            return $query;
        }

        $term = trim($term);

        return $query->where(function (Builder $q) use ($term) {
            $q->where('name', 'like', "%{$term}%")
                ->orWhere('generic_name', 'like', "%{$term}%")
                ->orWhere('brand', 'like', "%{$term}%")
                ->orWhere('category', 'like', "%{$term}%");
        });
    }

    public function scopeCategory(Builder $query, ?string $category): Builder
    {
        if (blank($category)) {
            return $query;
        }

        return $query->where('category', $category);
    }

    public function scopeLowStock(Builder $query): Builder
    {
        return $query->whereColumn('stock', '<=', 'reorder_level');
    }

    public function scopeExpiringSoon(Builder $query, int $days = 30): Builder
    {
        return $query->whereNotNull('expiry_date')
            ->where('expiry_date', '<=', today()->addDays($days));
    }
}
