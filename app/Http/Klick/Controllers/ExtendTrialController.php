<?php

namespace App\Http\Klick\Controllers;

use App\Actions\Team\ExtendTrialAction;
use App\Http\Controllers\Controller;
use App\Models\User;

class ExtendTrialController extends Controller
{
    public function __invoke($user, ExtendTrialAction $extendTrialAction)
    {
        $user = User::find($user);

        if (filled($user)) {
            $team = $extendTrialAction->execute($user, $user->team);

            return view('klick.pages.marketing.extend-trial', compact('user', 'team'));
        }

        return view('klick.pages.general-error');
    }
}
