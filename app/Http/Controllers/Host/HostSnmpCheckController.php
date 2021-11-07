<?php

/** @noinspection PhpRedundantCatchClauseInspection */

namespace App\Http\Controllers\Host;

use App\Actions\SnmpCheck\CreateSnmpCheckAction;
use App\Actions\SnmpCheck\DeleteSnmpCheckAction;
use App\Actions\SnmpCheck\UpdateSnmpCheckAction;
use App\Data\SnmpCheck\CreateSnmpCheckData;
use App\Data\SnmpCheck\UpdateSnmpCheckData;
use App\Http\Controllers\Controller;
use App\Http\Queries\SNMPCheckQuery;
use App\Http\Requests\SnmpCheck\CreateSnmpCheckRequest;
use App\Http\Requests\SnmpCheck\UpdateSnmpCheckRequest;
use App\Http\Resources\SnmpCheckResource;
use App\Models\Host;
use App\Models\SnmpCheck;

class HostSnmpCheckController extends Controller
{
    public function index(Host $host)
    {
        $this->authorize('viewAny', [SnmpCheck::class, $host]);

        return SnmpCheckResource::collection(
            (new SNMPCheckQuery($host))->jsonPaginate()
        );
    }

    public function store(
        CreateSnmpCheckRequest $request,
        Host $host,
        CreateSnmpCheckAction $createSnmpCheckAction
    ) {
        $this->authorize('create', [SnmpCheck::class, $host]);

        $snmpCheck = $createSnmpCheckAction->execute(
            $this->user(),
            $host,
            CreateSnmpCheckData::fromRequest($request)
        );

        return SnmpCheckResource::make($snmpCheck);
    }

    public function update(
        UpdateSnmpCheckRequest $request,
        Host $host,
        SnmpCheck $snmpCheck,
        UpdateSnmpCheckAction $updateSnmpCheckAction
    ) {
        $this->authorize('update', [$snmpCheck, $host]);

        $snmpCheck = $updateSnmpCheckAction->execute(
            $snmpCheck,
            UpdateSnmpCheckData::fromRequest($request)
        );

        return SnmpCheckResource::make($snmpCheck);
    }

    public function destroy(Host $host, SnmpCheck $snmpCheck, DeleteSnmpCheckAction $deleteSnmpCheckAction)
    {
        $this->authorize('delete', [$snmpCheck, $host]);

        $deleteSnmpCheckAction->execute($this->user(), $snmpCheck, $host);

        return $this->noContent();
    }
}
