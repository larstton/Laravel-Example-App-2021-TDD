<?php

namespace App\Console\Commands\Team;

use App\Models\HostHistory;
use App\Support\Tenancy\Facades\TenantManager;
use Illuminate\Console\Command;

class CleanupHostHistory extends Command
{
    protected $signature = 'cloudradar:host-history:cleanup-soft-deleted';

    protected $description = 'Removes host history entries older than 2 months';

    public function handle()
    {
        TenantManager::disableTenancyChecks();
        HostHistory::onlyTrashed()
            ->where('deleted_at', '<', now()->subDays(60))
            ->forceDelete();
    }
}
