<?php

namespace App\Http\Controllers\Support;

use App\Actions\Support\CreateSupportRequestAction;
use App\Actions\Support\UpdateSupportRequestAction;
use App\Data\Support\CreateSupportRequestData;
use App\Enums\SupportRequestState;
use App\Http\Controllers\Controller;
use App\Http\Requests\Support\CreateSupportRequest;
use App\Http\Requests\Support\UpdateSupportRequest;
use App\Http\Resources\SupportRequestResource;
use App\Models\SupportRequest;

class SupportRequestController extends Controller
{
    public function index()
    {
        return SupportRequestResource::collection(SupportRequest::jsonPaginate());
    }

    public function store(CreateSupportRequest $request, CreateSupportRequestAction $createSupport)
    {
        $supportMessage = $createSupport->execute(
            $this->user(),
            CreateSupportRequestData::fromRequest($request)
        );

        return SupportRequestResource::make($supportMessage);
    }

    public function show(SupportRequest $supportRequest)
    {
        return SupportRequestResource::make($supportRequest);
    }

    public function update(
        UpdateSupportRequest $request,
        SupportRequest $supportRequest,
        UpdateSupportRequestAction $updateSupportRequestAction
    ) {
        $requestUpdated = $updateSupportRequestAction->execute(
            $supportRequest,
            SupportRequestState::coerce($request->state)
        );

        return SupportRequestResource::make($requestUpdated);
    }
}
