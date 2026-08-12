<?php

declare(strict_types=1);

namespace Modules\Patients\Policies;

use Modules\Authentication\Models\User;
use Modules\Patients\Models\Patient;

class PatientPolicy
{
    public function viewAny(User $actor): bool
    {
        return $actor->can('patients.view');
    }

    public function view(User $actor, Patient $patient): bool
    {
        return $actor->can('patients.view');
    }

    public function create(User $actor): bool
    {
        return $actor->can('patients.create');
    }

    public function update(User $actor, Patient $patient): bool
    {
        return $actor->can('patients.update');
    }

    public function delete(User $actor, Patient $patient): bool
    {
        return $actor->can('patients.delete');
    }

    public function viewHistory(User $actor, Patient $patient): bool
    {
        return $actor->can('patients.view_history');
    }
}
