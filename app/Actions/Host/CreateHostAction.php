<?php

namespace App\Actions\Host;

use App\Data\Host\HostData;
use App\Models\Concerns\AuthedEntity;
use App\Models\Host;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class CreateHostAction extends AbstractHostAction
{
    public function execute(AuthedEntity $authedEntity, HostData $data): Host
    {
        $this->authedEntity = $authedEntity;
        $this->team = $this->authedEntity->team;
        $this->data = $data;

        $this->guard();

        $host = new Host([
            'name'        => $this->data->name,
            'user_id'     => $authedEntity->id,
            'frontman_id' => optional($this->data->frontman)->id
                ?? $this->team->defaultFrontman->id,
            'sub_unit_id' => optional($this->data->subUnit)->id,
            'connect'     => $this->data->connect,
            'description' => $this->data->description,
            'active'      => $this->data->active,
            'cagent'      => $this->data->cagent,
            'dashboard'   => $this->data->dashboard,
            'muted'       => $this->data->muted,
            'password'    => Str::random(12),
        ]);

        if ($this->data->snmpData->hasData()) {
            $host = $this->fillSnmpData($host);
        }

        return DB::transaction(function () use ($host) {
            $host->save();

            if (filled($this->data->tags)) {
                $host->addTags($this->data->tags);
            }

            if ($host->usesMonitoringAgent()) {
                $this->createAgentRules();
            }

            if (! $this->team->has_created_host) {
                $this->team->has_created_host = true;
                $this->team->save();
            }

            return $host;
        });
    }
}
