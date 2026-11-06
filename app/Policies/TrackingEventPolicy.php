<?php

namespace App\Policies;

use App\Models\Shipment;
use App\Models\User;

class TrackingEventPolicy
{
    /**
     * Determine whether the user can view tracking timeline of a shipment.
     */
    public function view(User $user, Shipment $shipment): bool
    {
        if ($user->isAdmin() || $user->isOperator()) {
            return true;
        }

        return $user->id === $shipment->customer_id;
    }

    /**
     * Determine whether the user can add a tracking checkpoint event.
     */
    public function create(User $user, Shipment $shipment): bool
    {
        return $user->isAdmin() || $user->isOperator();
    }
}
