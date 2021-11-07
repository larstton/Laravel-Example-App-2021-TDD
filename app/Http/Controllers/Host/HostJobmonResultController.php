<?php

namespace App\Http\Controllers\Host;

use App\Actions\Jobmon\DeleteJobmonJobAction;
use App\Http\Controllers\Controller;
use App\Http\Queries\JobmonResultQuery;
use App\Http\Resources\Host\HostJobIdGroupedJobmonResultResource;
use App\Http\Resources\Host\HostJobmonResultResource;
use App\Models\Host;
use App\Models\JobmonResult;

class HostJobmonResultController extends Controller
{
    public function index(Host $host)
    {
        return HostJobIdGroupedJobmonResultResource::collection(
            $host->jobmonResultsGroupedByJobId()->jsonPaginate()
        );
    }

    public function show(Host $host, $jobId, JobmonResultQuery $jobmonResultQuery)
    {
        return HostJobmonResultResource::collection(
            $jobmonResultQuery->get()
        );
    }

    public function destroy(Host $host, string $jobId, DeleteJobmonJobAction $deleteJobmonJobAction)
    {
        $deleteJobmonJobAction->execute($host, $jobId);

        return $this->noContent();
    }
}
