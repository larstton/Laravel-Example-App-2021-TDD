<?php

namespace App\Listeners\Recipient;

use App\Actions\Tag\SetTagMetaForRecipientFilteringAction;
use App\Events\Recipient\RecipientDeleted;
use Illuminate\Contracts\Queue\ShouldQueue;

class DeletedRecipientSetHostTagMeta implements ShouldQueue
{
    private SetTagMetaForRecipientFilteringAction $action;

    public function __construct(SetTagMetaForRecipientFilteringAction $action)
    {
        $this->action = $action;
    }

    public function handle(RecipientDeleted $event)
    {
        $this->action->execute($event->recipient);
    }
}
