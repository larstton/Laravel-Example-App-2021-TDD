<?php

namespace App\Http\Resources\Activity;

use App\Http\Requests\Activity\HostActivityRequest;
use App\Http\Resources\JsonResource;
use App\Http\Transformers\DateTransformer;
use App\Models\HostHistory;

/**
 * @mixin HostHistory
 */
class HostActivityResource extends JsonResource
{
    public function toArray($request)
    {
        $month = resolve(HostActivityRequest::class)->getMonthFilter();

        return [
            'name'      => $this->name,
            'paid'      => $this->paid,
            'hostId'    => $this->host_id,
            'userId'    => $this->user_id,
            'duration'  => $this->getPaidDurationForPeriod($month),
            'totalPaid' => $this->getTotalPaidForPeriod($month),
            'dates'     => [
                'durationStartAt' => DateTransformer::transform($this->getStartDateOfLog($month)),
                'durationEndAt'   => DateTransformer::transform($this->getEndDateOfLog($month)),
                'createdAt'       => DateTransformer::transform($this->created_at),
                'deletedAt'       => DateTransformer::transform($this->deleted_at),
            ],
        ];
    }
}
