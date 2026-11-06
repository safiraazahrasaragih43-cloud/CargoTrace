<?php

namespace App\Policies;

use App\Models\Carrier;
use App\Models\User;

class CarrierPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->isAdmin() || $user->isOperator();
    }

    public function view(User $user, Carrier $carrier): bool
    {
        return $user->isAdmin() || $user->isOperator();
    }

    public function create(User $user): bool
    {
        return $user->isAdmin();
    }

    public function update(User $user, Carrier $carrier): bool
    {
        return $user->isAdmin() || $user->isOperator();
    }

    public function delete(User $user, Carrier $carrier): bool
    {
        return $user->isAdmin();
    }
}

