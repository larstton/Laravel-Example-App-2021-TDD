<?php

namespace App\Models;

use App\Enums\EventAction;
use App\Http\Transformers\DateTransformer;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Support\Str;

class HostEventSummary implements Arrayable
{
    private Host $host;

    public function __construct(Host $host)
    {
        $this->host = $host;
    }

    public function toArray(): array
    {
        return $this->host->events->mapToGroups(function (Event $event) {
            $groupBy = $event->action->is(EventAction::Alert) ? 'alerts' : 'warnings';

            return [
                $groupBy => [
                    'checkKey'  => $event->check_key,
                    'message'   => $this->makeMessage($event),
                    'reminders' => (bool) $event->reminders->value,
                    'eventId'   => $event->id,
                    'checkId'   => $event->check_id,
                    'meta'      => $event->meta,
                    'comments'  => (int) $event->comment_count,
                    'dates'     => [
                        'createdAt'  => DateTransformer::transform($event->created_at),
                        'resolvedAt' => DateTransformer::transform($event->resolved_at),
                    ],
                ],
            ];
        })->all();
    }

    private function makeMessage(Event $event)
    {
        $rule = $event->rule;

        if (! $rule) {
            return '';
        }

        if ($rule->expression_alias) {
            $message = Str::of($rule->expression_alias)
                ->replace('_', ' ')
                ->replace('failed 1 times', 'failed 1 time');
        } elseif ($event->check_key === 'cagent.heartbeat') {
            $message = ' lost';
        } else {
            $message = $rule->operator->value.' ';
            $message .= str_replace('.0000', '', $rule->threshold).' ';
        }

        return (string) $message;
    }
}
