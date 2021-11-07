<?php

namespace App\Http\Controllers\Team;

use App\Events\Team\TeamSettingsUpdated;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class TeamSettingController extends Controller
{
    public function __invoke(Request $request)
    {
        team_settings($team = current_team())->set($request->settings);

        TeamSettingsUpdated::dispatch($team, $request->settings);

        return $this->accepted([
            'data' => team_settings($team)->get(),
        ]);
    }
}
