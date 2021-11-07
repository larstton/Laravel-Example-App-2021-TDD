<?php

namespace App\Jobs\ServiceCheck;

use App\Enums\EventState;
use App\Models\CheckResult;
use App\Models\Event;
use App\Models\Host;
use App\Models\ServiceCheck;
use App\Models\User;
use App\Support\Rule\RuleFactory;
use Facades\App\Support\NotifierService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\DB;

class DeleteServiceCheck implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable;

    public $user;

    public $serviceCheck;

    public $host;

    public function __construct(User $user, ServiceCheck $serviceCheck, Host $host)
    {
        $this->user = $user;
        $this->serviceCheck = $serviceCheck;
        $this->host = $host;
    }

    public function handle()
    {
        DB::transaction(function () {
            CheckResult::whereCheckId($this->serviceCheck->id)
                ->get()
                ->each->delete();

            Event::whereCheckId($this->serviceCheck->id)
                ->each(function (Event $event) {
                    if ($event->state->is(EventState::Active())) {
                        NotifierService::recoverEvent($event);
                    }
                    $event->delete();
                });

            NotifierService::deleteRemindersForServiceCheck($this->serviceCheck);

            RuleFactory::makeICMPRoundTripAlertRule($this->user)->deleteIf(
                Host::has('serviceChecks')->count() === 0
            );
            RuleFactory::makeICMPPacketLossAlertRule($this->user)->deleteIf(
                Host::has('serviceChecks')->count() === 0
            );
        });
    }
}
