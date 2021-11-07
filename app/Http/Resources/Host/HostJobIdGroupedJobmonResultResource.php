<?php

namespace App\Http\Resources\Host;

use App\Http\Resources\JsonResource;
use App\Http\Transformers\DateTransformer;
use App\Models\JobmonResult;
use Illuminate\Support\Carbon;

/**
 * @mixin JobmonResult
 */
class HostJobIdGroupedJobmonResultResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'jobId'      => $this->job_id,
            'numResults' => $this->result_count,
            'lastJob'    => [
                'id'       => $this->id,
                'exitCode' => $this->data['exit_code'],
                'status'   => $this->data['exit_code'] === 0 ? 'succeeded' : 'failed',
                'stderr'   => $this->data['stderr'],
                'stdout'   => $this->data['stdout'],
                'command'  => $this->data['command'],
                'severity' => $this->data['severity'],
                'dates'    => [
                    'jobStarted' => DateTransformer::transform(
                        Carbon::createFromTimestamp($this->data['job_started'])
                    ),
                    'jobEnded'   => DateTransformer::transform(
                        Carbon::createFromTimestamp($this->data['job_ended'])
                    ),
                ],
            ],
            'links'      => [
                'follow' => route('engine.host.jobmon-results.show', [
                    'host'  => $this->host_id,
                    'jobId' => $this->job_id,
                ]),
            ],
        ];
    }
}
