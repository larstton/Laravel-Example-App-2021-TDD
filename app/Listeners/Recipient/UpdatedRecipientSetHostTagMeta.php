<?php

namespace App\Listeners\Recipient;

use App\Actions\Tag\SetTagMetaForRecipientFilteringAction;
use App\Events\Recipient\RecipientUpdated;
use Illuminate\Contracts\Queue\ShouldQueue;

class UpdatedRecipientSetHostTagMeta implements ShouldQueue
{
    private SetTagMetaForRecipientFilteringAction $action;

    public function __construct(SetTagMetaForRecipientFilteringAction $action)
    {
        $this->action = $action;
    }

    public function handle(RecipientUpdated $event)
    {
        $this->action->execute($event->recipient);
    }
}
