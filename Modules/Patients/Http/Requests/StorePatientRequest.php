<?php

declare(strict_types=1);

namespace Modules\Patients\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Modules\Patients\Enums\BloodGroup;
use Modules\Patients\Enums\Gender;
use Modules\Patients\Enums\MaritalStatus;

class StorePatientRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('patients.create');
    }

    public function rules(): array
    {
        return [
            'first_name' => ['required', 'string', 'max:100'],
            'last_name' => ['required', 'string', 'max:100'],
            'gender' => ['nullable', Rule::enum(Gender::class)],
            'date_of_birth' => ['nullable', 'date', 'before_or_equal:today'],
            'blood_group' => ['nullable', Rule::enum(BloodGroup::class)],
            'phone' => ['nullable', 'string', 'max:40'],
            'whatsapp' => ['nullable', 'string', 'max:40'],
            'email' => ['nullable', 'email', 'max:255'],
            'cnic' => ['nullable', 'string', 'max:50'],
            'address' => ['nullable', 'string', 'max:500'],
            'city' => ['nullable', 'string', 'max:120'],
            'country' => ['nullable', 'string', 'max:120'],
            'emergency_contact' => ['nullable', 'string', 'max:100'],
            'emergency_contact_phone' => ['nullable', 'string', 'max:40'],
            'occupation' => ['nullable', 'string', 'max:255'],
            'marital_status' => ['nullable', Rule::enum(MaritalStatus::class)],
            'photo' => ['nullable', 'image', 'max:2048'],
            'notes' => ['nullable', 'string'],
        ];
    }
}
