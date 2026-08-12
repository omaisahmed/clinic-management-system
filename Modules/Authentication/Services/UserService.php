<?php

declare(strict_types=1);

namespace Modules\Authentication\Services;

use Illuminate\Support\Facades\Hash;
use Modules\Authentication\Models\User;

class UserService
{
    /**
     * @param  array<string, mixed>  $data
     */
    public function create(array $data): User
    {
        $user = User::query()->create([
            'clinic_id' => $data['clinic_id'] ?? current_clinic()?->id,
            'name' => $data['name'],
            'email' => $data['email'],
            'phone' => $data['phone'] ?? null,
            'gender' => $data['gender'] ?? null,
            'is_active' => $data['is_active'] ?? true,
            'password' => Hash::make($data['password']),
        ]);

        $user->assignRole($data['role']);

        return $user;
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public function update(User $user, array $data): User
    {
        $user->fill([
            'name' => $data['name'],
            'email' => $data['email'],
            'phone' => $data['phone'] ?? null,
            'gender' => $data['gender'] ?? null,
            'is_active' => $data['is_active'] ?? true,
        ]);

        if (! empty($data['password'])) {
            $user->password = Hash::make($data['password']);
        }

        if (isset($data['photo_path'])) {
            $user->photo_path = $data['photo_path'];
        }

        $user->save();

        if (isset($data['role'])) {
            $user->syncRoles([$data['role']]);
        }

        return $user;
    }
}
