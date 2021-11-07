<?php

namespace App\Events\JobmonResult;

use App\Models\JobmonResult;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class JobmonResultDeleted
{
    use Dispatchable, SerializesModels;

    /**
     * @var JobmonResult
     */
    public $jobmonResult;

    public function __construct(JobmonResult $jobmonResult)
    {
        $this->jobmonResult = $jobmonResult;
    }
}
