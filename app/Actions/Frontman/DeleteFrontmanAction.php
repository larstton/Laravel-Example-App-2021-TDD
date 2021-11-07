<?php

namespace App\Actions\Frontman;

use App\Enums\EventState;
use App\Exceptions\FrontmanException;
use App\Models\Event;
use App\Models\Frontman;
use Facades\App\Support\NotifierService;
use Illuminate\Support\Facades\DB;

class DeleteFrontmanAction
{
    public function execute(Frontman $frontman): void
    {
        throw_if($frontman->hosts()->count() > 0, FrontmanException::frontmanInUse());

        DB::transaction(function () use ($frontman) {
            Event::where('host_id', $frontman->id)
                ->each(function (Event $event) {
                    if ($event->state->is(EventState::Active())) {
                        NotifierService::recoverEvent($event);
                    }
                    $event->sentReminders()->delete();
                    $event->delete();
                });

            $frontman->delete();
        });
    }
}
