<?php

namespace App\Policies;

use App\Models\ApiToken;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class ApiTokenPolicy
{
    use HandlesAuthorization;

    public function before(User $user)
    {
        return $user->isTeamMember();
    }

    /**
     * Determine whether the user can view any models.
     *
     * @param  User  $user
     * @return mixed
     */
    public function viewAny(User $user)
    {
        return true;
    }

    /**
     * Determine whether the user can view the model.
     *
     * @param  User  $user
     * @param  ApiToken  $apiToken
     * @return mixed
     */
    public function view(User $user, ApiToken $apiToken)
    {
        return $user->team_id === $apiToken->team_id;
    }

    /**
     * Determine whether the user can create models.
     *
     * @param  User  $user
     * @return mixed
     */
    public function create(User $user)
    {
        return true;
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param  User  $user
     * @param  ApiToken  $apiToken
     * @return mixed
     */
    public function delete(User $user, ApiToken $apiToken)
    {
        return $user->team_id === $apiToken->team_id;
    }
}
