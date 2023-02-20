<?php

namespace App\Policies;

use App\Models\User;
use TCG\Voyager\Policies\UserPolicy as VoyagerUserPolicy;
use Illuminate\Auth\Access\HandlesAuthorization;

class UserPolicy extends VoyagerUserPolicy
{
    use HandlesAuthorization;

    /**
     * @param User $user
     * @return bool
     */
    public function viewTelescope(User $user): bool
    {
        return $user->email == 'guilherme@point2point.com.br';
    }
}
