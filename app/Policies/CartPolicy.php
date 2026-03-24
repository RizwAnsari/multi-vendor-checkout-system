<?php

namespace App\Policies;

use App\Models\User;

class CartPolicy
{
    /**
     * Determine whether the user can manage the cart.
     */
    public function manage(?User $user): bool
    {
        // If not logged in, user is a guest and we allow guest carts.
        if (!$user) return true;

        // If logged in, must be a customer.
        return $user->isCustomer();
    }
}
