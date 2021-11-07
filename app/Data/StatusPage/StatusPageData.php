<?php

namespace App\Data\StatusPage;

use App\Data\BaseData;
use App\Http\Requests\StatusPage\StatusPageRequest;

class StatusPageData extends BaseData
{
    public string $title;
    public array $meta;

    public static function fromRequest(StatusPageRequest $request): self
    {
        return new self([
            'title' => $request->title,
            'meta'  => $request->meta,
        ]);
    }
}
