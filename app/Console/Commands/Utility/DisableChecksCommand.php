<?php

namespace App\Console\Commands\Utility;

use App\Data\Utility\DisabledChecksData;
use App\Enums\EventState;
use App\Models\Event;
use App\Models\ServiceCheck;
use App\Models\TeamMember;
use App\Models\WebCheck;
use App\Notifications\Team\ChecksDisabledNotification;
use App\Support\Tenancy\Facades\TenantManager;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class DisableChecksCommand extends Command
{

    protected $signature = 'cloudradar:checks:disable-and-notify';

    protected $description = 'Disables check with events that are active for 30 days and notifies users';

    public function handle()
    {
        TenantManager::disableTenancyChecks();
        $teams = Event::query()
            ->where('state', EventState::Active)
            ->where(DB::raw('DATEDIFF(NOW(),`created_at`)'), '=', 30)
            ->get()
            ->reduce(function ($data, Event $event) {

                $webCheck = WebCheck::query()
                    ->where('id', $event->check_id)
                    ->where('active', true)
                    ->first();
                if ($webCheck) {
                    if (empty($data[$event->team_id])) {
                        $data[$event->team_id] = new DisabledChecksData();
                    }
                    $data[$event->team_id]->addWebCheck($webCheck);
                    $webCheck->update(['active' => false]);

                    return $data;
                }

                $serviceCheck = ServiceCheck::query()
                    ->where('id', $event->check_id)
                    ->where('active', true)
                    ->first();
                if ($serviceCheck) {
                    if (empty($data[$event->team_id])) {
                        $data[$event->team_id] = new DisabledChecksData();
                    }
                    $data[$event->team_id]->addServiceCheck($serviceCheck);
                    $serviceCheck->update(['active' => false]);
                }

                return $data;

            }, []);

        $teams = collect($teams);
        $teams->each(function ($data, $team_id) {
            if (! empty($data)) {
                TeamMember::whereTeamId($team_id)
                    ->notDeleted()
                    ->get()
                    ->each
                    ->notify(new ChecksDisabledNotification($data));
            }
        });
    }
}
