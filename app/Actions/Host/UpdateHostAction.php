<?php

namespace App\Actions\Host;

use App\Data\Host\HostData;
use App\Enums\EventState;
use App\Exceptions\HostException;
use App\Models\Concerns\AuthedEntity;
use App\Models\Event;
use App\Models\Host;
use Facades\App\Support\NotifierService;
use Illuminate\Support\Facades\DB;

class UpdateHostAction extends AbstractHostAction
{
    public function execute(AuthedEntity $authedEntity, Host $host, HostData $data): Host
    {
        $this->authedEntity = $authedEntity;
        $this->team = $this->authedEntity->team;
        $this->host = $host;
        $this->data = $data;

        $this->guard();

        $frontman = $this->data->frontman ?? $this->authedEntity->team->defaultFrontman;

        $host->fill([
            'last_update_user_id' => $this->authedEntity->getAuthIdentifier(),
            'name'                => $data->name,
            'frontman_id'         => $frontman->id,
            'sub_unit_id'         => optional($data->subUnit)->id,
            'connect'             => $data->connect,
            'description'         => $data->description,
            'active'              => $data->active,
            'cagent'              => $data->cagent,
            'dashboard'           => $data->dashboard,
            'muted'               => $data->muted,
        ]);

        if ($this->data->snmpData->hasData()) {
            $host = $this->fillSnmpData($host);
        } else {
            $host = $this->removeSnmpData($host);
        }

        return DB::transaction(function () use ($data, $host) {
            if (filled($this->data->tags)) {
                if (count($this->data->tags) === 1 && is_null($this->data->tags[0])) {
                    $host->detachTags($host->tags);
                } else {
                    $host->syncTagsWithType($data->tags, Host::getTagType());
                }
            }

            $host->save();

            if ($host->wasChanged('cagent')) {
                if ($host->usesMonitoringAgent()) {
                    $this->createAgentRules();
                } else {
                    $this->removeAgentRules();
                    $this->removeStaleEvents($host);
                }
            }

            if ($host->wasChanged('muted') && $host->muted) {
                $this->recoverAndUpdateHostEvents($host);
            }

            return $host->refresh();
        });
    }

    private function removeStaleEvents(Host $host): void
    {
        $host->events()
            ->where('check_id', $host->id)
            ->get()
            ->each->delete();
    }

    private function recoverAndUpdateHostEvents(Host $host): void
    {
        Event::query()
            ->whereCheckIdMatchesChecksOfHost($host)
            ->whereActive()
            ->each(function (Event $event) {
                $event->update([
                    'state'       => EventState::Recovered(),
                    'resolved_at' => now(),
                ]);
                NotifierService::recoverEvent($event);
            });
    }
}
