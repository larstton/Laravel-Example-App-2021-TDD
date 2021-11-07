<?php

namespace App\Data\Team;

use App\Data\BaseData;
use App\Enums\TeamPlan;
use App\Http\Loophole\Requests\TeamManagementRequest;

class TeamManagementUpdateData extends BaseData
{
    public TeamPlan  $plan;
    public int     $maxHosts;
    public int     $maxRecipients;
    public int     $dataRetention;
    public int     $maxMembers;
    public int     $maxFrontmen;
    public int     $minCheckInterval;
    public ?string $currency;

    public static function fromRequest(TeamManagementRequest $request): self
    {
        return new self([
            'plan'             => TeamPlan::coerce($request->plan),
            'maxHosts'         => (int) $request->maxHosts,
            'maxRecipients'    => (int) $request->maxRecipients,
            'dataRetention'    => (int) $request->dataRetention,
            'maxMembers'       => (int) $request->maxMembers,
            'maxFrontmen'      => (int) $request->maxFrontmen,
            'minCheckInterval' => (int) $request->minCheckInterval,
            'currency'         => $request->currency,
        ]);
    }
}
