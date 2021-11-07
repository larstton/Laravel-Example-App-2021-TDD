<?php

namespace App\Actions\Host;

use App\Actions\Event\PurgeEventsForHostAction;
use App\Enums\EventState;
use App\Enums\Rule\RuleHostMatchPart;
use App\Models\CheckResult;
use App\Models\Concerns\AuthedEntity;
use App\Models\CustomCheck;
use App\Models\Event;
use App\Models\Host;
use App\Models\Rule;
use Facades\App\Actions\Host\LogHostHistoryAction;
use Facades\App\Support\Influx\InfluxRepository;
use Facades\App\Support\NotifierService;
use Illuminate\Support\Facades\DB;

class PostHostDeleteTidyUpAction extends AbstractHostAction
{
    protected PurgeEventsForHostAction $purgeEventsAction;

    public function __construct(PurgeEventsForHostAction $purgeEventsAction)
    {
        $this->purgeEventsAction = $purgeEventsAction;
    }

    public function execute(Host $host, AuthedEntity $authedEntity, bool $purgeReports): void
    {
        $this->authedEntity = $authedEntity;

        DB::transaction(function () use ($purgeReports, $host) {
            CheckResult::withoutGlobalScopes()
                ->whereCheckIdMatchesChecksOfHost($host)
                ->get()->each->delete();

            if ($purgeReports) {
                $this->purgeEventsAction->execute($this->authedEntity->team, $host->id);
            } else {
                // Mark events as resolved, they will be deleted by event purge script after 30 days.
                Event::withoutGlobalScopes()
                    ->whereCheckIdMatchesChecksOfHost($host)
                    ->get()->each->update([
                        'state'       => EventState::Recovered(),
                        'resolved_at' => now(),
                    ]);
            }

            $host->customChecks()->each(function (CustomCheck $customCheck) {
                InfluxRepository::setDatabase(
                    config('influx.repository.databases.customChecks')
                )->dropMeasurement($customCheck->id);
                $customCheck->delete();
            });

            $host->serviceChecks->each->delete();
            $host->webChecks->each->delete();
            $host->snmpChecks->each->delete();
            $host->customChecks->each->delete();

            Rule::where('host_match_criteria', $host->id)
                ->where('host_match_part', RuleHostMatchPart::UUID())
                ->get()->each->delete();

            NotifierService::deleteHost($host, $purgeReports);
            LogHostHistoryAction::execute($host, 'delete');

            if (Host::whereCagent(true)->where('id', '!=', $host->id)->doesntExist()) {
                $this->removeAgentRules();
            }
        });
    }
}
