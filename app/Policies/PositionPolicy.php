<?php

namespace App\Policies;

use App\Models\Position;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class PositionPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->hasRole([User::ROLE_ADMIN, User::ROLE_EDITOR]);
    }
    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user): bool
    {   
        return $this->viewAny($user);
    }
}
