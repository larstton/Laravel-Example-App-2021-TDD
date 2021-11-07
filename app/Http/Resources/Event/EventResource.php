<?php

namespace App\Http\Resources\Event;

use App\Http\Resources\JsonResource;
use App\Http\Transformers\DateTransformer;
use App\Models\Event;
use Illuminate\Support\Str;

/**
 * @mixin Event
 */
class EventResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id'             => $this->id,
            $this->mergeWhen(filled($this->host), function () {
                return [
                    'host' => [
                        'id'      => $this->host->id,
                        'name'    => $this->host->name,
                        'connect' => $this->host->connect,
                    ],
                ];
            }),
            'checkId'        => $this->check_id,
            'checkKey'       => $this->check_key,
            'message'        => $this->makeMessage(),
            'action'         => $this->action->value,
            'state'          => $this->state->value,
            'reminders'      => $this->reminders->value,
            'affectedHostId' => $this->affected_host_id,
            'meta'           => $this->meta,
            'dates'          => [
                'lastCheckedAt' => DateTransformer::transform($this->last_checked_at),
                'createdAt'     => DateTransformer::transform($this->created_at),
                'resolvedAt'    => DateTransformer::transform($this->resolved_at),
            ],
        ];
    }

    private function makeMessage()
    {
        $rule = $this->rule;

        if (! $rule) {
            if ($this->check_key === 'cagent.heartbeat') {
                return ' lost';
            }
            return '';
        }

        if ($rule->expression_alias) {
            $message = Str::of($rule->expression_alias)
                ->replace('_', ' ')
                ->replace('failed 1 times', 'failed 1 time');
        } else {
            $message = $rule->operator->value.' ';
            $message .= str_replace('.0000', '', $rule->threshold).' ';
        }

        return (string) $message;
    }
}
