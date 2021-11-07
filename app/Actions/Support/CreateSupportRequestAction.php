<?php

namespace App\Actions\Support;

use App\Data\Support\CreateSupportRequestData;
use App\Enums\SupportRequestState;
use App\Events\Support\SupportMessageCreated;
use App\Models\SupportRequest;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class CreateSupportRequestAction
{
    public function execute(User $user, CreateSupportRequestData $supportRequestData): SupportRequest
    {
        $attachments = collect($supportRequestData->attachment)
            ->mapWithKeys(
                fn ($attachment) => [Str::uuid()->toString() => $attachment->getClientOriginalName()]
            );

        $supportRequest = SupportRequest::create([
            'email'      => $user->email,
            'user_id'    => $user->id,
            'subject'    => strip_tags($supportRequestData->subject),
            'body'       => nl2br(strip_tags($supportRequestData->body)),
            'state'      => SupportRequestState::Open,
            'attachment' => $attachments->isEmpty() ? null : $attachments->all(),
        ]);

        if ($attachments->isNotEmpty()) {
            collect($supportRequestData->attachment)
                ->each(function (UploadedFile $attachment) use ($supportRequest) {
                    Storage::disk('support_attachments')->putFileAs(
                        $supportRequest->id,
                        $attachment,
                        $attachment->getClientOriginalName()
                    );
                });
        }

        SupportMessageCreated::dispatch($supportRequest);

        return $supportRequest;
    }
}
