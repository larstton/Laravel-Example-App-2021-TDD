<?php

namespace App\Http\Controllers\Host;

use App\Actions\Host\CreateHostAction;
use App\Actions\Host\DeleteHostAction;
use App\Actions\Host\UpdateHostAction;
use App\Data\Host\HostData;
use App\Http\Controllers\Controller;
use App\Http\Queries\HostQuery;
use App\Http\Requests\Host\CreateHostRequest;
use App\Http\Requests\Host\UpdateHostRequest;
use App\Http\Resources\Host\HostResource;
use App\Jobs\Host\HardDeleteHost;
use App\Jobs\Host\PostDeleteHostTidyUp;
use App\Models\Host;

class HostController extends Controller
{
    public function index(HostQuery $hostQuery)
    {
        return HostResource::collection($hostQuery->jsonPaginate())->setMeta([
            'hostsHash' => Host::getHashOfAllTeamsHosts(),
            'hostCount' => Host::count(),
        ]);
    }

    public function store(CreateHostRequest $request, CreateHostAction $createNewHost)
    {
        $this->authorize(Host::class);

        $host = $createNewHost->execute($this->user(), HostData::fromRequest($request));

        return HostResource::make($host->load('frontman', 'subUnit'));
    }

    public function show(Host $host)
    {
        $this->authorize($host);

        return HostResource::make($host);
    }

    public function update(UpdateHostRequest $request, Host $host, UpdateHostAction $updateHostAction)
    {
        $this->authorize($host);

        $host = $updateHostAction->execute($this->user(), $host, HostData::fromRequest($request));

        return HostResource::make(Host::resolveFullyLoaded($host));
    }

    public function destroy(Host $host, DeleteHostAction $deleteHostAction)
    {
        $this->authorize($host);

        $deleteHostAction->execute($host, request('complete', false));

        return $this->noContent();
    }
}
