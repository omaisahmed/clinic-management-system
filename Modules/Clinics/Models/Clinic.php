<?php

declare(strict_types=1);

namespace Modules\Clinics\Models;

use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\Core\Models\BaseModel;

class Clinic extends BaseModel
{
    use SoftDeletes;

    protected $fillable = [
        'name',
        'logo_path',
        'favicon_path',
        'tagline',
        'description',
        'phone',
        'whatsapp',
        'email',
        'website',
        'address',
        'city',
        'state',
        'country',
        'postal_code',
        'timezone',
        'currency',
        'registration_number',
        'tax_number',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    public function settings(): HasMany
    {
        return $this->hasMany(ClinicSetting::class);
    }

    public function primarySettings(): HasOne
    {
        return $this->settings()->one()->where('group', 'clinic');
    }

    public function getLogoUrlAttribute(): ?string
    {
        return $this->logo_path ? asset('storage/' . $this->logo_path) : null;
    }

    public function getFaviconUrlAttribute(): ?string
    {
        return $this->favicon_path ? asset('storage/' . $this->favicon_path) : null;
    }
}
