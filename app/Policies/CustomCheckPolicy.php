<?php

namespace App\Policies;

use App\Models\CustomCheck;
use App\Models\Host;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class CustomCheckPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     *
     * @param  User  $user
     * @param  Host  $host
     * @return mixed
     */
    public function viewAny(User $user, Host $host)
    {
        return $user->team->id === $host->team_id;
    }

    /**
     * Determine whether the user can create models.
     *
     * @param  User  $user
     * @param  Host  $host
     * @return mixed
     */
    public function create(User $user, Host $host)
    {
        // can only add checks to hosts owned by the team.
        return $user->team->id === $host->team_id;
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param  User  $user
     * @param  CustomCheck  $customCheck
     * @param  Host  $host
     * @return mixed
     */
    public function update(User $user, CustomCheck $customCheck, Host $host)
    {
        return $user->team->id === $host->team_id && $customCheck->host_id === $host->id;
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param  User  $user
     * @param  CustomCheck  $customCheck
     * @param  Host  $host
     * @return mixed
     */
    public function delete(User $user, CustomCheck $customCheck, Host $host)
    {
        return $user->team->id === $host->team_id && $customCheck->host_id === $host->id;
    }
}
