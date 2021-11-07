<?php

namespace App\Actions\Host\Guard;

use App\Enums\TeamPlan;
use App\Exceptions\TeamException;
use App\Models\ApiToken;
use App\Models\Concerns\AuthedEntity;
use App\Models\Team;

class PlanLevelGuard
{
    public function __invoke(AuthedEntity $authedEntity, Team $team): void
    {
        if (is_a($authedEntity, ApiToken::class)) {
            return;
        }

        throw_if(
            $team->plan->is(TeamPlan::Frozen()),
            TeamException::trialExpired()
        );
        throw_if(
            $team->hosts()->count() >= $team->max_hosts,
            TeamException::maximumHostsReached()
        );
    }
}
