<?php

namespace App\Http\Resources;

use App\Http\Transformers\DateTransformer;
use App\Models\SubUnit;

/**
 * @mixin SubUnit
 */
class SubUnitResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id'          => $this->id,
            'shortId'     => $this->short_id,
            'name'        => $this->name,
            'information' => $this->information,
            'dates'       => [
                'updatedAt' => DateTransformer::transform($this->updated_at),
                'createdAt' => DateTransformer::transform($this->created_at),
            ],
        ];
    }
}
