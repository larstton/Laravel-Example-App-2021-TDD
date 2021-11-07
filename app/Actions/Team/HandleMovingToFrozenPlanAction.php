<?php

namespace App\Actions\Team;

use App\Enums\EventReminder;
use App\Models\Frontman;
use App\Models\Host;
use App\Models\Team;

class HandleMovingToFrozenPlanAction
{
    public function execute(Team $team)
    {
        $team->events()
            ->select('events.*')
            ->join('rules', 'events.rule_id', '=', 'rules.id')
            ->whereActive()
            ->union(
                $team->events()
                    ->select('events.*')
                    ->whereActive()
                    ->whereColumn('check_id', '=', 'rule_id')
                    ->where(function ($query) use ($team) {
                        $query
                            ->whereIn('check_id', Host::query()->select('id')->whereTeamId($team->id))
                            ->orWhereIn('check_id', Frontman::query()->select('id')->whereTeamId($team->id));
                    })
            )
            ->get()
            ->each->update([
                'reminders' => EventReminder::Disabled(),
            ]);
    }
}
