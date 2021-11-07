<?php

namespace App\Actions\Host\Guard;

use App\Data\Host\HostData;
use App\Exceptions\TeamException;
use App\Models\Frontman;
use App\Models\Team;

class FrontmanGuard
{
    public function __invoke(HostData $hostData, Team $team): void
    {
        if (is_null($hostData->frontman)) {
            throw_if(
                $team->defaultFrontman->id === Frontman::DEFAULT_FRONTMAN_UUID,
                TeamException::noDefaultFrontman()
            );
        } else {
            throw_unless(
                $hostData->frontman->validForTeam($team),
                TeamException::invalidFrontman($hostData->frontman->id)
            );
        }
    }
}
