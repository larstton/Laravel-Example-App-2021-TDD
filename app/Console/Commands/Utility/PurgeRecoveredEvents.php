<?php

namespace App\Console\Commands\Utility;

use App\Models\Event;
use App\Support\NotifierService;
use App\Support\Tenancy\Facades\TenantManager;
use Illuminate\Console\Command;

class PurgeRecoveredEvents extends Command
{
    protected $signature = 'cloudradar:events:purge {days=30}';
    protected $description = 'Removes event resolved more then given number of days ago';

    public function handle(NotifierService $notifierService)
    {
        $days = $this->argument('days');

        $this->info("Removing events resolved more than ".$days." day(s) ago");
        TenantManager::disableTenancyChecks();
        $query = Event::query()
            ->withoutGlobalScopes()
            ->where('resolved_at','<', now()->subDays($days));

        $count = $query->count();
        $this->info($string = $count." event(s) to cleanup...");
        logger()->info($string);

        $this->output->progressStart($count);

        $query->cursor()
            ->each(function (Event $event) use ($notifierService) {
                $this->output->progressAdvance();
                $event->team->makeCurrentTenant();
                $event->sentReminders()->delete();
                $event->eventComments()->delete();
                $event->delete();
            });

        $this->output->progressFinish();
        $this->info("Removed events events resolved more than ".$days." day(s) ago");
    }
}
