<?php

declare(strict_types=1);

namespace Modules\Documents\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Modules\Authentication\Models\User;
use Modules\Core\Models\BaseModel;
use Modules\Patients\Models\Patient;

class Document extends BaseModel
{
    protected $fillable = [
        'clinic_id',
        'patient_id',
        'title',
        'category',
        'file_path',
        'original_name',
        'mime_type',
        'file_size',
        'uploaded_by',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'file_size' => 'integer',
        ];
    }

    public function patient(): BelongsTo
    {
        return $this->belongsTo(Patient::class);
    }

    public function uploader(): BelongsTo
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }

    public function getFileSizeLabelAttribute(): string
    {
        if ($this->file_size >= 1024 * 1024) {
            return number_format($this->file_size / (1024 * 1024), 1) . ' MB';
        }

        return number_format($this->file_size / 1024, 1) . ' KB';
    }

    public function scopeSearch(Builder $query, ?string $term): Builder
    {
        if (blank($term)) {
            return $query;
        }

        $term = trim($term);

        return $query->where(function (Builder $q) use ($term) {
            $q->where('title', 'like', "%{$term}%")
                ->orWhere('category', 'like', "%{$term}%");
        });
    }

    public function scopeCategory(Builder $query, ?string $category): Builder
    {
        return blank($category) ? $query : $query->where('category', $category);
    }
}
