<?php

namespace App\Policies;

use App\Models\Shipment;
use App\Models\User;

class ShipmentPolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, Shipment $shipment): bool
    {
        if ($user->isAdmin() || $user->isOperator()) {
            return true;
        }

        return $user->id === $shipment->customer_id;
    }

    public function create(User $user): bool
    {
        return $user->isAdmin() || $user->isOperator();
    }

    public function update(User $user, Shipment $shipment): bool
    {
        return $user->isAdmin() || $user->isOperator();
    }

    public function delete(User $user, Shipment $shipment): bool
    {
        return $user->isAdmin();
    }
}
