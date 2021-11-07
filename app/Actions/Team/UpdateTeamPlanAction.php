<?php

namespace App\Actions\Team;

use App\Actions\Host\ActivateAllTeamHostsAction;
use App\Actions\Host\DeactivateAllTeamHostsAction;
use App\Actions\Host\UpdateCheckIntervalsForHostChecksAction;
use App\Data\Team\TeamManagementUpdateData;
use App\Enums\TeamPlan;
use App\Events\Team\TeamPlanDowngraded;
use App\Events\Team\TeamPlanUpgraded;
use App\Models\Team;
use Illuminate\Support\Facades\DB;

class UpdateTeamPlanAction
{
    public function execute(Team $team, TeamManagementUpdateData $updateData): Team
    {
        return DB::transaction(function () use ($updateData, $team): Team {
            $currentPlan = $team->plan;
            $newPlan = $updateData->plan;

            if ($team->isUnfreezingPlan($newPlan)) {
                resolve(ActivateAllTeamHostsAction::class)->execute($team);
            }

            if ($team->isUpgradingToPaygPlan($newPlan)) {
                resolve(CreatePaidHostHistoryOnTeamPlanUpgradeAction::class)->execute($team);
                resolve(NotifyAdminsTeamPlanUpgradedAction::class)->execute($team);
                $team->upgraded_at = now();
                TeamPlanUpgraded::dispatch($team);
            }

            if ($team->isDowngradingFromPaygPlan($newPlan)) {
                resolve(SoftDeletePaidHostHistoryOnTeamPlanDowngradeAction::class)->execute($team);
                resolve(NotifyAdminsTeamPlanDowngradedAction::class)->execute($team);
                TeamPlanDowngraded::dispatch($team);
            }

            if ($team->hasExceededMaximumHostsForPlan($updateData->maxHosts)) {
                resolve(DeactivateAllTeamHostsAction::class)->execute($team);
            }

            resolve(UpdateCheckIntervalsForHostChecksAction::class)->execute($team);

            if ($newPlan->is(TeamPlan::Frozen())) {
                resolve(HandleMovingToFrozenPlanAction::class)->execute($team);
            }

            $team->fill([
                'plan'               => $newPlan,
                'max_hosts'          => $updateData->maxHosts,
                'max_recipients'     => $updateData->maxRecipients,
                'data_retention'     => $updateData->dataRetention,
                'max_members'        => $updateData->maxMembers,
                'max_frontmen'       => $updateData->maxFrontmen,
                'min_check_interval' => $updateData->minCheckInterval,
                'trial_ends_at'      => null,
            ]);

            if ($newPlan->isNot($currentPlan)) {
                $team->plan_last_changed_at = now();
                $team->previous_plan = $currentPlan;
            }

            if (! is_null($updateData->currency)) {
                $team->currency = $updateData->currency;
            }

            $team->save();

            return $team;
        });
    }
}
