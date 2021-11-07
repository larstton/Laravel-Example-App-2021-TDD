<?php

namespace App\Jobs\SnmpCheck;

use App\Enums\EventState;
use App\Models\CheckResult;
use App\Models\Event;
use App\Models\Host;
use App\Models\SnmpCheck;
use App\Models\User;
use Facades\App\Support\NotifierService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\DB;

class DeleteSnmpCheck implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable;

    public $user;

    public $snmpCheck;

    public $host;

    public function __construct(User $user, SnmpCheck $snmpCheck, Host $host)
    {
        $this->user = $user;
        $this->snmpCheck = $snmpCheck;
        $this->host = $host;
    }

    public function handle()
    {
        DB::transaction(function () {
            CheckResult::whereCheckId($this->snmpCheck->id)
                ->get()
                ->each->delete();

            Event::whereCheckId($this->snmpCheck->id)
                ->each(function (Event $event) {
                    if ($event->state->is(EventState::Active())) {
                        NotifierService::recoverEvent($event);
                    }
                    $event->delete();
                });

            NotifierService::deleteRemindersForSnmpCheck($this->snmpCheck);
        });
    }
}
