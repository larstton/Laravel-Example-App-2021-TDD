<?php

namespace Database\Factories;

use App\Enums\EventAction;
use App\Enums\EventReminder;
use App\Enums\EventState;
use App\Models\Event;
use App\Models\Host;
use App\Models\Rule;
use App\Models\Team;
use App\Models\WebCheck;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class EventFactory extends Factory
{
    protected $model = Event::class;

    protected $eventMeta = [
        'name'         => 'Net Icmp Ping has failed 1 time',
        'uuid'         => '17984499-6533-42f1-8304-6ec53bcecb7b',
        'check'        => [
            'name'                  => 'The rule Net Icmp Ping has failed 1 time has triggered an alert.',
            'uuid'                  => 'fe941692-5551-49ed-87d0-09422325813a',
            'lastValue'             => '0',
            'lastValueTextTemplate' => null,
        ],
        'linkUrl'      => null,
        'footnote'     => null,
        'linkText'     => null,
        'severity'     => 'alert',
        'description'  => '',
        'affectedHost' => [
            'name'        => null,
            'uuid'        => null,
            'connect'     => null,
            'location'    => 'Check performed from CloudRadar datacenter EU-WEST.',
            'description' => '',
        ],
    ];

    public function definition()
    {
        return [
            'id'               => Str::uuid()->toString(),
            'team_id'          => Team::factory(),
            'host_id'          => Host::factory(),
            'rule_id'          => Rule::factory(),
            'check_id'         => WebCheck::factory(),
            'check_key'        => '*.success',
            'action'           => EventAction::Alert(),
            'state'            => EventState::Active(),
            'reminders'        => EventReminder::Enabled(),
            'meta'             => function ($attributes) {
                /** @var Host $host */
                $host = Host::find($attributes['host_id']) ?? Host::factory()->create();

                return tap($this->eventMeta, function (array &$eventMeta) use ($host) {
                    $meta = [
                        'affectedHost' => [
                            'name'    => $host->name,
                            'uuid'    => $host->id,
                            'connect' => $host->connect,
                        ],
                    ];
                    $eventMeta = array_merge_recursive_distinct($eventMeta, $meta);
                });
            },
            'last_check_value' => null,
            'last_checked_at'  => null,
            'resolved_at'      => null,
            'created_at'       => now(),
            'updated_at'       => now(),
        ];
    }

    public function withMergedMeta(array $meta)
    {
        return $this->state(function (array $attributes) use ($meta) {
            return [
                'meta' => tap($this->eventMeta, function (array &$eventMeta) use ($meta) {
                    $eventMeta = array_merge_recursive_distinct($eventMeta, $meta);
                }),
            ];
        });
    }
}
