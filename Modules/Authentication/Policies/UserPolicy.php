<?php

declare(strict_types=1);

namespace Modules\Authentication\Policies;

use Modules\Authentication\Models\User;

class UserPolicy
{
    public function viewAny(User $actor): bool
    {
        return $actor->can('staff.view');
    }

    public function view(User $actor, User $user): bool
    {
        return $actor->can('staff.view');
    }

    public function create(User $actor): bool
    {
        return $actor->can('staff.create');
    }

    public function update(User $actor, User $user): bool
    {
        if ($actor->id === $user->id) {
            return true;
        }

        return $actor->can('staff.update');
    }

    public function delete(User $actor, User $user): bool
    {
        if ($actor->id === $user->id) {
            return false;
        }

        if ($user->hasRole(\Modules\Authentication\Enums\Role::SuperAdmin->value)) {
            return false;
        }

        return $actor->can('staff.delete');
    }
}
