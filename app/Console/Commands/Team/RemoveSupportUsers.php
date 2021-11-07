<?php

namespace App\Console\Commands\Team;

use App\Models\Team;
use App\Models\TeamMember;
use App\Support\Tenancy\Facades\TenantManager;
use Illuminate\Console\Command;

class RemoveSupportUsers extends Command
{
    protected $signature = 'cloudradar:user:cleanup-support';

    protected $description = 'Removes support users that are used on team';

    public function handle()
    {
        TenantManager::disableTenancyChecks();
        $teamMembers = TeamMember::supportUser()->get();
        if ($teamMembers->isNotEmpty()) {
            Team::whereIn('id', $teamMembers->pluck('team_id'))
                ->update([
                    'has_granted_access_to_support' => 0,
                ]);
            $teamMembers->each->delete();
        }
    }
}
