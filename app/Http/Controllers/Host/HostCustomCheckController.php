<?php

/** @noinspection PhpRedundantCatchClauseInspection */

namespace App\Http\Controllers\Host;

use App\Actions\CustomCheck\CreateCustomCheckAction;
use App\Actions\CustomCheck\DeleteCustomCheckAction;
use App\Actions\CustomCheck\UpdateCustomCheckAction;
use App\Data\CustomCheck\CustomCheckData;
use App\Http\Controllers\Controller;
use App\Http\Queries\CustomCheckQuery;
use App\Http\Requests\CustomCheck\CreateCustomCheckRequest;
use App\Http\Requests\CustomCheck\UpdateCustomCheckRequest;
use App\Http\Resources\CustomCheckResource;
use App\Models\CustomCheck;
use App\Models\Host;

class HostCustomCheckController extends Controller
{
    public function index(Host $host)
    {
        $this->authorize('viewAny', [CustomCheck::class, $host]);

        return CustomCheckResource::collection(
            (new CustomCheckQuery($host))->jsonPaginate()
        );
    }

    public function store(
        CreateCustomCheckRequest $request,
        Host $host,
        CreateCustomCheckAction $createCustomCheckAction
    ) {
        $this->authorize('create', [CustomCheck::class, $host]);

        $customCheck = $createCustomCheckAction->execute(
            $this->user(),
            $host,
            CustomCheckData::fromRequest($request)
        );

        return CustomCheckResource::make($customCheck);
    }

    public function update(
        UpdateCustomCheckRequest $request,
        Host $host,
        CustomCheck $customCheck,
        UpdateCustomCheckAction $updateCustomCheckAction
    ) {
        $this->authorize('update', [$customCheck, $host]);

        $customCheck = $updateCustomCheckAction->execute(
            $customCheck,
            CustomCheckData::fromRequest($request)
        );

        return CustomCheckResource::make($customCheck);
    }

    public function destroy(Host $host, CustomCheck $customCheck, DeleteCustomCheckAction $deleteCustomCheckAction)
    {
        $this->authorize('delete', [$customCheck, $host]);

        $deleteCustomCheckAction->execute($this->user(), $customCheck, $host);

        return $this->noContent();
    }
}
