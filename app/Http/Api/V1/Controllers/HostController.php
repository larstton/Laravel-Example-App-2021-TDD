<?php

namespace App\Http\Api\V1\Controllers;

use App\Actions\Host\CreateHostAction;
use App\Actions\Host\UpdateHostAction;
use App\Data\Host\HostData;
use App\Http\Api\V1\Requests\CreateHostRequest;
use App\Http\Api\V1\Requests\UpdateHostRequest;
use App\Http\Controllers\Controller;
use App\Http\Resources\Api\V1\HostCollection;
use App\Http\Resources\Api\V1\HostResource;
use App\Jobs\Host\HardDeleteHost;
use App\Jobs\Host\PostDeleteHostTidyUp;
use App\Models\Host;

class HostController extends Controller
{
    public function index()
    {
        $query = Host::query()->with([
            'serviceChecks', 'webChecks', 'customChecks',
            'snmpChecks', 'frontman', 'tags',
        ]);

        return HostCollection::make($query->get());
    }

    public function store(CreateHostRequest $request, CreateHostAction $createNewHost)
    {
        $this->authorize(Host::class);

        $host = $createNewHost->execute($this->user(), HostData::fromApiV1Request($request));

        return HostResource::make($host);
    }

    public function show(Host $host)
    {
        $this->authorize($host);

        return HostResource::make($host);
    }

    public function update(UpdateHostRequest $request, Host $host, UpdateHostAction $updateHostAction)
    {
        $this->authorize($host);

        $host = $updateHostAction->execute(
            $this->user(),
            $host,
            HostData::fromApiV1Request($request, $host)
        );

        return HostResource::make($host);
    }

    public function destroy(Host $host)
    {
        $this->authorize($host);

        $hostSoftDeleted = $host->delete();

        if ($hostSoftDeleted) {
            PostDeleteHostTidyUp::withChain([
                new HardDeleteHost($host),
            ])->dispatch($host, request('purgeFromReports', false));
        }

        return $this->success();
    }
}
