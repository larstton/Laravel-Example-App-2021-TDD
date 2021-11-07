<?php

namespace App\Console\Commands\Utility;

use App\Enums\EventState;
use App\Models\Event;
use App\Support\NotifierService;
use App\Support\Tenancy\Facades\TenantManager;
use Illuminate\Console\Command;
use Illuminate\Database\Query\Builder;

class EventsCleanUpCommand extends Command
{
    protected $signature = 'cloudradar:events:cleanup';

    protected $description = 'Removes events with deleted checks';

    public function handle(NotifierService $notifierService)
    {
        $this->info("Removing events without checks");
        TenantManager::disableTenancyChecks();
        $query = Event::query()
            ->withoutGlobalScopes()
            ->whereNotIn('check_id', function (Builder $query) {
                $query->select('id')->from('web_checks');
            })
            ->whereNotIn('check_id', function (Builder $query) {
                $query->select('id')->from('service_checks');
            })
            ->whereNotIn('check_id', function (Builder $query) {
                $query->select('id')->from('custom_checks');
            })
            ->whereNotIn('check_id', function (Builder $query) {
                $query->select('id')->from('snmp_checks');
            })
            ->whereNotIn('check_id', function (Builder $query) {
                $query->select('id')->from('frontmen');
            })
            ->whereNotIn('check_id', function (Builder $query) {
                $query->select('id')->from('hosts')->where('cagent', 1);
            });

        $count = $query->count();
        $this->info($string = $count." event(s) to cleanup...");
        logger()->info($string);

        $this->output->progressStart($count);

        $query->cursor()
            ->each(function (Event $event) use ($notifierService) {
                $this->output->progressAdvance();
                $event->team->makeCurrentTenant();
                if ($event->state->is(EventState::Active())) {
                    $notifierService->recoverEvent($event);
                }
                $event->sentReminders()->delete();
                $event->eventComments()->delete();
                $event->delete();
            });

        $this->output->progressFinish();
        $this->info("Removed events without checks");
    }
}
