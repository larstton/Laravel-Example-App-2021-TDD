<?php

namespace App\Console\Commands\Team;

use App\Actions\Team\DeleteTeamAction;
use App\Enums\TeamPlan;
use App\Models\Team;
use App\Support\Tenancy\Facades\TenantManager;
use Illuminate\Console\Command;

class FrozenTeamCleanUp extends Command
{
    protected $signature = 'cloudradar:team:cleanup-frozen';

    protected $description = 'Removes frozen teams which are older than 60 days.';

    public function handle(DeleteTeamAction $deleteTeamAction)
    {
        TenantManager::disableTenancyChecks();

        $query = Team::query()
            ->where('plan', TeamPlan::Frozen())
            ->where('plan_last_changed_at', '<=', now()->subDays(60));

        $count = $query->count();

        $this->info($string = $count.' frozen team(s) to purge...');
        logger()->info($string);

        $this->output->progressStart($count);

        $query->each(function (Team $team) use ($deleteTeamAction) {
            $this->output->progressAdvance();
            $deleteTeamAction->execute($team);
        });

        $this->output->progressFinish();
    }
}
