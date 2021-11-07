<?php

namespace App\Actions\Host\Guard;

use App\Data\Host\HostData;
use App\Exceptions\HostException;
use App\Models\Host;
use Illuminate\Database\Eloquent\Builder;

class HostUniquenessGuard
{
    private HostData $hostData;
    private ?Host $host;

    public function __invoke(HostData $hostData, ?Host $host): void
    {
        $this->hostData = $hostData;
        $this->host = $host;

        $this->checkNameIsUniqueForTeam();
        $this->checkConnectIsUniqueForTeam();
    }

    private function checkNameIsUniqueForTeam()
    {
        throw_if(
            Host::where('name', $this->hostData->name)
                ->when(! is_null($this->host), function (Builder $query) {
                    $query->where('id', '!=', $this->host->id);
                })
                ->exists(),
            HostException::hostWithNameExistsForTeam($this->hostData->name)
        );
    }

    private function checkConnectIsUniqueForTeam()
    {
        if (is_null($this->hostData->connect)) {
            return;
        }

        throw_if(
            Host::where('connect', $this->hostData->connect)
                ->when(! is_null($this->host), function (Builder $query) {
                    $query->where('id', '!=', $this->host->id);
                })
                ->exists(),
            HostException::hostWithConnectExistsForTeam($this->hostData->connect)
        );
    }
}
