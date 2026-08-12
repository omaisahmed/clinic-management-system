<?php

declare(strict_types=1);

namespace Modules\Authentication\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Modules\Authentication\Enums\Role;

class UpdateUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('staff.update');
    }

    public function rules(): array
    {
        $userId = $this->route('user')?->getKey();

        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($userId)],
            'phone' => ['nullable', 'string', 'max:40'],
            'gender' => ['nullable', 'string', 'in:male,female,other'],
            'role' => ['required', Rule::enum(Role::class)],
            'password' => ['nullable', 'string', 'min:8', 'confirmed'],
            'is_active' => ['sometimes', 'boolean'],
        ];
    }
}
