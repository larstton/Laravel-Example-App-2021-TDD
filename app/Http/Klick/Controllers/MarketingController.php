<?php

namespace App\Http\Klick\Controllers;

use App\Actions\User\ToggleSubscriptionAction;
use App\Http\Controllers\Controller;
use App\Models\Team;
use App\Models\User;

class MarketingController extends Controller
{
    public function edit($userId, $teamId)
    {
        $user = User::where('id', $userId)
            ->where('team_id', $teamId)
            ->first();

        if (filled($user)) {
            $team = $user->team;

            return view('klick.pages.marketing.unsubscribe-form', compact('user', 'team'));
        }

        return view('klick.pages.general-error');
    }

    public function update(User $user, Team $team, ToggleSubscriptionAction $unsubscribeAction)
    {
        $user = $unsubscribeAction->execute($user);

        return view('klick.pages.marketing.unsubscribe-result', compact('user', 'team'));
    }
}
