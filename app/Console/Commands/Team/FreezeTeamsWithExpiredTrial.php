<?php

namespace App\Console\Commands\Team;

use App\Actions\Team\NotifyTeamAdminTrialHasExpiredAction;
use App\Enums\TeamPlan;
use App\Models\Team;
use App\Support\Tenancy\Facades\TenantManager;
use Illuminate\Console\Command;

class FreezeTeamsWithExpiredTrial extends Command
{
    protected $signature = 'cloudradar:team:freeze-expired-trials';

    protected $description = 'Find all teams with expired trials and set their plan to Frozen.';

    public function handle(NotifyTeamAdminTrialHasExpiredAction $notifyTeamAdminTrialHasExpiredAction)
    {
        TenantManager::disableTenancyChecks();

        $query = Team::query()
            ->where('plan', TeamPlan::Trial())
            ->where('trial_ends_at', '<', now());

        $count = $query->count();

        $this->info($string = $count.' team(s) to freeze...');
        logger()->info($string);

        $this->output->progressStart($count);

        $query->each(function (Team $team) use ($notifyTeamAdminTrialHasExpiredAction) {
            $this->output->progressAdvance();
            $trialEndedAt = $team->trial_ends_at;

            $team->update([
                'plan'                 => TeamPlan::Frozen(),
                'previous_plan'        => TeamPlan::Trial(),
                'plan_last_changed_at' => now(),
                'trial_ends_at'        => null,
            ]);

            // If trial ended within last 24 hours, then send emails to team admins.
            // If trial ended more than 24 hours ago, then we don't send emails as
            // these are probably old downgrades or dead accounts from v2.
            if ($trialEndedAt->greaterThanOrEqualTo(now()->subDay())) {
                $notifyTeamAdminTrialHasExpiredAction->execute($team);
            }
        });

        $this->output->progressFinish();
    }
}
