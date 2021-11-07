<?php

namespace App\Http\Controllers\Recipient;

use App\Actions\Recipient\CreateRecipientAction;
use App\Actions\Recipient\DeleteRecipientAction;
use App\Actions\Recipient\UpdateRecipientAction;
use App\Data\Recipient\RecipientData;
use App\Http\Controllers\Controller;
use App\Http\Queries\RecipientQuery;
use App\Http\Requests\Recipient\CreateRecipientRequest;
use App\Http\Requests\Recipient\UpdateRecipientRequest;
use App\Http\Resources\RecipientResource;
use App\Models\Recipient;

class RecipientController extends Controller
{
    public function index(RecipientQuery $recipientQuery)
    {
        return RecipientResource::collection($recipientQuery->jsonPaginate());
    }

    public function store(CreateRecipientRequest $request, CreateRecipientAction $createRecipientAction)
    {
        $this->authorize(Recipient::class);

        $recipient = $createRecipientAction->execute(
            $this->user(),
            RecipientData::fromRequest($request)
        );

        return RecipientResource::make($recipient);
    }

    public function update(
        UpdateRecipientRequest $request,
        Recipient $recipient,
        UpdateRecipientAction $updateRecipientAction
    ) {
        $this->authorize($recipient);

        $updateRecipientAction->execute($recipient, RecipientData::fromRequest($request));

        return RecipientResource::make($recipient);
    }

    public function destroy(Recipient $recipient, DeleteRecipientAction $deleteRecipientAction)
    {
        $this->authorize($recipient);

        $deleteRecipientAction->execute($recipient);

        return $this->noContent();
    }
}
