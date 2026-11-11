<?php

namespace App\Policies;

use App\Models\Shipment;
use App\Models\ShipmentException;
use App\Models\User;

class ShipmentExceptionPolicy
{
    /**
     * Determine whether the user can view exceptions for a shipment.
     */
    public function viewAny(User $user, Shipment $shipment): bool
    {
        if ($user->isAdmin() || $user->isOperator()) {
            return true;
        }

        return $user->id === $shipment->customer_id;
    }

    /**
     * Determine whether the user can report a new shipment exception.
     */
    public function create(User $user, Shipment $shipment): bool
    {
        return $user->isAdmin() || $user->isOperator();
    }

    /**
     * Determine whether the user can update or resolve an exception.
     */
    public function update(User $user, ShipmentException $exception): bool
    {
        return $user->isAdmin() || $user->isOperator();
    }
}

