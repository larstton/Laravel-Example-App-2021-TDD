<?php

namespace App\Http\Klick\Controllers\Auth;

use App\Actions\Auth\ConfigureNewPleskTeamPostVerificationAction;
use App\Http\Controllers\Controller;
use App\Models\User;

class PleskTeamUserVerificationSuccess extends Controller
{
    public function __invoke(User $user, ConfigureNewPleskTeamPostVerificationAction $action)
    {
        $apiToken = $action->execute($user->team);

        return view('klick.pages.plesk-team-verification-success', compact('user', 'apiToken'));
    }
}
