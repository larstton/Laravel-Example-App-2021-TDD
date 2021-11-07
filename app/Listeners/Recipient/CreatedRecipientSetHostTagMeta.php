<?php

namespace App\Listeners\Recipient;

use App\Actions\Tag\SetTagMetaForRecipientFilteringAction;
use App\Events\Recipient\RecipientCreated;
use Illuminate\Contracts\Queue\ShouldQueue;

class CreatedRecipientSetHostTagMeta implements ShouldQueue
{
    private SetTagMetaForRecipientFilteringAction $action;

    public function __construct(SetTagMetaForRecipientFilteringAction $action)
    {
        $this->action = $action;
    }

    public function handle(RecipientCreated $event)
    {
        $this->action->execute($event->recipient);
    }
}
