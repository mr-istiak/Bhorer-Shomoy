<?php

namespace App\Policies;

use App\Models\Media;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class MediaPolicy
{
    use HandlesAuthorization;
    /**
     * Determine whether the user can view any media (listing).
     * Admins may view all; non-admins will be shown only their own (controller handles filtering).
     */
    public function viewAny(User $user)
    {
        return $user->hasRole([User::ROLE_ADMIN, User::ROLE_EDITOR]);
    }

    /**
     * Determine whether the user can view/download the media.
     * Admins can view any; owners can view their own.
     */
    public function update(User $user, Media $media)
    {
        return $this->viewAny($user) || $media->isBelongsTo($user);
    }

    /**
     * Determine whether the user can delete the media.
     * Admins can delete any; owners can delete their own.
     */
    public function delete(User $user, Media $media)
    {
        return $user->isAdmin() || $media->isBelongsTo($user);
    }
}
