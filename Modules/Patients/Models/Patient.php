<?php

declare(strict_types=1);

namespace Modules\Patients\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\Clinics\Models\Clinic;
use Modules\Core\Models\BaseModel;
use Modules\MedicalRecords\Models\PatientAllergy;
use Modules\MedicalRecords\Models\PatientCondition;
use Modules\MedicalRecords\Models\PatientFamilyHistory;
use Modules\MedicalRecords\Models\PatientSocialHistory;
use Modules\MedicalRecords\Models\PatientSurgery;
use Modules\Patients\Database\Factories\PatientFactory;

class Patient extends BaseModel
{
    use HasFactory, SoftDeletes;

    protected static function newFactory(): PatientFactory
    {
        return PatientFactory::new();
    }

    protected $fillable = [
        'clinic_id',
        'patient_number',
        'first_name',
        'last_name',
        'gender',
        'date_of_birth',
        'blood_group',
        'phone',
        'whatsapp',
        'email',
        'cnic',
        'address',
        'city',
        'country',
        'emergency_contact',
        'emergency_contact_phone',
        'occupation',
        'marital_status',
        'photo_path',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'date_of_birth' => 'date',
        ];
    }

    public function clinic(): BelongsTo
    {
        return $this->belongsTo(Clinic::class);
    }

    public function contacts(): HasMany
    {
        return $this->hasMany(PatientContact::class);
    }

    public function allergies(): HasMany
    {
        return $this->hasMany(PatientAllergy::class);
    }

    public function conditions(): HasMany
    {
        return $this->hasMany(PatientCondition::class);
    }

    public function surgeries(): HasMany
    {
        return $this->hasMany(PatientSurgery::class);
    }

    public function familyHistories(): HasMany
    {
        return $this->hasMany(PatientFamilyHistory::class);
    }

    public function socialHistory(): HasOne
    {
        return $this->hasOne(PatientSocialHistory::class);
    }

    public function getFullNameAttribute(): string
    {
        return trim("{$this->first_name} {$this->last_name}");
    }

    public function getInitialsAttribute(): string
    {
        return mb_strtoupper(mb_substr((string) $this->first_name, 0, 1) . mb_substr((string) $this->last_name, 0, 1));
    }

    public function getAgeAttribute(): ?int
    {
        return $this->date_of_birth?->age;
    }

    public function getPhotoUrlAttribute(): ?string
    {
        return $this->photo_path ? asset('storage/' . $this->photo_path) : null;
    }

    public function scopeSearch($query, ?string $term)
    {
        if (blank($term)) {
            return $query;
        }

        $term = trim($term);

        return $query->where(function ($q) use ($term) {
            $q->where('patient_number', 'like', "%{$term}%")
                ->orWhere('first_name', 'like', "%{$term}%")
                ->orWhere('last_name', 'like', "%{$term}%")
                ->orWhere('phone', 'like', "%{$term}%")
                ->orWhere('email', 'like', "%{$term}%")
                ->orWhere('cnic', 'like', "%{$term}%");
        });
    }
}
