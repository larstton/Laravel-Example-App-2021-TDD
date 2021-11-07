<?php

namespace App\Console\Commands\Utility;

use App\Models\ApiToken;
use App\Models\CheckResult;
use App\Support\Tenancy\Facades\TenantManager;
use Illuminate\Console\Command;

class GeneralCleanup extends Command
{
    protected $signature = 'cloudradar:general:cleanup';

    protected $description = 'General cleanup of orphaned entities.';

    public function handle()
    {
        TenantManager::disableTenancyChecks();
        CheckResult::where('data_updated_at', '<', now()->subDays(60))->delete();
        CheckResult::doesntHave('host')->delete();
        ApiToken::doesntHave('team')->delete();
    }
}
