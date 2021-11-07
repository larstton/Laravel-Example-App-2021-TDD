<?php

namespace App\Jobs\CustomCheck;

use App\Enums\EventState;
use App\Models\CheckResult;
use App\Models\CustomCheck;
use App\Models\Event;
use App\Models\Host;
use App\Models\User;
use App\Support\Rule\RuleFactory;
use Facades\App\Support\Influx\InfluxRepository;
use Facades\App\Support\NotifierService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\DB;

class DeleteCustomCheck implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable;

    public $user;

    public $customCheck;

    public $host;

    public bool $isLastCheck;

    public function __construct(User $user, CustomCheck $customCheck, Host $host, bool $isLastCheck)
    {
        $this->user = $user;
        $this->customCheck = $customCheck;
        $this->host = $host;
        $this->isLastCheck = $isLastCheck;
    }

    public function handle()
    {
        DB::transaction(function () {
            CheckResult::whereCheckId($this->customCheck->id)
                ->get()
                ->each->delete();

            Event::whereCheckId($this->customCheck->id)
                ->each(function (Event $event) {
                    if ($event->state->is(EventState::Active())) {
                        NotifierService::recoverEvent($event);
                    }
                    $event->delete();
                });

            NotifierService::deleteRemindersForCustomCheck($this->customCheck);

            InfluxRepository::setDatabase(
                config('influx.repository.databases.customChecks')
            )->dropMeasurement($this->customCheck->id);

            RuleFactory::makeSmartCustomCheckAlertRule($this->user)->deleteIf($this->isLastCheck);
            RuleFactory::makeSmartCustomCheckWarningRule($this->user)->deleteIf($this->isLastCheck);
            if (RuleFactory::makeCustomCheckSuccessAlertRule($this->user)->deleteIf($this->isLastCheck)) {
                team_settings($this->user->team)->set([
                    'heartbeats.custom.active' => false,
                ]);
            }
        });
    }
}
