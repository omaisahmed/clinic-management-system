<?php

declare(strict_types=1);

namespace Modules\Authentication\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Modules\Authentication\Database\Factories\UserFactory;
use Modules\Authentication\Enums\Role;
use Modules\Clinics\Models\Clinic;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasFactory;
    use HasRoles;
    use Notifiable;

    protected static function newFactory(): UserFactory
    {
        return UserFactory::new();
    }

    protected $fillable = [
        'clinic_id',
        'name',
        'email',
        'phone',
        'gender',
        'photo_path',
        'is_active',
        'password',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_active' => 'boolean',
        ];
    }

    public function clinic(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Clinic::class);
    }

    public function getInitialsAttribute(): string
    {
        $words = preg_split('/\s+/', trim((string) $this->name));

        $initials = '';
        foreach (array_slice($words, 0, 2) as $word) {
            $initials .= mb_substr($word, 0, 1);
        }

        return mb_strtoupper($initials !== '' ? $initials : '?');
    }

    public function getPhotoUrlAttribute(): ?string
    {
        if ($this->photo_path) {
            return asset('storage/' . $this->photo_path);
        }

        return null;
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function isAdmin(): bool
    {
        return $this->hasAnyRole([Role::SuperAdmin->value, Role::ClinicAdmin->value]);
    }
}
