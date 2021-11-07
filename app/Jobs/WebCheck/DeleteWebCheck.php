<?php

namespace App\Jobs\WebCheck;

use App\Enums\EventState;
use App\Models\CheckResult;
use App\Models\Event;
use App\Models\Host;
use App\Models\User;
use App\Models\WebCheck;
use App\Support\Rule\RuleFactory;
use Facades\App\Support\NotifierService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\DB;

class DeleteWebCheck implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable;

    public $user;

    public $webCheck;

    public $host;

    public function __construct(User $user, WebCheck $webCheck, Host $host)
    {
        $this->user = $user;
        $this->webCheck = $webCheck;
        $this->host = $host;
    }

    public function handle()
    {
        DB::transaction(function () {
            CheckResult::whereCheckId($this->webCheck->id)
                ->get()
                ->each->delete();

            Event::whereCheckId($this->webCheck->id)
                ->each(function (Event $event) {
                    if ($event->state->is(EventState::Active())) {
                        NotifierService::recoverEvent($event);
                    }
                    $event->delete();
                });

            NotifierService::deleteRemindersForWebCheck($this->webCheck);

            RuleFactory::makeHttpPerformanceWarningRule($this->user)->deleteIf(
                Host::has('webChecks')->count() === 0
            );
        });
    }
}
