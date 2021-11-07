<?php

namespace App\Http\Controllers\Team;

use App\Actions\User\ToggleSubscriptionAction;
use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use App\Models\Team;
use App\Models\User;

class MarketingController extends Controller
{
    public function __invoke(User $user, Team $team, ToggleSubscriptionAction $changeSubscription)
    {
        $user = $changeSubscription->execute($user);

        return UserResource::make($user);
    }
}
