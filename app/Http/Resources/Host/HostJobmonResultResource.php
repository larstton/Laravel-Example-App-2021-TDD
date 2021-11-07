<?php

namespace App\Http\Resources\Host;

use App\Http\Resources\JsonResource;
use App\Http\Transformers\DateTransformer;
use App\Models\JobmonResult;
use Illuminate\Support\Carbon;

/**
 * @mixin JobmonResult
 */
class HostJobmonResultResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id'          => $this->id,
            'hostId'      => $this->host_id,
            'jobId'       => $this->job_id,
            'jobDuration' => $this->data['job_duration_s'],
            'jobUser'     => $this->data['job_user'],
            'exitCode'    => $this->data['exit_code'],
            'severity'    => $this->data['severity'],
            'status'      => $this->data['exit_code'] === 0 ? 'succeeded' : 'failed',
            'stderr'      => $this->data['stderr'],
            'stdout'      => $this->data['stdout'],
            'command'     => $this->data['command'],
            'dates'       => [
                'jobStarted' => DateTransformer::transform(
                    Carbon::createFromTimestamp($this->data['job_started'])
                ),
                'jobEnded'   => DateTransformer::transform(
                    Carbon::createFromTimestamp($this->data['job_ended'])
                ),
                'createdAt'  => DateTransformer::transform($this->created_at),
            ],
        ];
    }
}
