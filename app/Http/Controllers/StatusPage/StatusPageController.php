<?php

namespace App\Http\Controllers\StatusPage;

use App\Actions\StatusPage\CreateStatusPageAction;
use App\Actions\StatusPage\DeleteStatusPageAction;
use App\Actions\StatusPage\UpdateStatusPageAction;
use App\Data\StatusPage\StatusPageData;
use App\Http\Controllers\Controller;
use App\Http\Requests\StatusPage\StatusPageRequest;
use App\Http\Resources\StatusPageResource;
use App\Models\StatusPage;

class StatusPageController extends Controller
{
    public function index()
    {
        $this->authorize(StatusPage::class);

        return StatusPageResource::collection(StatusPage::latest()->jsonPaginate());
    }

    public function store(StatusPageRequest $request, CreateStatusPageAction $createStatusPageAction)
    {
        $this->authorize(StatusPage::class);

        $statusPage = $createStatusPageAction->execute(
            $this->user(),
            StatusPageData::fromRequest($request)
        );

        return StatusPageResource::make($statusPage);
    }

    public function show(StatusPage $statusPage)
    {
        $this->authorize($statusPage);

        return StatusPageResource::make($statusPage);
    }

    public function update(
        StatusPageRequest $request,
        StatusPage $statusPage,
        UpdateStatusPageAction $updateStatusPageAction
    ) {
        $this->authorize($statusPage);

        $statusPage = $updateStatusPageAction->execute(
            $statusPage,
            StatusPageData::fromRequest($request)
        );

        return StatusPageResource::make($statusPage);
    }

    public function destroy(StatusPage $statusPage, DeleteStatusPageAction $deleteStatusPageAction)
    {
        $this->authorize($statusPage);

        $deleteStatusPageAction->execute($statusPage);

        return $this->noContent();
    }
}
