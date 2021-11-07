<?php

/** @noinspection PhpRedundantCatchClauseInspection */

namespace App\Http\Controllers\Host;

use App\Actions\ServiceCheck\CreateServiceCheckAction;
use App\Actions\ServiceCheck\DeleteServiceCheckAction;
use App\Actions\ServiceCheck\UpdateServiceCheckAction;
use App\Data\ServiceCheck\ServiceCheckData;
use App\Exceptions\CheckPreflightException;
use App\Http\Controllers\Controller;
use App\Http\Queries\ServiceCheckQuery;
use App\Http\Requests\ServiceCheck\CreateServiceCheckRequest;
use App\Http\Requests\ServiceCheck\UpdateServiceCheckRequest;
use App\Http\Resources\ServiceCheckResource;
use App\Models\Host;
use App\Models\ServiceCheck;

class HostServiceCheckController extends Controller
{
    public function index(Host $host)
    {
        $this->authorize('viewAny', [ServiceCheck::class, $host]);

        return ServiceCheckResource::collection(
            (new ServiceCheckQuery($host))->jsonPaginate()
        );
    }

    public function store(
        CreateServiceCheckRequest $request,
        Host $host,
        CreateServiceCheckAction $createServiceCheckAction
    ) {
        $this->authorize('create', [ServiceCheck::class, $host]);

        try {
            $serviceCheck = $createServiceCheckAction->execute(
                $this->user(),
                $host,
                ServiceCheckData::fromRequest($request)
            );
        } catch (CheckPreflightException $e) {
            return $this->json([
                'data' => [
                    'error'   => $e->getMessage(),
                    'console' => $e->getConsole(),
                ],
            ], 412);
        }

        return ServiceCheckResource::make($serviceCheck);
    }

    public function update(
        UpdateServiceCheckRequest $request,
        Host $host,
        ServiceCheck $serviceCheck,
        UpdateServiceCheckAction $updateServiceCheckAction
    ) {
        $this->authorize('update', [$serviceCheck, $host]);

        $serviceCheck = $updateServiceCheckAction->execute(
            $serviceCheck,
            $request->active,
            $request->checkInterval
        );

        return ServiceCheckResource::make($serviceCheck);
    }

    public function destroy(Host $host, ServiceCheck $serviceCheck, DeleteServiceCheckAction $deleteServiceCheckAction)
    {
        $this->authorize('delete', [$serviceCheck, $host]);

        $deleteServiceCheckAction->execute($this->user(), $serviceCheck, $host);

        return $this->noContent();
    }
}
