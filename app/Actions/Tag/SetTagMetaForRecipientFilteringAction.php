<?php

namespace App\Actions\Tag;

use App\Models\Host;
use App\Models\Recipient;
use App\Models\Tag;

class SetTagMetaForRecipientFilteringAction
{
    public function execute(Recipient $recipient)
    {
        $currentTags = collect(optional($recipient->rules)['data'] ?? [])
            ->filter(fn ($rule) => $rule['field'] === 'tags')
            ->pluck('value')
            ->flatten();

        $oldTagsToBeRemoved = collect(optional($recipient->getOriginal('rules'))['data'] ?? [])
            ->filter(fn ($rule) => $rule['field'] === 'tags')
            ->pluck('value')
            ->flatten()
            ->diff($currentTags);

        if ($oldTagsToBeRemoved->isNotEmpty()) {
            $oldTagsToBeRemoved->each(function ($tag) use ($recipient) {
                if ($tag = Tag::findFromString($tag, Host::getTagType())) {
                    $tag->removeRecipientFilterFromMeta($recipient)->save();
                }
            });
        }

        if ($currentTags->isNotEmpty()) {
            $currentTags->each(function ($tag) use ($recipient) {
                if ($tag = Tag::findFromString($tag, Host::getTagType())) {
                    if ($recipient->exists) {
                        $tag->addRecipientFilterToMeta($recipient)->save();
                    } else {
                        $tag->removeRecipientFilterFromMeta($recipient)->save();
                    }
                }
            });
        }
    }
}
