<?php

/** @noinspection PhpRedundantCatchClauseInspection */

namespace App\Http\Controllers\Host;

use App\Actions\WebCheck\CreateWebCheckAction;
use App\Actions\WebCheck\DeleteWebCheckAction;
use App\Actions\WebCheck\UpdateWebCheckAction;
use App\Data\WebCheck\WebCheckData;
use App\Exceptions\CheckPreflightException;
use App\Http\Controllers\Controller;
use App\Http\Queries\WebCheckQuery;
use App\Http\Requests\WebCheck\CreateWebCheckRequest;
use App\Http\Requests\WebCheck\UpdateWebCheckRequest;
use App\Http\Resources\WebCheckResource;
use App\Models\Host;
use App\Models\WebCheck;

class HostWebCheckController extends Controller
{
    public function index(Host $host)
    {
        $this->authorize('viewAny', [WebCheck::class, $host]);

        return WebCheckResource::collection(
            (new WebCheckQuery($host))->jsonPaginate()
        );
    }

    public function store(CreateWebCheckRequest $request, Host $host, CreateWebCheckAction $createWebCheckAction)
    {
        $this->authorize('create', [WebCheck::class, $host]);

        try {
            $webCheck = $createWebCheckAction->execute(
                $this->user(),
                $host,
                WebCheckData::fromRequest($request)
            );
        } catch (CheckPreflightException $e) {
            return $this->json([
                'data' => [
                    'error'   => $e->getMessage(),
                    'success' => false,
                    'console' => $e->getConsole(),
                ],
            ], 412);
        }

        return WebCheckResource::make($webCheck);
    }

    public function update(
        UpdateWebCheckRequest $request,
        Host $host,
        WebCheck $webCheck,
        UpdateWebCheckAction $updateWebCheckAction
    ) {
        $this->authorize('update', [$webCheck, $host]);

        try {
            $webCheck = $updateWebCheckAction->execute(
                $webCheck,
                $host,
                WebCheckData::fromRequest($request)
            );
        } catch (CheckPreflightException $e) {
            return $this->json([
                'data' => [
                    'error'   => $e->getMessage(),
                    'success' => false,
                    'console' => $e->getConsole(),
                ],
            ], 412);
        }

        return WebCheckResource::make($webCheck);
    }

    public function destroy(Host $host, WebCheck $webCheck, DeleteWebCheckAction $deleteWebCheckAction)
    {
        $this->authorize('delete', [$webCheck, $host]);

        $deleteWebCheckAction->execute($this->user(), $webCheck, $host);

        return $this->noContent();
    }
}
