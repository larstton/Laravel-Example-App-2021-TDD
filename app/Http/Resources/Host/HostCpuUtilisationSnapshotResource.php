<?php

namespace App\Http\Resources\Host;

use App\Http\Resources\JsonResource;
use App\Http\Transformers\DateTransformer;
use App\Models\CpuUtilisationSnapshot;

/**
 * @mixin CpuUtilisationSnapshot
 */
class HostCpuUtilisationSnapshotResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id'       => $this->id,
            'top'      => $this->top,
            'settings' => $this->settings,
            'dates'    => [
                'createdAt' => DateTransformer::transform($this->created_at),
            ],
        ];
    }
}
